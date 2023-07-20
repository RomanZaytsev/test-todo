<?php

namespace app\helpers;

class Utilities
{
    public static function sql_exist(&$mysqli, $select)
    {
        $query = "SELECT EXISTS (" . $select . ") e";
        $queryResult = $mysqli->query($query);
        $row = $queryResult->fetch_object();
        $queryResult->close();
        if ($row->e === '1') {
            return true;
        }
        return false;
    }

    public static function gensalt($count)
    {
        $rand = '';
        for ($i = 0; $i < $count; $i++) {
            $rand .= chr(rand(33, 126));
        }
        return $rand;
    }

    public static function my_auth(&$mysqli, $cookie)
    {
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
            return $row;
        }
    }
}
