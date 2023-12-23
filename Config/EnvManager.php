<?php

$env = __DIR__ . '/../.env';

if (!file_exists($env)) {
    die('oops ! .env file not found.');
}

$env_content = file($env, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

foreach($env_content as $line) {
    list($key, $value) = explode("=", $line, 2);
    $key = trim($key);
    $value = trim($value);
    putenv("$key=$value");
}