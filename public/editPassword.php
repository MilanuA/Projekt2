<?php

require_once __DIR__ . "/../autoloader/autoloader.php";

session_start();


class  EditPasswordPage extends  Page{

    public function __construct()
    {
        $this->pageTitle = "Úprava hesla";
    }

    protected function prepare() : void
    {
        if($_SERVER['REQUEST_METHOD'] !== 'POST') return;

        $formData = $_POST["data"];
        $success = Login::updateCredentials($formData);

        if(!$success) return;

        self::redirectTo("crossroad.php", $success, Page::ACTION_UPDATEPASSWORD);
    }

    protected function body()
    {
        return "  <div class='container'> 
          <form action='editPassword.php' method='post'>
          <div class='form-group'>
            <label for='exampleInputEmail1'>Nynější heslo</label>
            <input type='password' class='form-control' name='data[currPassword]'  maxlength='30' placeholder='Zadejte nynější' required>
           
          </div>
          <div class='form-group'>
            <label for='exampleInputPassword1'>Nové heslo</label>
            <input type='password' class='form-control' name='data[pass1]'  maxlength='30' placeholder='Heslo poprvé' required>
          </div>  <div class='form-group'>
            <label for='exampleInputPassword1'>Znovu nové heslo</label>
            <input type='password' class='form-control' name='data[pass2]'  maxlength='30' placeholder='Heslo podruhé' required>
          </div>
          <button type='submit' class='btn btn-primary'>Změnit</button>
        </form>  <a href='/../crossroad.php' class='btn btn-primary'>Vrátit se</a></div>";
    }
}

$loadedPage = new EditPasswordPage();
$loadedPage->renderPage();