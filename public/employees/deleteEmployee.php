<?php

require_once __DIR__ . "/../../autoloader/autoloader.php";

session_start();


class DeleteEmployeePage extends  Page{

    protected function prepare(): void
    {
        if(!Employee::checkLoggin())
            self::redirectTo("/../employees/employees.php", false, Page::ERROR_NOTADMIN);

        $id = filter_input(INPUT_POST, 'humanId', FILTER_VALIDATE_INT);

        $deleteKeys =  Database::checkSuccess("DELETE FROM `key` WHERE employee = :id ",["id" => $id]);
        $deleteEmployee = Database::checkSuccess("DELETE FROM employee WHERE employee_id = :id", ["id" => $id]);

        $success = $deleteKeys && $deleteEmployee;

        Page::redirectTo("employees.php",$success, self::ACTION_REMOVE );
    }

    protected function body()
    {
        return "";
    }
}

$loadedPage = new DeleteEmployeePage();
$loadedPage->renderPage();