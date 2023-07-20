<?php

namespace app\controllers;

class Controller
{
    public $user = null;

    public function render($view, $parameters = [], $main_template = null)
    {
        if (!$main_template) {
            $main_template = BASEPATH . "/view/main.php";
        }
        extract($parameters);
        ob_start();
        include $view;
        $_content = ob_get_clean();
        include $main_template;
    }

    public function renderJSON($data)
    {
        header("Content-type: application/json; charset=utf-8");
        echo json_encode($data);
    }
}
