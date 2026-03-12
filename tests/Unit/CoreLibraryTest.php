<?php

declare(strict_types=1);

namespace Luolongfei\Tests\Unit;

use Colors\Color;
use GuzzleHttp\Cookie\SetCookie;
use Luolongfei\App\Constants\CommonConst;
use Luolongfei\Libs\Argv;
use Luolongfei\Libs\Config;
use Luolongfei\Libs\Connector\MessageGateway;
use Luolongfei\Libs\Env;
use Luolongfei\Libs\IP;
use Luolongfei\Libs\Lang;
use Luolongfei\Libs\Log;
use Luolongfei\Libs\PhpColor;
use Luolongfei\Tests\TestCase;
use Monolog\Logger;

final class CoreLibraryTest extends TestCase
{
    public function testBaseCachesInstancesAndRunsInitOnce(): void
    {
        $first = SingletonFixture::getInstance();
        $second = SingletonFixture::getInstance();

        self::assertSame($first, $second);
        self::assertSame(1, $first->initCalls);
    }

    public function testEnvCastsCommonValues(): void
    {
        $this->setEnvValues([
            'UNIT_BOOL_TRUE' => 'true',
            'UNIT_BOOL_FALSE' => '(false)',
            'UNIT_EMPTY' => '(empty)',
            'UNIT_QUOTED' => '"quoted value"',
        ]);

        self::assertTrue(env('UNIT_BOOL_TRUE'));
        self::assertFalse(env('UNIT_BOOL_FALSE'));
        self::assertSame('', env('UNIT_EMPTY'));
        self::assertSame('quoted value', env('UNIT_QUOTED'));
        self::assertInstanceOf(Env::class, Env::getInstance());
    }

    public function testConfigReadsNestedValuesFromEnvironment(): void
    {
        $this->setEnvValues([
            'CUSTOM_LANGUAGE' => 'zh',
            'CUSTOM_TELEGRAM_HOST' => 'telegram.example.test',
        ]);

        self::assertSame('telegram.example.test', config('message.telegram.host'));
        self::assertSame(1, config('notice_freq'));
        self::assertInstanceOf(Config::class, Config::getInstance());
    }

    public function testLangReturnsRootAndNestedMessages(): void
    {
        $this->setEnvValues([
            'CUSTOM_LANGUAGE' => 'zh',
        ]);

        self::assertSame('邮件', lang('100064'));
        self::assertStringContainsString('freenom 账户信息', (string) lang('exception_msg.34520001'));
        self::assertInstanceOf(Lang::class, Lang::getInstance());
    }

    public function testArgvParsesShortAndLongFlags(): void
    {
        global $argv;
        $argv = ['run', '-c=FreeNom', '--m=handle', '--flag'];

        $this->resetProjectState();

        self::assertSame('FreeNom', get_argv('c'));
        self::assertSame('handle', get_argv('m'));
        self::assertTrue(Argv::getInstance()->get('flag'));
    }

    public function testMessageGatewayHelpersWorkWithoutNetwork(): void
    {
        $this->setEnvValues([
            'SHOW_SERVER_INFO' => '0',
        ]);

        $gateway = new GatewayFixture();
        $footer = '';

        self::assertSame('Hello world', $gateway->genMessageContent(['world'], 'Hello %s'));
        self::assertSame("a<br>b", $gateway->newLine2Br("a\nb"));

        $gateway->setCommonFooter($footer);

        self::assertStringContainsString('NOTICE_FREQ', $footer);
    }

    public function testHelperFunctionsAndConstantsReturnExpectedValues(): void
    {
        $this->setEnvValues([
            'CUSTOM_LANGUAGE' => 'en',
        ]);

        $cookie = buildAwsWafCookie('token-123');

        self::assertSame('21st', get_local_num(21));
        self::assertSame(20, getSleepTime(1));
        self::assertSame(24, getSleepTime(6, 4, 20));
        self::assertInstanceOf(SetCookie::class, $cookie);
        self::assertSame('aws-waf-token', $cookie->getName());
        self::assertSame('token-123', $cookie->getValue());
        self::assertSame(CommonConst::AWS_WAF_TOKEN, 'AWS_WAF_TOKEN');
        self::assertMatchesRegularExpression('/^\d+\.\d+\.\d+\.\d+$/', get_random_user_agent());
        self::assertInstanceOf(Color::class, PhpColor::getInstance()->getColorInstance());
    }

    public function testIpParserAndLogFacadeWorkWithoutRemoteCalls(): void
    {
        $this->setEnvValues([
            'CUSTOM_LANGUAGE' => 'en',
        ]);

        $ip = $this->newInstanceWithoutConstructor(IP::class);
        $this->invokeMethod($ip, 'setIpInfo', ['{"ip":"1.1.1.1","country":"US","region":"CA","city":"SF"}']);

        self::assertSame('1.1.1.1', IP::$ip);
        self::assertSame('US CA SF', IP::$loc);
        self::assertInstanceOf(Logger::class, Log::logger());

        Log::info('unit-test');
        Log::error('unit-test');

        self::assertTrue(true);
    }
}

final class SingletonFixture extends \Luolongfei\Libs\Base
{
    public int $initCalls = 0;

    public function init(): void
    {
        $this->initCalls++;
    }
}

final class GatewayFixture extends MessageGateway
{
    public function send(string $content, string $subject = '', int $type = 1, array $data = [], ?string $recipient = null, ...$params)
    {
        return true;
    }
}
