<?php

declare(strict_types=1);
require dirname(__DIR__).'/vendor/autoload.php';
session_name(getenv('APP_SESSION_NAME') ?: 'example_session');
session_set_cookie_params(['httponly' => true,'secure' => (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off'),'samesite' => 'Lax','path' => '/']);
session_start();
