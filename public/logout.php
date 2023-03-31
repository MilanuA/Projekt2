<?php

require_once __DIR__ . "/../autoloader/autoloader.php";

session_start();

class  LogoutPage extends  Page {

    protected function prepare() : void
    {
        session_destroy();
        header('Location:index.php');
        exit();
    }

    protected function body()
    {
        return "";
    }
}

$loadedPage = new LogoutPage();
$loadedPage->renderPage();