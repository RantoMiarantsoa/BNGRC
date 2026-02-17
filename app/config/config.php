<?php

define('DB_HOST', '127.0.0.1');
define('DB_NAME', 'bngrc');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_CHARSET', 'utf8');
define('DB_DRIVER', 'mysql');

// define('DB_HOST', '172.16.7.131');
// define('DB_NAME', 'db_s2_ETU004304');
// define('DB_USER', 'ETU004304');
// define('DB_PASS', 'g2HhvSfn');
// define('DB_CHARSET', 'utf8');
// define('DB_DRIVER', 'mysql');

// Base URL
// En local : auto-detect
define('BASE_URL', rtrim(dirname($_SERVER['SCRIPT_NAME']), '/') . '/');
// En deploiement :
// define('BASE_URL', '/ETU004304/BNGRC/');
