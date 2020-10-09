<?php
// HTTP
define('HTTP_SERVER',  'http://' . getenv('HTTP_SERVER_HOST') . '/');

// HTTPS
define('HTTPS_SERVER',  'http://' . getenv('HTTP_SERVER_HOST') . '/');


// DIR
define('DIR_APPLICATION', '/var/www/upload/catalog/');
define('DIR_SYSTEM', '/var/www/upload/system/');
define('DIR_IMAGE', '/var/www/upload/image/');
define('DIR_STORAGE', '/var/www/storage/');
define('DIR_LANGUAGE', DIR_APPLICATION . 'language/');
define('DIR_TEMPLATE', DIR_APPLICATION . 'view/theme/');
define('DIR_CONFIG', DIR_SYSTEM . 'config/');
define('DIR_CACHE', DIR_STORAGE . 'cache/');
define('DIR_DOWNLOAD', DIR_STORAGE . 'download/');
define('DIR_LOGS', DIR_STORAGE . 'logs/');
define('DIR_MODIFICATION', DIR_STORAGE . 'modification/');
define('DIR_SESSION', DIR_STORAGE . 'session/');
define('DIR_UPLOAD', DIR_STORAGE . 'upload/');

// DB
define('DB_DRIVER', 'mpdo');
define('DB_HOSTNAME', getenv('DB_HOSTNAME'));
define('DB_USERNAME', getenv('DB_USERNAME'));
define('DB_PASSWORD', getenv('DB_PASSWORD'));
define('DB_DATABASE', getenv('DB_NAME'));
define('DB_PORT', getenv('DB_PORT'));
define('DB_PREFIX', 'oc_');