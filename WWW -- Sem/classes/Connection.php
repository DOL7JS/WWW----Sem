<?php
define("DB_HOST", "localhost");
define("DB_NAME", "db_dev");
define("DB_USER", "root");
define("DB_PASSWORD", "");
class connection
{
    static private $connection = NULL;
static function getConnection(){
    if (self::$connection == NULL) {
        $conn = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . "", DB_USER, DB_PASSWORD);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        self::$connection = $conn;
    }
    return self::$connection;

}
}