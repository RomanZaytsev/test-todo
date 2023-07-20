<?php

namespace app\models;

use app\configs\DB;

class Tasks
{
    public static function getAll($condition = [])
    {
        $mysqli = DB::connector();
        $result = [];
        $query = "SELECT ";
        if (isset($condition['select'])) {
            $query .= $mysqli->real_escape_string($condition['select']);
        } else {
            $query .= "*";
        }
        $query .= " FROM tasks";
        if (is_array(@$condition['sort'])) {
            $query .= " ORDER By ";
            foreach ($condition['sort'] as $key => $value) {
                $query .= $key . " " . $mysqli->real_escape_string($value);
            }
        }
        if (isset($condition['limit'])) {
            $query .= " LIMIT " . intval($condition['limit']);
        }
        if (isset($condition['offset'])) {
            $query .= " OFFSET " . intval($condition['offset']);
        }
        $queryResult = $mysqli->query($query);
        if ($queryResult) {
            while ($row = $queryResult->fetch_object()) {
                $result[] = $row;
            };
        }
        return $result;
    }

    public static function getById($id)
    {
        $mysqli = DB::connector();
        $result = null;
        $query = "SELECT * FROM tasks WHERE id=" . intval($id);
        $queryResult = $mysqli->query($query);
        if ($queryResult) {
            while ($row = $queryResult->fetch_object()) {
                return $row;
            };
        }
        return $result;
    }

    public static function post($params)
    {
        $mysqli = DB::connector();
        $query = "INSERT INTO tasks(`username`,`email`,`text`) VALUES('" . $mysqli->real_escape_string($params['username']) . "', '" . $mysqli->real_escape_string($params['email']) . "', '" . $mysqli->real_escape_string($params['text']) . "')";
        $result['query'] = $query;
        if ($mysqli->query($query)) {
            $result['id'] = $mysqli->insert_id;
            $result['status'] = "OK";
        } else {
            $result['status'] = "Ошибка при добавлении: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
        }
        return $result;
    }

    public static function update($params)
    {
        $old = Tasks::getById($params['id']);
        if ($old->text != $params['text']) {
            $params['checked'] = 1;
        }
        $mysqli = DB::connector();
        $setvalue = "";
        foreach ($params as $key => $value) {
            if ($setvalue) {
                $setvalue .= ",";
            }
            $setvalue .= $key . "='" . $mysqli->real_escape_string($value) . "'";
        }
        $query = "UPDATE tasks SET " . $setvalue . " WHERE id=" . $mysqli->real_escape_string($params['id']);
        $result['query'] = $query;
        if ($mysqli->query($query)) {
            $result['status'] = "OK";
        } else {
            $result['status'] = "Ошибка при обновлении: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
        }
        return $result;
    }

    public static function delete($params)
    {
        $mysqli = DB::connector();
        $query = "DELETE FROM tasks WHERE id=" . $mysqli->real_escape_string($params['id']);
        $result['query'] = $query;
        if ($mysqli->query($query)) {
            $result['status'] = "OK";
        } else {
            $result['status'] = "Ошибка при удалении: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
        }
        return $result;
    }
}
