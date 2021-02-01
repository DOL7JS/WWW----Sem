<?php
define("server_name","localhost");
define("username","root");
define("password","");
class connection
{
    static private $connection = NULL;
static function getConnection(){
    if(self::$connection == NULL){
        self::$connection = new mysqli(server_name, username,password);
    }
    return self::$connection;

}
}