<?php

set_time_limit(0);

header('X-Accel-Buffering: no');

echo '<pre>';

$FF_TOKEN = $_GET['ff-token'] ?? '';
if ($FF_TOKEN !== getenv('FF_TOKEN')) {
    die('你没有权限触发执行');
}

echo "Freenom 自动续期\n\n";
echo "开始执行\n\n";

$cmd = 'php /app/run';

while (@ob_end_flush());

$proc = popen($cmd, 'r');

while (!feof($proc))
{
    echo fread($proc, 4096);
    @flush();
}

echo "\n\n执行完成";
echo '</pre>';