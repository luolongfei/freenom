<?php
declare(strict_types=1);

namespace Luolongfei\Tests\Libs;

use Colors\Color;
use Luolongfei\Libs\Argv;
use Luolongfei\Libs\Base;
use Luolongfei\Libs\Config;
use Luolongfei\Libs\Env;
use Luolongfei\Libs\IP;
use Luolongfei\Libs\Lang;
use Luolongfei\Libs\Log;
use Luolongfei\Libs\PhpColor;
use Luolongfei\Tests\TestCase;
use Monolog\Logger;

class DummySingleton extends Base
{
    public array $params = [];

    public function init(...$params)
    {
        $this->params = $params;
    }
}

class TestableIP extends IP
{
    public function __construct()
    {
    }

    public function parse(string $body): bool
    {
        return $this->setIpInfo($body);
    }
}

final class CoreLibsTest extends TestCase
{
    public function testBaseSingletonInitializesOnce(): void
    {
        $first = DummySingleton::getInstance('foo', 'bar');
        $second = DummySingleton::getInstance('baz');

        $this->assertSame($first, $second);
        $this->assertSame(['foo', 'bar'], $first->params);
    }

    public function testEnvConfigAndLangReadFixtureValues(): void
    {
        $this->loadFixtureEnv([
            'FEATURE_FLAG' => 'true',
            'EMPTY_VAL' => '(empty)',
            'NULL_VAL' => '(null)',
            'QUOTED_VAL' => '"quoted"',
        ]);

        $this->assertTrue(Env::getInstance()->get('FEATURE_FLAG'));
        $this->assertSame('', Env::getInstance()->get('EMPTY_VAL'));
        $this->assertNull(Env::getInstance()->get('NULL_VAL'));
        $this->assertSame('quoted', Env::getInstance()->get('QUOTED_VAL'));

        $this->assertSame('api.telegram.org', Config::getInstance()->get('message.telegram.host'));
        $this->assertSame('fallback', Config::getInstance()->get('missing.value', 'fallback'));
        $this->assertNotNull(Lang::getInstance()->get('100064'));
        $this->assertSame(lang('100064'), Lang::getInstance()->get('100064'));
    }

    public function testArgvParsesNamedArguments(): void
    {
        global $argv;
        $argv = ['run', '--foo=bar', '-num=3', '--flag'];

        $arg = $this->makeWithoutConstructor(Argv::class);
        $parsed = $arg->parseAllArgs();

        $this->assertSame('bar', $parsed['foo']);
        $this->assertSame('3', $parsed['num']);
        $this->assertTrue($parsed['flag']);
        $this->assertSame('bar', $arg->get('foo'));
    }

    public function testIpParsingSupportsChineseAndEnglishSources(): void
    {
        $ip = new TestableIP();

        $this->loadFixtureEnv(['CUSTOM_LANGUAGE' => 'zh']);
        $ip->parse("当前 IP：1.2.3.4 来自于：中国 广东 深圳 ");
        $this->assertSame('1.2.3.4', IP::$ip);
        $this->assertSame('中国 广东 深圳', IP::$loc);

        $this->loadFixtureEnv(['CUSTOM_LANGUAGE' => 'en']);
        $ip->parse('{"ip":"5.6.7.8","country":"US","region":"CA","city":"San Francisco"}');
        $this->assertSame('5.6.7.8', IP::$ip);
        $this->assertSame('US CA San Francisco', IP::$loc);
    }

    public function testLogAndPhpColorExposeUsableInstances(): void
    {
        $this->assertTrue(Log::info('info message'));
        $this->assertTrue(Log::error('error message'));
        $this->assertInstanceOf(Logger::class, Log::logger());
        $this->assertInstanceOf(Color::class, PhpColor::getInstance()->getColorInstance());
    }
}
