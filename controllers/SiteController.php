<?php

namespace app\controllers;

class SiteController extends Controller
{
    public function index()
    {
        $this->render(BASEPATH . "/view/index.php", [
            'title' => 'Главная страница',
            'jscripts' => array(),
            'user' => $this->user,
            'breadcrumbs' => [],
        ]);
    }
}
