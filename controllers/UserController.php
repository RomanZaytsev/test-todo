<?php

namespace app\controllers;

use app\models\Session;
use app\models\User;

class UserController extends Controller
{
    public function index()
    {
        // Перенаправляем на страницу входа по умолчанию
        header("Location:" . BASEURL . "?controller=user&action=login");
        exit;
    }

    public function login()
    {
        if ($this->user) {
            header("Location:" . BASEURL);
        } else {
            $this->render(BASEPATH . "/view/user/login.php", [
                'title' => 'Вход',
                'jscripts' => array(),
                'user' => $this->user,
                'breadcrumbs' => ["Главная" => "?controller=site&action=index"],
            ]);
        }
    }

    public function regform()
    {
        if (User::current()) {
            header("Location:" . BASEURL);
        } else {
            $this->render(BASEPATH . "/view/user/regform.php", [
                'title' => 'Регистрация',
                'jscripts' => array(),
                'user' => $this->user,
                'breadcrumbs' => ["Главная" => "?controller=site&action=index"],
            ]);
        }
    }

    public function profile()
    {
        if (!User::current()) {
            header("Location:" . BASEURL);
        } else {
            $this->render(BASEPATH . "/view/user/profile.php", [
                'title' => 'Профиль',
                'jscripts' => array(),
                'user' => $this->user,
                'breadcrumbs' => ["Главная" => "?controller=site&action=index"],
            ]);
        }
    }

    public function accountCreate()
    {
        $model = new User();
        $params = [];
        $errors = [];
        $validate = [
            'username' => empty(@$_POST['username']) ? "не заполнено" : true,
            'email' => empty(@$_POST['email']) ? "не заполнено" : true,
            'password' => empty(@$_POST['password']) ? "не заполнено" : true,
        ];
        foreach ($validate as $key => $value) {
            if ($value !== true) {
                $errors[$key] = $value;
            } else {
                $params[$key] = $_POST[$key];
            }
        }
        if (empty($errors)) {
            $result = $model->create($params);
        } else {
            $result['validate'] = $validate;
        }

        if (isset($result['http_response_code'])) {
            http_response_code($result['http_response_code']);
        }
        $this->renderJSON($result);
    }

    public function authorization()
    {
        $model = new Session();
        $params = [];
        $errors = [];
        $validate = [
            'login' => empty(@$_POST['login']) ? "не заполнено" : true,
            'password' => empty(@$_POST['password']) ? "не заполнено" : true,
        ];
        foreach ($validate as $key => $value) {
            if ($value !== true) {
                $errors[$key] = $value;
            } else {
                $params[$key] = $_POST[$key];
            }
        }
        if (empty($errors)) {
            $result = $model->login($params);
        } else {
            $result['validate'] = $validate;
            $result['status'] = 'Все поля обязательны для заполнения';
        }

        if (isset($result['http_response_code'])) {
            http_response_code($result['http_response_code']);
        }
        $this->renderJSON($result);
    }

    public function authorizationLogout()
    {
        $model = new Session();
        $result = $model->logout();

        if (isset($result['http_response_code'])) {
            http_response_code($result['http_response_code']);
        }
        $this->renderJSON($result);
    }
}
