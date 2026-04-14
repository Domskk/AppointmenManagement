<?php
define('DB_HOST', $_ENV['DB_HOST']);
define('DB_NAME', $_ENV['DB_NAME']);
define('DB_USER', $_ENV['DB_USER']);
define('DB_PASS', $_ENV['DB_PASS']);
define('JWT_SECRET', $_ENV['JWT_SECRET']);
define('JWT_EXPIRY', (int) $_ENV['JWT_EXPIRY']);
define('ENC_KEY', hex2bin($_ENV['ENC_KEY']));
define('APP_NAME', $_ENV['APP_NAME']);
date_default_timezone_set($_ENV['APP_TIMEZONE']);