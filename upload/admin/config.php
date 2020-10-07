<?php
// HTTP
define('HTTP_SERVER', 'http://localhost/admin/');
define('HTTP_CATALOG', 'http://localhost/');

// HTTPS
define('HTTPS_SERVER', 'http://localhost/admin/');
define('HTTPS_CATALOG', 'http://localhost/');

// DIR
define('DIR_APPLICATION', '/var/www/upload/admin/');
define('DIR_SYSTEM', '/var/www/upload/system/');
define('DIR_IMAGE', '/var/www/upload/image/');
define('DIR_STORAGE', '/var/www/public_html/storage/');
define('DIR_CATALOG', '/var/www/upload/catalog/');
define('DIR_LANGUAGE', DIR_APPLICATION . 'language/');
define('DIR_TEMPLATE', DIR_APPLICATION . 'view/template/');
define('DIR_CONFIG', DIR_SYSTEM . 'config/');
define('DIR_CACHE', DIR_STORAGE . 'cache/');
define('DIR_DOWNLOAD', DIR_STORAGE . 'download/');
define('DIR_LOGS', DIR_STORAGE . 'logs/');
define('DIR_MODIFICATION', DIR_STORAGE . 'modification/');
define('DIR_SESSION', DIR_STORAGE . 'session/');
define('DIR_UPLOAD', DIR_STORAGE . 'upload/');

// DB
define('DB_DRIVER', 'mpdo');
define('DB_HOSTNAME', 'mysql');
define('DB_USERNAME', 'opencart');
define('DB_PASSWORD', 'password');
define('DB_DATABASE', 'opencart');
define('DB_PORT', '3306');
define('DB_PREFIX', 'oc_');

// OpenCart API
define('OPENCART_SERVER', 'https://www.opencart.com/');
