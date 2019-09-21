<?php

use Symfony\Component\Dotenv\Dotenv;

require_once __DIR__ . '/../vendor/autoload.php';
$dotEnv = new Dotenv(true);
$dotEnv->load(__DIR__ . '/../.env');
if (in_array(getenv('APP_ENV'), ['dev', 'test'])) {
    (new \Symfony\Component\Dotenv\Dotenv(true))->load(__DIR__ . '/../.env');
}
$_ENV['DATABASE_URL'] = getenv('DATABASE_URL_TEST');