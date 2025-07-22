<?php

use Symfony\Component\Dotenv\Dotenv;

require dirname(__DIR__).'/vendor/autoload.php';

$envFile = '.env';
if (getenv('APP_ENV') === 'test') {
    $envFile = '.env.test';
}

// if (method_exists(Dotenv::class, 'bootEnv')) {
//     (new Dotenv())->bootEnv(dirname(__DIR__).'/.env');
// }
// if ($_SERVER['APP_DEBUG']) {
//     umask(0000);
// }

if (method_exists(Dotenv::class, 'bootEnv')) {
    (new Dotenv())->bootEnv(dirname(__DIR__).'/'.$envFile);
}

if ($_SERVER['APP_DEBUG'] ?? false) {
    umask(0000);
}
