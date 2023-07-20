<?php

namespace app\models;

use app\configs\DB;
use app\helpers\Utilities;
use app\helpers\PasswordHasher;

class Session
{
    public function login($params)
    {
        if (empty($params['login'])) {
            $result['status'] = "Все поля обязательны для заполнения";
            $result['http_response_code'] = "200";
            return $result;
        }
        $mysqli = DB::connector();
        $query = "SELECT * FROM user WHERE name='" . $mysqli->real_escape_string($params['login']) . "' OR name='" . $mysqli->real_escape_string($params['login']) . "'";
        $queryResult = $mysqli->query($query);
        $row = $queryResult->fetch_object();
        $queryResult->close();
        if (!$row) {
            $result['status'] = "Неверный логин или пароль";
            $result['http_response_code'] = "200";
            return $result;
        }
        // Проверяем пароль
        if (!(new User())->verifyPassword($params['password'], $row->hash)) {
            $result['status'] = "Неверный пароль";
            $result['http_response_code'] = "401";
            return $result;
        }

        $access_keys = "";
        $access_values = "";
        foreach ($row as $key => $value) {
            if (strpos($key, "access_") !== false) {
                $access_keys .= "," . $key;
                $access_values .= "," . $value;
            }
        }
        $hashSession = sha1($row->hash . Utilities::gensalt(15));
        $query = "INSERT INTO session(user_id,hash" . $access_keys . ") VALUES(" . $row->id . ", '" . $hashSession . "' " . $access_values . ")";
        $result['query'] = $query;
        if ($mysqli->query($query)) {
            $result['id'] = $mysqli->insert_id;
            $result['hash'] = $hashSession;
            $result['status'] = "OK";
            $result['http_response_code'] = "201";
            return $result;
        }
        $result['status'] = "Ошибка при созданииs авторизационной сессии: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
        return $result;
    }

    public function logout()
    {
        $mysqli = DB::connector();
        $user = Utilities::my_auth($mysqli, $_COOKIE);
        if (!$user) {
            self::onError("Ошибка доступа!");
        }
        $query = "DELETE FROM session WHERE id=" . intval($_COOKIE['id']);
        $result['query'] = $query;
        if ($mysqli->query($query)) {
            $result['http_response_code'] = "201";
            $result['status'] = "Logged out";
            return $result;
        }
        $result['status'] = "Ошибка: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
        return $result;
    }

    public static function onError($text)
    {
        $result['status'] = $text;
        return $result;
    }
}
