<?php
declare(strict_types=1);

namespace Luolongfei\Tests\App;

use Luolongfei\App\Exceptions\LlfException;
use Luolongfei\App\Exceptions\WarningException;
use Luolongfei\Tests\TestCase;

final class ExceptionTest extends TestCase
{
    public function testLlfExceptionFormatsMessageAndCode(): void
    {
        $exception = new LlfException(34520006, ['8.1', '7.1']);

        $this->assertStringContainsString('8.1', $exception->getMessage());
        $this->assertStringContainsString('7.1', $exception->getMessage());
        $this->assertStringContainsString('(Error code: 34520006)', $exception->getMessage());
    }

    public function testWarningExceptionFormatsMessageAndCode(): void
    {
        $exception = new WarningException(34520014, ['tester@example.com', 'No domains']);

        $this->assertStringContainsString('tester@example.com', $exception->getMessage());
        $this->assertStringContainsString('No domains', $exception->getMessage());
        $this->assertStringContainsString('(Warning code: 34520014)', $exception->getMessage());
    }
}
