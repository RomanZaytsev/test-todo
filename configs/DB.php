<?php

namespace app\configs;

define('DB_HOST', 'mysql');
define('DB_USER', 'root');
define('DB_PASSWORD', 'root');
define('DB_NAME', 'todo-php');

class DB
{
    private static $instance;

    public function __construct()
    {
        if (self::$instance) {
            exit("Instance on DBConnection already exists.");
        } else {
            self::$instance = new \mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);

            if (self::$instance->connect_errno) {
                exit("Не удалось подключиться к MySQL: (" . self::$instance->connect_errno . ") " . self::$instance->connect_error);
            }
            self::$instance->set_charset("utf8");
            self::$instance->query("SET lc_time_names = 'ru_RU';");
        }
    }

    public static function connector()
    {
        if (!DB::$instance) {
            new DB();
        }
        return DB::$instance;
    }
}
