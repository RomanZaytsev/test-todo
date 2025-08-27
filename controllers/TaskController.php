<?php

namespace app\controllers;

use app\models\Task;
use app\models\User;

class TaskController extends Controller
{
    public function post()
    {
        $params = [];
        $errors = [];
        $validate = [
            'username' => empty(@$_POST['username']) ? "не заполнено" : true,
            'email' => empty(@$_POST['email']) ? "не заполнено" : ((!filter_var(@$_POST['email'], FILTER_VALIDATE_EMAIL)) ? "email не валиден" : true),
            'text' => empty(@$_POST['text']) ? "не заполнено" : true,
        ];
        foreach ($validate as $key => $value) {
            if ($value !== true) {
                $errors[$key] = $value;
            } else {
                $params[$key] = $_POST[$key];
            }
        }
        if (empty($errors)) {
            $result = Task::post($params);
        } else {
            $result['validate'] = $validate;
        }
        if (isset($result['http_response_code'])) {
            http_response_code($result['http_response_code']);
        }
        $this->renderJSON($result);
    }

    public function update()
    {
        $user = User::current();
        $result = [];
        if (@$user->name != 'admin') {
            $result['status'] = "Нет прав для редактирования";
            $result['redirect'] = "?controller=user&action=login";
        } else {
            $params = [];
            $errors = [];
            $validate = [
                'id' => !is_numeric($_POST['id'] ?? false) ? 'Неправильный формат ID.' : true,
                'username' => empty(@$_POST['username']) ? "не заполнено" : true,
                'email' => empty(@$_POST['email']) ? "не заполнено" : ((!filter_var(@$_POST['email'], FILTER_VALIDATE_EMAIL)) ? "email не валиден" : true),
                'text' => empty(@$_POST['text']) ? "не заполнено" : true,
            ];
            // Проверяем поле 'done' только если оно передано
            if (isset($_POST['done'])) {
                $validate['done'] = !is_numeric($_POST['done'] ?? false) ? 'Неправильный формат done.' : true;
            }
            foreach ($validate as $key => $value) {
                if ($value !== true) {
                    $errors[$key] = $value;
                } else {
                    $params[$key] = $_POST[$key];
                }
            }
            if (empty($errors)) {
                $result = Task::update($params);
            } else {
                $result['validate'] = $validate;
            }
        }
        if (isset($result['http_response_code'])) {
            http_response_code($result['http_response_code']);
        }
        $this->renderJSON($result);
    }

    public function delete()
    {
        $user = User::current();
        $result = [];
        if (@$user->name != 'admin') {
            $result['status'] = "Нет прав для удаления";
            $result['redirect'] = "?controller=user&action=login";
        } else {
            $params = [];
            $errors = [];
            $id = $_POST['id'] ?? '';

            // Проверяем, что ID не пустой и не равен "undefined"
            if (empty($id) || $id === 'undefined' || !is_numeric($id)) {
                $errors[] = 'Неправильный формат ID.';
            } else {
                $params['id'] = (int)$id;
            }

            if (empty($errors)) {
                $result = Task::delete($params);
            } else {
                $result['status'] = implode('; ', $errors);
            }
        }
        if (isset($result['http_response_code'])) {
            http_response_code($result['http_response_code']);
        }
        $this->renderJSON($result);
    }

    public function accept()
    {
        $result = Task::update($_POST);
        if (isset($result['http_response_code'])) {
            http_response_code($result['http_response_code']);
        }
        $this->renderJSON($result);
    }

    public function list()
    {
        $current = @$_POST['current'] ? $_POST['current'] : 1;
        $rowCount = @$_POST['rowCount'] ? $_POST['rowCount'] : 3;
        $total = Task::getAll(["select" => "count(*) count"])[0]['count'];
        $totalPages = max(1, ceil($total / $rowCount));
        if ($current > $totalPages) {
            $current = $totalPages;
        }
        $condition = $rowCount > 0 ? ['limit' => $rowCount, 'offset' => ($current - 1) * $rowCount] : [];
        $condition['sort'] = @$_POST['sort'];
        $rows = Task::getAll($condition);
        if (@$_POST['id'] == "bootgrid") {
            foreach ($rows as &$row) {
                foreach ($row as $key => $value) {
                    $row[$key] = htmlentities($value ?? '');
                }
            }
        }
        $result = [
            'current' => intval($current),
            'rowCount' => intval($rowCount),
            'rows' => $rows,
            'total' => intval($total),
        ];
        $this->renderJSON($result);
    }
}
