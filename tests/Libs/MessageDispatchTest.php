<?php
declare(strict_types=1);

namespace Luolongfei\Tests\Libs;

use Luolongfei\Libs\Config;
use Luolongfei\Libs\Connector\MessageServiceInterface;
use Luolongfei\Libs\Message;
use Luolongfei\Tests\TestCase;

class DummyMessageSuccess implements MessageServiceInterface
{
    public static int $calls = 0;

    public function send(string $content, string $subject = '', int $type = 1, array $data = [], ?string $recipient = null, ...$params)
    {
        self::$calls++;

        return true;
    }
}

class DummyMessageFailure implements MessageServiceInterface
{
    public static int $calls = 0;

    public function send(string $content, string $subject = '', int $type = 1, array $data = [], ?string $recipient = null, ...$params)
    {
        self::$calls++;

        return false;
    }
}

final class MessageDispatchTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        DummyMessageSuccess::$calls = 0;
        DummyMessageFailure::$calls = 0;
    }

    public function testMessageDispatchReturnsTrueWhenAnyChannelSucceeds(): void
    {
        $config = Config::getInstance();
        $this->setProperty($config, 'allConfig', [
            'message' => [
                [
                    'enable' => 1,
                    'not_enabled_tips' => false,
                    'class' => DummyMessageFailure::class,
                    'name' => 'failure',
                ],
                [
                    'enable' => 1,
                    'not_enabled_tips' => false,
                    'class' => DummyMessageSuccess::class,
                    'name' => 'success',
                ],
            ],
        ]);

        $this->assertTrue(Message::send('hello world'));
        $this->assertSame(1, DummyMessageFailure::$calls);
        $this->assertSame(1, DummyMessageSuccess::$calls);
    }

    public function testMessageDispatchReturnsFalseWhenNoChannelSucceeds(): void
    {
        $config = Config::getInstance();
        $this->setProperty($config, 'allConfig', [
            'message' => [
                [
                    'enable' => 1,
                    'not_enabled_tips' => false,
                    'class' => DummyMessageFailure::class,
                    'name' => 'failure',
                ],
            ],
        ]);

        $this->assertFalse(Message::send('hello world'));
        $this->assertSame(1, DummyMessageFailure::$calls);
    }
}
