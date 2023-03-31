<?php
    require_once __DIR__ . "/../autoloader/autoloader.php";

    session_start();

class LoginPage extends  Page {

   public function __construct()
   {
       $this->pageTitle = "Přihlášení";
       $this->isLoginPage = true;
   }

   protected function prepare() : void
   {
       if($_SERVER['REQUEST_METHOD'] !== 'POST') return;

       $formData = $_POST["data"];
       Login::checkCredentials($formData);
   }

    protected function body()
    {
        return '<div class="container">
        <form action="index.php" method="post">
        <h2 class="text-center">Log in</h2>
        <div class="form-group">
            <input id="username" type="text" class="form-control" placeholder="Username"  maxlength="30" name="data[username]  " required="required">
        </div>
        <div class="form-group">
            <input id="password" type="password" class="form-control" placeholder="Password" maxlength="30" name="data[password]" required="required">
        </div>
        <div class="form-group">
            <button type="submit" class="btn btn-primary btn-block">Log in</button>
        </div>
    </form> </div>';
    }
}

$loadedPage = new LoginPage();
$loadedPage->renderPage();

