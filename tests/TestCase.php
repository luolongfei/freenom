<?php
declare(strict_types=1);

namespace Luolongfei\Tests;

use PHPUnit\Framework\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    protected const FIXTURE_ENV_FILE = 'tests/fixtures/test.env';

    protected const ENV_KEYS = [
        'ENV_FILE_VERSION',
        'FREENOM_USERNAME',
        'FREENOM_PASSWORD',
        'MULTIPLE_ACCOUNTS',
        'FREENOM_PROXY',
        'MAIL_USERNAME',
        'MAIL_PASSWORD',
        'TO',
        'MAIL_ENABLE',
        'MAIL_HOST',
        'MAIL_PORT',
        'MAIL_ENCRYPTION',
        'TELEGRAM_CHAT_ID',
        'TELEGRAM_BOT_TOKEN',
        'TELEGRAM_PROXY',
        'CUSTOM_TELEGRAM_HOST',
        'TELEGRAM_BOT_ENABLE',
        'WECHAT_CORP_ID',
        'WECHAT_CORP_SECRET',
        'WECHAT_AGENT_ID',
        'WECHAT_USER_ID',
        'WECHAT_ENABLE',
        'SCT_SEND_KEY',
        'SCT_ENABLE',
        'BARK_KEY',
        'BARK_URL',
        'BARK_IS_ARCHIVE',
        'BARK_GROUP',
        'BARK_LEVEL',
        'BARK_ICON',
        'BARK_JUMP_URL',
        'BARK_SOUND',
        'BARK_ENABLE',
        'PUSHPLUS_KEY',
        'PUSHPLUS_ENABLE',
        'NOTICE_FREQ',
        'VERIFY_SSL',
        'DEBUG',
        'NEW_VERSION_DETECTION',
        'CUSTOM_LANGUAGE',
        'SHOW_SERVER_INFO',
        'MOSAIC_SENSITIVE_INFO',
        'MAX_REQUEST_RETRY_COUNT',
        'USE_OPEN_SOURCE_WAF_SOLVER_API',
        'OPEN_SOURCE_WAF_SOLVER_URL',
        'FF_SECRET_KEY',
        'AWS_WAF_SOLVER_URL',
    ];

    protected function setUp(): void
    {
        parent::setUp();

        $this->resetStaticState();
        $this->loadFixtureEnv();
    }

    protected function tearDown(): void
    {
        $this->resetStaticState();

        parent::tearDown();
    }

    protected function loadFixtureEnv(array $overrides = []): void
    {
        foreach (self::ENV_KEYS as $key) {
            putenv($key);
            unset($_ENV[$key], $_SERVER[$key]);
        }

        $runtimeFile = ROOT_PATH . DS . 'tests' . DS . 'runtime' . DS . 'phpunit.env';
        $contents = (string) file_get_contents(ROOT_PATH . DS . self::FIXTURE_ENV_FILE);

        foreach ($overrides as $key => $value) {
            $contents .= sprintf("\n%s=%s", $key, $this->formatEnvValue($value));
        }

        file_put_contents($runtimeFile, trim($contents) . "\n");

        $this->resetStaticState();
        \Luolongfei\Libs\Env::getInstance()->init('tests/runtime/phpunit.env', true);
    }

    protected function setEnvValues(array $values): void
    {
        $this->loadFixtureEnv($values);
    }

    protected function makeWithoutConstructor(string $class): object
    {
        return (new \ReflectionClass($class))->newInstanceWithoutConstructor();
    }

    protected function newInstanceWithoutConstructor(string $class): object
    {
        return $this->makeWithoutConstructor($class);
    }

    protected function invokeMethod(object $object, string $method, array $args = [])
    {
        $reflection = new \ReflectionMethod($object, $method);

        return $reflection->invokeArgs($object, $args);
    }

    protected function setProperty(object|string $target, string $property, mixed $value): void
    {
        $reflection = new \ReflectionProperty($target, $property);
        $reflection->setValue(is_object($target) ? $target : null, $value);
    }

    protected function getProperty(object|string $target, string $property): mixed
    {
        $reflection = new \ReflectionProperty($target, $property);

        return $reflection->getValue(is_object($target) ? $target : null);
    }

    protected function createTempDir(string $name): string
    {
        $dir = ROOT_PATH . DS . 'tests' . DS . 'runtime' . DS . $name;
        if (is_dir($dir)) {
            $this->deleteDir($dir);
        }
        mkdir($dir, 0777, true);

        return $dir;
    }

    protected function resetProjectState(): void
    {
        $this->resetStaticState();
    }

    protected function seedBaseInstance(string $class, object $instance): void
    {
        $instances = $this->getProperty(\Luolongfei\Libs\Base::class, 'instances');
        $instances[$class] = $instance;
        $this->setProperty(\Luolongfei\Libs\Base::class, 'instances', $instances);
    }

    private function resetStaticState(): void
    {
        $this->setProperty(\Luolongfei\Libs\Base::class, 'instances', []);
        $this->setProperty(\Luolongfei\Libs\Log::class, 'loggerInstance', null);
        $this->setProperty(\Luolongfei\Libs\IP::class, 'ip', '');
        $this->setProperty(\Luolongfei\Libs\IP::class, 'loc', '');
        $this->setProperty(\Luolongfei\Libs\Message::class, 'notEnabledTips', true);

        foreach ([
            \Luolongfei\App\Console\Cron::class,
            \Luolongfei\App\Console\FreeNom::class,
            \Luolongfei\App\Console\GlobalValue::class,
            \Luolongfei\App\Console\MigrateEnvFile::class,
            \Luolongfei\App\Console\Upgrade::class,
        ] as $class) {
            $this->setProperty($class, 'instance', null);
        }
    }

    private function formatEnvValue(mixed $value): string
    {
        if (is_bool($value)) {
            return $value ? '1' : '0';
        }

        if ($value === null) {
            return "''";
        }

        if (is_numeric($value)) {
            return (string) $value;
        }

        return "'" . str_replace(['\\', "'"], ['\\\\', "\\'"], (string) $value) . "'";
    }

    private function deleteDir(string $dir): void
    {
        if (!is_dir($dir)) {
            return;
        }

        $items = scandir($dir) ?: [];
        foreach ($items as $item) {
            if ($item === '.' || $item === '..') {
                continue;
            }

            $path = $dir . DS . $item;
            if (is_dir($path)) {
                $this->deleteDir($path);

                continue;
            }

            unlink($path);
        }

        rmdir($dir);
    }
}
