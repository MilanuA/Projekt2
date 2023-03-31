<?php

class Login
{
    public static function checkCredentials($data) : void {

        //kontrola, zda účet vůbec existuje
        $numberOfRows = Database::getNumberOfRows("SELECT login FROM employee WHERE login = :login", ['login' => $data["username"]] );

        if ($numberOfRows <= 0) {  echo self::error("Účet neexistuje"); return; }

        //kontrola hesla
        $accData = Database::select("SELECT password, admin, employee_id FROM employee WHERE login = :login", ['login' => $data["username"]]);

        if(!password_verify($data["password"],  $accData->password)) {echo  self::error("Špatné heslo"); return;}

        Login::redirectUser($accData->admin, $accData->employee_id);
    }

    private static function redirectUser($isAdmin, $id) : void {

        $_SESSION["isAdmin"] = $isAdmin; //nastavení, zda je přihlášený uživatel admin
        $_SESSION["logged"] = true; //uživatel je přihlášen
        $_SESSION["userID"] = $id;

       header('Location:crossroad.php');
       exit();
    }


    public  static function updateCredentials($data) : bool {
        $updateSQL = "UPDATE employee SET password = :newPassword WHERE employee_id = :id";

       if(!self::checkPassword($data))  return false;

       $hash = password_hash($data["pass1"], PASSWORD_DEFAULT, array('cost' => 15));

       return database::checkSuccess($updateSQL, ['newPassword' =>$hash, 'id' =>$_SESSION["userID"]]);
    }

    //kontrola, zda se obě hesla shodují a jestli je nynější heslo stejné s heslem v databázi.
    private static  function checkPassword($data) : bool {
        if($data["pass1"] !== $data["pass2"]) {echo self::error("Hesla ne neshodují");; return false; }

        $password = database::select("SELECT password FROM employee WHERE employee_id = :id", ['id' =>$_SESSION["userID"]] );

        if(!password_verify($data["currPassword"],$password->password) ) {echo self::error("Heslo se neshoduje s uloženým heslem"); return false;}

        return  true;
    }

    private static  function  error($message) : string {
        return" <div class=' alert alert-danger' role='alert'> ".$message."</div>";
    }
}