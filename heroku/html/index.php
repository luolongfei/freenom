<?php

set_time_limit(0);

header('X-Accel-Buffering: no');

echo '<pre>';

echo "Freenom 自动续期\n\n";

echo "开始执行\n\n";

passthru('php /app/run');

echo "\n\n执行完成";

echo '</pre>';