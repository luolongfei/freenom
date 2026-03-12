<?php
declare(strict_types=1);

namespace Luolongfei\Tests\Libs;

use GuzzleHttp\Cookie\SetCookie;
use Luolongfei\Tests\TestCase;

final class HelpersTest extends TestCase
{
    public function testLocalizedNumberFormattingAndRandomUserAgent(): void
    {
        $this->loadFixtureEnv(['CUSTOM_LANGUAGE' => 'en']);
        $this->assertSame('1st', get_local_num(1));
        $this->assertSame('4th', get_local_num(4));

        $this->loadFixtureEnv(['CUSTOM_LANGUAGE' => 'zh']);
        $this->assertSame('2', get_local_num(2));
        $this->assertSame('121.0.0.0', get_random_user_agent());
    }

    public function testSleepTimeAndAwsWafCookieHelpers(): void
    {
        $this->assertSame(20, getSleepTime(1));
        $this->assertSame(24, getSleepTime(6, 4, 20));

        $cookie = buildAwsWafCookie('token-value');

        $this->assertInstanceOf(SetCookie::class, $cookie);
        $this->assertSame('aws-waf-token', $cookie->getName());
        $this->assertSame('token-value', $cookie->getValue());
        $this->assertSame('.my.freenom.com', $cookie->getDomain());
    }

    public function testGlobalValueAndTaskLockHelpers(): void
    {
        setGlobalValue('foo', 'bar');
        $this->assertTrue(hasGlobalValue('foo'));
        $this->assertSame('bar', getGlobalValue('foo'));
        delGlobalValue('foo');
        $this->assertFalse(hasGlobalValue('foo'));

        $taskName = 'phpunit_' . uniqid('', true);
        $lockFile = APP_PATH . DS . 'num_limit' . DS . date('Y-m-d') . DS . $taskName . '.lock';

        $this->assertFalse(is_locked($taskName));
        $this->assertTrue(lock_task($taskName));
        $this->assertTrue(is_locked($taskName));

        if (file_exists($lockFile)) {
            unlink($lockFile);
        }
    }
}
