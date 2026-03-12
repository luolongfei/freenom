<?php
declare(strict_types=1);

namespace Luolongfei\Tests\Libs;

use Luolongfei\Libs\Connector\MessageGateway;
use Luolongfei\Libs\MessageServices\Bark;
use Luolongfei\Libs\MessageServices\Mail;
use Luolongfei\Libs\MessageServices\Pushplus;
use Luolongfei\Libs\MessageServices\ServerChan;
use Luolongfei\Libs\MessageServices\TelegramBot;
use Luolongfei\Libs\MessageServices\WeChat;
use Luolongfei\Tests\TestCase;
use PHPMailer\PHPMailer\PHPMailer;

final class FakeBody
{
    public function __construct(private readonly string $contents)
    {
    }

    public function getContents(): string
    {
        return $this->contents;
    }

    public function __toString(): string
    {
        return $this->contents;
    }
}

final class FakeResponse
{
    public function __construct(private readonly string $contents)
    {
    }

    public function getBody(): FakeBody
    {
        return new FakeBody($this->contents);
    }
}

final class RecordingClient
{
    public array $calls = [];

    public function __construct(private readonly string $responseBody)
    {
    }

    public function post(string $url, array $options): FakeResponse
    {
        $this->calls[] = ['method' => 'post', 'url' => $url, 'options' => $options];

        return new FakeResponse($this->responseBody);
    }
}

final class MessageServicesTest extends TestCase
{
    public function testMessageGatewayHelpersWork(): void
    {
        $gateway = new class extends MessageGateway {
            public function send(string $content, string $subject = '', int $type = 1, array $data = [], ?string $recipient = null, ...$params)
            {
                return true;
            }
        };

        $this->assertSame('Hello world', $gateway->genMessageContent(['world'], 'Hello %s'));
        $this->assertSame('a<br>b', $gateway->newLine2Br("a\nb"));

        $footer = '';
        $gateway->setCommonFooter($footer, "\n", false);
        $this->assertSame('', $footer);
    }

    public function testMailHelpersReturnExpectedValues(): void
    {
        $mail = $this->makeWithoutConstructor(Mail::class);

        [$host, $secure, $port] = $mail->getBasicMailConf('user@outlook.com');

        $this->assertSame('smtp.office365.com', $host);
        $this->assertSame(PHPMailer::ENCRYPTION_STARTTLS, $secure);
        $this->assertSame(587, $port);
        $this->assertStringContainsString('alpha.tk', $mail->genDomainStatusHtml(['alpha.tk' => 5]));
    }

    public function testBarkHelpersParseKeyAndFormatDomainStatus(): void
    {
        $bark = $this->makeWithoutConstructor(Bark::class);

        $this->assertSame('abc', $bark->parseBarkKey('https://api.day.app/abc/hello'));
        $this->assertStringContainsString('alpha.tk', $bark->genDomainStatusText(['alpha.tk' => 2]));
    }

    public function testPushplusSendUsesHttpsEndpoint(): void
    {
        $pushplus = $this->makeWithoutConstructor(Pushplus::class);
        $client = new RecordingClient('{"code":200}');

        $this->setProperty($pushplus, 'sendKey', 'push-token');
        $this->setProperty($pushplus, 'client', $client);

        $this->assertTrue($pushplus->send('Body', 'Subject'));
        $this->assertSame(Pushplus::API_URL, $client->calls[0]['url']);
        $this->assertSame('push-token', $client->calls[0]['options']['form_params']['token']);
    }

    public function testServerChanSendUsesExpectedEndpoint(): void
    {
        $serverChan = $this->makeWithoutConstructor(ServerChan::class);
        $client = new RecordingClient('{"code":0}');

        $this->setProperty($serverChan, 'sendKey', 'send-key');
        $this->setProperty($serverChan, 'client', $client);

        $this->assertTrue($serverChan->send('Body', 'Subject'));
        $this->assertStringContainsString('https://sctapi.ftqq.com/send-key.send', $client->calls[0]['url']);
    }

    public function testTelegramBotEscapesMarkdownV2AndPreservesLinks(): void
    {
        $telegram = $this->makeWithoutConstructor(TelegramBot::class);
        $client = new RecordingClient('{"ok":true}');

        $this->setProperty($telegram, 'chatID', '123456');
        $this->setProperty($telegram, 'token', 'bot-token');
        $this->setProperty($telegram, 'host', 'api.telegram.org');
        $this->setProperty($telegram, 'client', $client);

        $message = "Release notes:\n* item\nraw \\ slash\n[example](http://example.com/path?a=1)";
        $this->assertTrue($telegram->send($message, 'Subject'));

        $call = $client->calls[0];
        $text = $call['options']['form_params']['text'];

        $this->assertSame('MarkdownV2', $call['options']['form_params']['parse_mode']);
        $this->assertStringContainsString('Subject', $text);
        $this->assertStringContainsString('\* item', $text);
        $this->assertStringContainsString('raw \\\\ slash', $text);
        $this->assertStringContainsString('[example](http://example.com/path?a=1)', $text);
    }

    public function testTelegramBotPreservesProjectMarkdownFormatting(): void
    {
        $telegram = $this->makeWithoutConstructor(TelegramBot::class);
        $client = new RecordingClient('{"ok":true}');

        $this->setProperty($telegram, 'chatID', '123456');
        $this->setProperty($telegram, 'token', 'bot-token');
        $this->setProperty($telegram, 'host', 'api.telegram.org');
        $this->setProperty($telegram, 'client', $client);

        $this->assertTrue($telegram->send('', '', 3, [
            'username' => 'tester@example.com',
            'domainStatusArr' => ['alpha.tk' => 5],
        ]));

        $text = $client->calls[0]['options']['form_params']['text'];

        $this->assertStringContainsString('[alpha.tk](http://alpha.tk)', $text);
        $this->assertStringContainsString('*5*', $text);
        $this->assertStringNotContainsString('\*5\*', $text);
    }

    public function testTelegramHelperMethodsParseHostAndTables(): void
    {
        $telegram = new TelegramBot();

        $this->assertSame('api.telegram.org', $this->invokeMethod($telegram, 'getTelegramHost'));
        $rows = $telegram->getMarkDownRawArr("| A | B |\n| 1 | 2 |");
        $this->assertSame([['A', 'B'], ['1', '2']], $rows);
    }

    public function testWeChatCacheReaderAndFormattingHelpersWork(): void
    {
        $weChat = $this->makeWithoutConstructor(WeChat::class);
        $cacheFile = ROOT_PATH . DS . 'tests' . DS . 'runtime' . DS . 'wechat_access_token.txt';
        file_put_contents($cacheFile, sprintf("WECHAT_ACCESS_TOKEN=token\nWECHAT_ACCESS_TOKEN_EXPIRES_AT=%s\n", time() + 300));

        $this->setProperty($weChat, 'accessTokenFile', $cacheFile);

        $this->assertSame('token', $this->invokeMethod($weChat, 'getAccessTokenCache'));
        $this->assertStringContainsString('alpha.tk', $weChat->genDomainStatusFullText('tester', ['alpha.tk' => 3]));
    }
}
