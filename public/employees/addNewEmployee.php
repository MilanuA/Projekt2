<?php

require_once __DIR__ . "/../../autoloader/autoloader.php";

session_start();

class  AddNewEmployeePage extends  Page {

    private array $errors = [];
    private array $data = [];

    public function __construct()
    {
        $this->pageTitle= "Přidat nového člena";
    }

    protected function prepare(): void
    {
        if($_SERVER['REQUEST_METHOD'] !== 'POST'){
            $employee = new Employee();
            $this->data = $employee->getData();
            return;
        }

        $employee = Employee::readData();
        $validate = $employee->validate($this->errors);
        $this->data = $employee->getData();


        if(!$validate) return;

        if(!Employee::checkLoggin())
            self::redirectTo("/../employees/employees.php", false, Page::ERROR_NOTADMIN);

        $success = $employee->addEmployee();
        self::redirectTo("/../employees/employees.php", $success, Page::ACTION_NEW);
    }

    protected function body()
    {
        return EmployeeForm::addNewEmployeeForm($this->errors, $this->data, "addNewEmployee.php", "Přidat", false);
    }
}

$loadedPage = new AddNewEmployeePage();
$loadedPage->renderPage();