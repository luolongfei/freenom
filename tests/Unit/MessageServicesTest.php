<?php

declare(strict_types=1);

namespace Luolongfei\Tests\Unit;

use Luolongfei\Libs\Config;
use Luolongfei\Libs\Message;
use Luolongfei\Libs\MessageServices\Bark;
use Luolongfei\Libs\MessageServices\Mail;
use Luolongfei\Libs\MessageServices\Pushplus;
use Luolongfei\Libs\MessageServices\ServerChan;
use Luolongfei\Libs\MessageServices\TelegramBot;
use Luolongfei\Libs\MessageServices\WeChat;
use Luolongfei\Tests\Support\FakeHttpClient;
use Luolongfei\Tests\TestCase;

final class MessageServicesTest extends TestCase
{
    public function testMailReturnsExpectedProviderConfiguration(): void
    {
        $this->setEnvValues([
            'CUSTOM_LANGUAGE' => 'zh',
            'MAIL_USERNAME' => 'robot@outlook.com',
            'MAIL_PASSWORD' => 'secret',
            'TO' => 'owner@example.com',
        ]);

        $mail = new Mail();

        self::assertSame(['smtp.office365.com', \PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS, 587], $mail->getBasicMailConf('owner@outlook.com'));
        self::assertStringContainsString('alpha.tk', $mail->genDomainsHtml(['alpha.tk']));
        self::assertStringContainsString('还有', $mail->genDomainStatusHtml(['alpha.tk' => 10]));
    }

    public function testBarkSendBuildsExpectedRequest(): void
    {
        $this->setEnvValues([
            'CUSTOM_LANGUAGE' => 'zh',
            'BARK_KEY' => 'https://api.day.app/demo-key/test',
            'BARK_URL' => 'https://api.day.app',
            'BARK_GROUP' => 'FreeNom',
            'BARK_LEVEL' => 'active',
            'BARK_ENABLE' => '1',
        ]);

        $service = new Bark();
        $client = new FakeHttpClient();
        $client->queue('post', FakeHttpClient::jsonResponse(['code' => 200]));
        $this->setProperty($service, 'client', $client);

        self::assertTrue($service->send('hello', 'subject'));
        self::assertSame('https://api.day.app/demo-key/', $client->requests[0]['url']);
    }

    public function testServerChanSendFormatsMarkdownBody(): void
    {
        $this->setEnvValues([
            'CUSTOM_LANGUAGE' => 'zh',
            'SCT_SEND_KEY' => 'send-key',
        ]);

        $service = new ServerChan();
        $client = new FakeHttpClient();
        $client->queue('post', FakeHttpClient::jsonResponse(['code' => 0]));
        $this->setProperty($service, 'client', $client);

        self::assertTrue($service->send("line1\nline2", 'subject'));
        self::assertStringContainsString("\n\n", $client->requests[0]['options']['form_params']['desp']);
    }

    public function testPushplusSendUsesHttpsEndpoint(): void
    {
        $this->setEnvValues([
            'CUSTOM_LANGUAGE' => 'zh',
            'PUSHPLUS_KEY' => 'push-token',
        ]);

        $service = new Pushplus();
        $client = new FakeHttpClient();
        $client->queue('post', FakeHttpClient::jsonResponse(['code' => 200]));
        $this->setProperty($service, 'client', $client);

        self::assertTrue($service->send('content', 'subject'));
        self::assertSame(Pushplus::API_URL, $client->requests[0]['url']);
    }

    public function testTelegramBotEscapesMarkdownAndPreservesLinks(): void
    {
        $this->setEnvValues([
            'CUSTOM_LANGUAGE' => 'zh',
            'TELEGRAM_CHAT_ID' => '100',
            'TELEGRAM_BOT_TOKEN' => 'bot-token',
            'CUSTOM_TELEGRAM_HOST' => 'api.telegram.org',
        ]);

        $service = new TelegramBot();
        $client = new FakeHttpClient();
        $client->queue('post', FakeHttpClient::jsonResponse(['ok' => true]));
        $this->setProperty($service, 'client', $client);

        self::assertTrue($service->send("Release:\n* item\nraw \\ slash\n[example](http://example.com/path?a=1)", 'subject'));

        $request = $client->requests[0];
        self::assertSame('MarkdownV2', $request['options']['form_params']['parse_mode']);
        self::assertStringContainsString('\* item', $request['options']['form_params']['text']);
        self::assertStringContainsString('[example](http://example.com/path?a=1)', $request['options']['form_params']['text']);
    }

    public function testWeChatSendFetchesTokenAndPostsMessage(): void
    {
        $this->setEnvValues([
            'CUSTOM_LANGUAGE' => 'zh',
            'WECHAT_CORP_ID' => 'corp-id',
            'WECHAT_CORP_SECRET' => 'corp-secret',
            'WECHAT_AGENT_ID' => '1000001',
            'WECHAT_USER_ID' => '@all',
        ]);

        $service = new WeChat();
        $client = new FakeHttpClient();
        $client->queue('get', FakeHttpClient::jsonResponse([
            'errcode' => 0,
            'access_token' => 'access-token',
            'expires_in' => 7200,
        ]));
        $client->queue('post', FakeHttpClient::jsonResponse([
            'errcode' => 0,
            'errmsg' => 'ok',
        ]));
        $this->setProperty($service, 'client', $client);

        $tempFile = tempnam(sys_get_temp_dir(), 'wechat-token-');
        $this->setProperty($service, 'accessTokenFile', $tempFile);

        try {
            self::assertTrue($service->send('hello', 'subject'));
            self::assertCount(2, $client->requests);
            self::assertStringContainsString('WECHAT_ACCESS_TOKEN=access-token', (string) file_get_contents($tempFile));
        } finally {
            @unlink($tempFile);
        }
    }

    public function testMessageDispatcherFallsThroughAfterBrokenChannel(): void
    {
        $config = $this->newInstanceWithoutConstructor(Config::class);
        $this->setProperty($config, 'allConfig', [
            'message' => [
                [
                    'enable' => 1,
                    'not_enabled_tips' => false,
                    'class' => BrokenMessageService::class,
                    'name' => 'broken',
                ],
                [
                    'enable' => 1,
                    'not_enabled_tips' => false,
                    'class' => WorkingMessageService::class,
                    'name' => 'working',
                ],
            ],
            'custom_language' => 'zh',
        ]);
        $this->seedBaseInstance(Config::class, $config);

        ob_start();
        try {
            self::assertTrue(Message::send('payload'));
        } finally {
            ob_end_clean();
        }
        self::assertSame(1, WorkingMessageService::$sendCalls);
    }
}

final class BrokenMessageService implements \Luolongfei\Libs\Connector\MessageServiceInterface
{
    public function send(string $content, string $subject = '', int $type = 1, array $data = [], ?string $recipient = null, ...$params)
    {
        throw new \RuntimeException('boom');
    }
}

final class WorkingMessageService implements \Luolongfei\Libs\Connector\MessageServiceInterface
{
    public static int $sendCalls = 0;

    public function send(string $content, string $subject = '', int $type = 1, array $data = [], ?string $recipient = null, ...$params)
    {
        self::$sendCalls++;

        return true;
    }
}
