<?php

//vypůjčeno =)
abstract class Page
{
    public const ERROR_NOTFOUND = "notFound";
    public const ERROR_BADREQUEST = "badRequest";
    public const ACTION_NEW = "new";
    public const ACTION_UPDATE = "update";
    public const ACTION_REMOVE = "remove";
    public const ACTION_UPDATEPASSWORD = "updatePassword";
    public const ACTION_HOMEROOM = "homeRoom";
    public const ERROR_NOTADMIN = "notAdmin";

    protected string $pageTitle = "";
    protected bool $isLoginPage = false;


    private bool $error = false;



    protected function prepare() : void {}

    abstract protected function body();

    public function renderPage() : void  {

        //kontrola, zda se jedná o login stránku a podle toho zkontrolovat přihlášení a přesměrování uživatele
        $this->isLoginPage ? $this->checkLoginLoginPage() : $this->checkLogin();

        $this->prepare();
        self::generateHeader($this->pageTitle);

        if($this->error) return;

        echo $this->body();
    }

    //vypůjčeno =)
   public function redirectTo($location, $success, $action): void
    {
        $data = [
            'action' => $action,
            'success' => $success ? 1 : 0
        ];

        header("Location: ".$location."?". http_build_query($data));
        exit();
    }

    private function generateHeader($title) : void
    {
        $lang = JSONconfig::getJSONinfo('app.lang');

        echo
            "<html lang=".$lang.">
        <head>
            <meta charset='UTF-8'>
            <meta name='viewport'
                  content='width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0'>
            <meta http-equiv='X-UA-Compatible' content='ie=edge'>
            <title>".$title."</title>
        
        <link rel='stylesheet' type='text/css' href='style.css'>
        <link rel='stylesheet' href='https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css' integrity='sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm' crossorigin='anonymous'>

        </head>";

        self::showNotification();;
    }

    private function checkLogin(): void
    {
        if(isset($_SESSION["logged"])) return;

        header('Location:/../index.php');
        exit();
    }

    private function checkLoginLoginPage() : void
    {
        if(!isset($_SESSION["logged"])) return;

        header('Location:crossroad.php');
        exit();
    }

    protected function showError($status) : void
    {
        switch ($status) {
            case self::ERROR_BADREQUEST:
                http_response_code(400);
                self::generateHeader("Error 400: Bad request");
                echo "<h1>Error 400: Bad request</h1>";
                break;
            case self::ERROR_NOTFOUND:
                http_response_code(404);
                self::generateHeader("Error 404: Not found");
                echo "<h1>Error 404: Not found</h1>";
                break;
        }

        $this->error = true;
    }

    //vypůjčeno =)
    private  function showNotification() : void {

        $crudResult = filter_input(INPUT_GET, 'success', FILTER_VALIDATE_INT);
        $crudAction = filter_input(INPUT_GET, 'action');

        if (!is_int($crudResult))  return;

           $alert = $crudResult === 0 ? 'danger' : 'success';

           $message = '';

        if ($crudResult === 0 && !self::ACTION_HOMEROOM)
            $message = 'Něco se pokazilo, operace neproběhla úspěšně.';

        switch ($crudAction){
            case  self::ACTION_REMOVE:
                $message = 'Úspěšně smazáno.';
                break;
            case  self::ACTION_NEW:
                $message = 'Úspěšně založeno';
                break;
            case  self::ACTION_UPDATE:
                $message = 'Úspěšně upraveno';
                break;
            case  self::ACTION_UPDATEPASSWORD:
                $message = 'Heslo úspěšně změněno';
                break;
            case  self::ACTION_HOMEROOM:
                $message = 'Nelze smazat místnost, místnost je něčí domovskou místností';
                break;
            case self::ERROR_NOTADMIN:
                $message = 'Nejste Adminem, nemůžete upravovat';
        }

        echo "<div class='alert alert-".$alert."' role='alert'> ".$message." </div>";
    }
}
