<?php

declare(strict_types=1);

namespace Luolongfei\Tests\Unit;

use Luolongfei\App\Console\Base;
use Luolongfei\App\Console\FreeNom;
use Luolongfei\App\Console\GlobalValue;
use Luolongfei\App\Console\MigrateEnvFile;
use Luolongfei\App\Console\Upgrade;
use Luolongfei\App\Exceptions\LlfException;
use Luolongfei\App\Exceptions\WarningException;
use Luolongfei\Tests\TestCase;

final class ConsoleModulesTest extends TestCase
{
    public function testConsoleBaseExtractsVersionNumber(): void
    {
        $base = new Base();

        self::assertSame('1.2.3', $base->getVerNum('v1.2.3'));
        self::assertNull($base->getVerNum('no-version'));
    }

    public function testGlobalValueSupportsCrudOperations(): void
    {
        $store = GlobalValue::getInstance();

        $store->set('foo', 'bar');

        self::assertTrue($store->has('foo'));
        self::assertSame('bar', $store->get('foo'));

        $store->del('foo');

        self::assertFalse($store->has('foo'));
        self::assertSame('fallback', $store->get('foo', 'fallback'));
    }

    public function testCustomExceptionsEmbedFormattedMessagesAndCodes(): void
    {
        $this->setEnvValues([
            'CUSTOM_LANGUAGE' => 'zh',
        ]);

        $llf = new LlfException(34520002, 'boom');
        $warning = new WarningException(34520014, ['demo@example.com', 'no domain']);

        self::assertStringContainsString('boom', $llf->getMessage());
        self::assertStringContainsString('Error code: 34520002', $llf->getMessage());
        self::assertStringContainsString('Warning code: 34520014', $warning->getMessage());
    }

    public function testFreeNomProtectedParsingHelpersWorkOnExpectedMarkup(): void
    {
        $freenom = $this->newInstanceWithoutConstructor(FreeNom::class);
        $this->setProperty($freenom, 'username', 'demo@example.com');

        $page = <<<'HTML'
<html>
<body>
<li>Logout</li>
<input type="hidden" name="token" value="token-123">
<table>
    <tr><td>alpha.tk</td><td>Active</td><td>expires <span class="status">10 days</span><a href="/renew?foo=1&domain=123">renew</a></td></tr>
    <tr><td>beta.ml</td><td>Active</td><td>expires <span class="status">20 days</span><a href="/renew?foo=1&domain=456">renew</a></td></tr>
</table>
</body>
</html>
HTML;

        $domains = $this->invokeMethod($freenom, 'getAllDomains', [$page]);

        self::assertCount(2, $domains);
        self::assertSame('alpha.tk', $domains[0]['domain']);
        self::assertSame('10', $domains[0]['days']);
        self::assertSame('123', $domains[0]['id']);
        self::assertSame('token-123', $this->invokeMethod($freenom, 'getToken', [$page]));
    }

    public function testFreeNomArrayUniqueKeepsDistinctCredentialPairs(): void
    {
        $freenom = $this->newInstanceWithoutConstructor(FreeNom::class);
        $accounts = [
            ['username' => 'ab', 'password' => 'c'],
            ['username' => 'a', 'password' => 'bc'],
            ['username' => 'ab', 'password' => 'c'],
        ];

        self::assertTrue($freenom->arrayUnique($accounts));
        self::assertCount(2, $accounts);
    }

    public function testUpgradeFormatsReleaseMessageWithoutHttpCalls(): void
    {
        $this->setEnvValues([
            'CUSTOM_LANGUAGE' => 'zh',
        ]);

        $upgrade = $this->newInstanceWithoutConstructor(Upgrade::class);
        $this->setProperty($upgrade, 'releaseInfo', [
            'published_at' => '2024-01-01T00:00:00Z',
            'body' => "Line 1\nLine 2",
            'html_url' => 'https://example.test/releases/v9.9.9',
        ]);
        $this->setProperty($upgrade, 'latestVer', '9.9.9');
        $this->setProperty($upgrade, 'currVer', '0.6.2');

        self::assertStringStartsWith('2024-01-01', $upgrade->friendlyDateFormat('2024-01-01T00:00:00Z', 'UTC'));
        self::assertStringContainsString('v9.9.9', $upgrade->genMsgContent());
        self::assertStringContainsString('https://example.test/releases/v9.9.9', $upgrade->genMsgContent());
    }

    public function testMigrateEnvFileEscapesStringValues(): void
    {
        $migrator = $this->newInstanceWithoutConstructor(MigrateEnvFile::class);

        self::assertSame("COUNT=1", $migrator->formatEnvVal('COUNT', 1));
        self::assertSame("SECRET='a\\'b\\\\c\\nnext'", $migrator->formatEnvVal('SECRET', "a'b\\c\nnext"));
    }
}
