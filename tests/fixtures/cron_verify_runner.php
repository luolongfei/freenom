<?php
declare(strict_types=1);

require dirname(__DIR__) . '/bootstrap.php';

\Luolongfei\Libs\Env::getInstance()->init('tests/fixtures/test.env', true);
\Luolongfei\App\Console\Cron::getInstance()->verify();
