<?php
declare(strict_types=1);

namespace Luolongfei\Tests\App;

use GuzzleHttp\Cookie\CookieJar;
use Luolongfei\App\Console\Base;
use Luolongfei\App\Console\Cron;
use Luolongfei\App\Console\FreeNom;
use Luolongfei\App\Console\GlobalValue;
use Luolongfei\App\Console\MigrateEnvFile;
use Luolongfei\App\Console\Upgrade;
use Luolongfei\App\Exceptions\LlfException;
use Luolongfei\Tests\TestCase;

class TestableFreeNom extends FreeNom
{
    public array $renewResults = [];

    public function __construct()
    {
    }

    protected function renew(int $id, string $token)
    {
        return $this->renewResults[$id] ?? false;
    }
}

final class ConsoleTest extends TestCase
{
    public function testConsoleBaseExtractsVersionNumber(): void
    {
        $base = new Base();

        $this->assertSame('1.2.3', $base->getVerNum('v1.2.3-beta'));
        $this->assertNull($base->getVerNum('invalid'));
    }

    public function testCronVerifyAcceptsAndRejectsExpressions(): void
    {
        $script = ROOT_PATH . DS . 'tests' . DS . 'fixtures' . DS . 'cron_verify_runner.php';

        exec(sprintf('php -d xdebug.mode=off %s --cron_exp=%s', escapeshellarg($script), escapeshellarg('* * * * *')), $output, $validCode);
        exec(sprintf('php -d xdebug.mode=off %s --cron_exp=%s', escapeshellarg($script), escapeshellarg('bad cron')), $output, $invalidCode);

        $this->assertSame(0, $validCode);
        $this->assertSame(1, $invalidCode);
    }

    public function testGlobalValueStoresAndRemovesEntries(): void
    {
        $globalValue = GlobalValue::getInstance();

        $globalValue->set('foo', 'bar');

        $this->assertTrue($globalValue->has('foo'));
        $this->assertSame('bar', $globalValue->get('foo'));

        $globalValue->del('foo');

        $this->assertFalse($globalValue->has('foo'));
        $this->assertNull($globalValue->get('foo'));
    }

    public function testMigrateEnvFileReadsVersionsAndUpdatesValues(): void
    {
        $dir = $this->createTempDir('migrator');
        file_put_contents($dir . DS . '.env', "ENV_FILE_VERSION='v1.0'\nFOO='old'\n");
        file_put_contents($dir . DS . '.env.example', "ENV_FILE_VERSION='v2.0'\nFOO='new'\n");

        $migrator = new class extends MigrateEnvFile {
            public string $baseDir = '';

            public function __construct()
            {
            }

            public function getEnvFilePath($filename = '.env')
            {
                return $this->baseDir . DS . $filename;
            }
        };
        $migrator->baseDir = $dir;

        $this->assertSame('1.0', $migrator->getEnvFileVer());
        $this->assertSame('2.0', $migrator->getEnvFileVer('.env.example'));
        $this->assertTrue($migrator->needToMigrate());
        $this->assertSame("FOO='value'", $migrator->formatEnvVal('FOO', 'value'));

        $this->assertTrue($migrator->setEnv('FOO', 'changed'));
        $this->assertStringContainsString("FOO='changed'", (string) file_get_contents($dir . DS . '.env'));
    }

    public function testUpgradeHelpersWorkWithInjectedReleaseInfo(): void
    {
        $dir = $this->createTempDir('upgrade');
        $upgrade = new class extends Upgrade {
            public function __construct()
            {
            }
        };

        $this->setProperty($upgrade, 'pushedVerFile', $dir . DS . 'pushed_version.txt');
        $this->setProperty($upgrade, 'releaseInfo', [
            'published_at' => '2026-03-10T00:00:00Z',
            'body' => "Bug fixes\n",
            'html_url' => 'https://github.com/luolongfei/freenom/releases/tag/v9.9.9',
        ]);
        $this->setProperty($upgrade, 'latestVer', '9.9.9');
        $this->setProperty($upgrade, 'currVer', '0.6.2');

        $this->assertFalse($upgrade->isPushed('9.9.9'));
        $this->assertTrue($upgrade->rememberVer('9.9.9'));
        $this->assertTrue($upgrade->isPushed('9.9.9'));
        $this->assertStringContainsString('9.9.9', $upgrade->genMsgContent());
        $this->assertStringContainsString('2026-03-10', $upgrade->friendlyDateFormat('2026-03-10T00:00:00Z', 'UTC'));
    }

    public function testUpgradeDeclaresHttpClientProperty(): void
    {
        $this->assertTrue((new \ReflectionClass(Upgrade::class))->hasProperty('client'));
    }

    public function testFreeNomParsesAccountsDomainsAndToken(): void
    {
        $this->loadFixtureEnv([
            'MULTIPLE_ACCOUNTS' => '<first@example.com>@<first-pass>|<second@example.com>@<second-pass>',
        ]);

        $freeNom = new TestableFreeNom();

        $accounts = $this->invokeMethod($freeNom, 'getAccounts');
        $this->assertCount(3, $accounts);

        $page = <<<'HTML'
<input type="hidden" name="token" value="abc123" />
<table>
<tr><td>alpha.tk</td><td>Active</td><td>Expires <span class="text-danger">5 days left</span></td><td><a href="domains.php?a=renewdomain&domain=1">Renew</a></td></tr>
<tr><td>beta.ml</td><td>Active</td><td>Expires <span class="text-success">20 days left</span></td><td><a href="domains.php?a=renewdomain&domain=2">Renew</a></td></tr>
</table>
HTML;

        $domains = $this->invokeMethod($freeNom, 'getAllDomains', [$page]);
        $this->assertCount(2, $domains);
        $this->assertSame('abc123', $this->invokeMethod($freeNom, 'getToken', [$page]));
    }

    public function testFreeNomLoginFailsGracefullyWhenSessionCookieMissing(): void
    {
        $freeNom = new TestableFreeNom();
        $client = new class {
            public function post(string $url, array $options)
            {
                return null;
            }
        };

        $this->setProperty($freeNom, 'client', $client);
        $this->setProperty($freeNom, 'jar', new CookieJar());
        $this->setProperty($freeNom, 'maxRequestRetryCount', 0);

        $this->expectException(LlfException::class);
        $this->expectExceptionMessage('(Error code: 34520002)');

        $this->invokeMethod($freeNom, 'login', ['tester@example.com', 'secret']);
    }

    public function testFreeNomArrayUniqueAndRenewAllDomains(): void
    {
        $freeNom = new TestableFreeNom();
        $freeNom->renewResults = [1 => true, 2 => false];
        $this->setProperty($freeNom, 'username', 'tester@example.com');

        $accounts = [
            ['username' => 'a', 'password' => '1'],
            ['password' => '1', 'username' => 'a'],
            ['username' => 'b', 'password' => '2'],
        ];

        $this->assertTrue($freeNom->arrayUnique($accounts, ['username', 'password']));
        $this->assertCount(2, $accounts);

        $domains = [
            ['domain' => 'alpha.tk', 'days' => '5', 'id' => 1],
            ['domain' => 'beta.ml', 'days' => '10', 'id' => 2],
            ['domain' => 'gamma.ga', 'days' => '30', 'id' => 3],
        ];

        $this->assertTrue($freeNom->renewAllDomains($domains, 'token'));
    }
}
