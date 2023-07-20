<?php

namespace app\models;

use app\configs\DB;
use app\helpers\Utilities;
use app\helpers\PasswordHasher;

class User
{
    public static $currentUser;
    public $id;
    public $name;
    public $email;
    public $hash;

    public function create($params)
    {
        $mysqli = DB::connector();

        if (Utilities::sql_exist($mysqli, "SELECT * FROM user WHERE email='" . $mysqli->real_escape_string($params['email']) . "'")) {
            $result['status'] = "E-mail busy";
            return $result;
        }
        $query = "INSERT INTO user(name,email,hash)
    							VALUES('" . $mysqli->real_escape_string($params['name']) . "',
										'" . $mysqli->real_escape_string($params['email']) . "',
	 									'" . $mysqli->real_escape_string(PasswordHasher::hash($params['password'])) . "')";
        $result['query'] = $query;
        if ($mysqli->query($query)) {
            $result['id'] = $mysqli->insert_id;
            $result['status'] = "Created";
        } else {
            $result['status'] = "Ошибка при создании учётной записи: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
        }
        return $result;
    }

    public function getuser($params)
    {
        $mysqli = DB::connector();
        $result = [];
        if ($params['id']) {
            $query = "SELECT * FROM user WHERE id='" . intval($params['id']) . "'";
        } else {
            if ($params['email']) {
                $query = "SELECT * FROM user WHERE email='" . $mysqli->real_escape_string($params['email']) . "'";
            } else {
                return $result;
            }
        }

        $queryResult = $mysqli->query($query);
        $row = $queryResult->fetch_object();
        $queryResult->close();
        if (!$row) {
            $result['status'] = "User was not found";
        } else {
            $result['profile'] = $row;
            $result['status'] = "OK";
        }
        return $result;
    }

    /**
     * Проверяет пароль пользователя
     */
    public function verifyPassword($password, $hash)
    {
        return PasswordHasher::verify($password, $hash);
    }

    public static function current()
    {
        $result = null;
        if (self::$currentUser) {
            $result = self::$currentUser;
        } else {
            $mysqli = DB::connector();
            $cookie = $_COOKIE;
            if (@$cookie['id'] != null and @$cookie['hash'] != null) {
                $id = $cookie['id'];
                $hash = $cookie['hash'];
                $query = "
					SELECT
					a.*,b.name,b.email
					FROM session a
					JOIN user b
					ON a.user_id = b.id
					WHERE
						a.id = " . intval($id) . "
					AND
						a.hash = '" . $mysqli->real_escape_string($hash) . "'";
                $queryResult = $mysqli->query($query);
                $row = $queryResult->fetch_object();
                $queryResult->close();
                if ($row) {
                    $result = new User();
                    $result->id = $row->id;
                    $result->name = $row->name;
                    $result->email = $row->email;
                    $result->hash = $row->hash;
                    self::$currentUser = $result;
                }
            }
        }
        return $result;
    }
}
