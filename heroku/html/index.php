<?php

set_time_limit(0);

echo '<pre>';
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