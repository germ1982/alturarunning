<?php

declare(strict_types=1);

// El modo debug solo se activa cuando se navega desde la propia máquina.
$esLocal = in_array($_SERVER['REMOTE_ADDR'] ?? '', ['127.0.0.1', '::1'], true)
    && in_array(strtok($_SERVER['HTTP_HOST'] ?? '', ':'), ['localhost', '127.0.0.1'], true);

defined('YII_DEBUG') or define('YII_DEBUG', $esLocal);
defined('YII_ENV') or define('YII_ENV', $esLocal ? 'dev' : 'prod');

require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/../vendor/yiisoft/yii2/Yii.php';

$config = require __DIR__ . '/../config/web.php';

(new yii\web\Application($config))->run();
