<?php

namespace app\controllers;

class Controller
{
    public $user = NULL;

    public function render($view, $parameters = [], $main_template = NULL)
    {
        if (!$main_template) $main_template = BASEPATH . "/view/main.php";
        extract($parameters);
        ob_start();
        include $view;
        $_content = ob_get_clean();
        include $main_template;
    }
}

?>