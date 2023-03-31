<?php

require_once __DIR__ . "/../../autoloader/autoloader.php";

session_start();

class  UpdateEmployeePage extends  Page {

    private array $errors = [];
    private array $data = [];

    public function __construct()
    {
        $this->pageTitle="Úprava zaměstnance";
    }

    protected function prepare(): void
    {
        if($_SERVER['REQUEST_METHOD'] !== 'POST'){

            $id = filter_input(INPUT_GET, 'humanId',FILTER_VALIDATE_INT, ["options" => ["min_range"=> 1]]);

            if (!$id) {
                self::showError(self::ERROR_BADREQUEST);
                return;
            }

            if(Database::getNumberOfRows("SELECT 1 FROM employee WHERE employee_id = :id", ['id' =>$id]) <= 0){
                self::showError(self::ERROR_NOTFOUND);
                return;
            }

            $employeeData = Database::select("SELECT employee.employee_id as 'id', employee.name as 'jmeno', employee.surname as 'prijmeni', employee.job as 'pozice', employee.wage as 'mzda', employee.login  as 'login', employee.admin as 'admin', room.room_id as 'mistnostID', room.name as 'mistnost' 
                                    FROM employee
                                    JOIN room ON employee.room = room.room_id WHERE employee_id=:humanID", ['humanID' => $id]);

            $roomKeys = Database::fetch("Select room.room_id  from `key` klic JOIN room ON klic.room = room.room_id WHERE klic.employee =:humanID", ['humanID' => $id]);
            $keys =  array_map('current',json_decode( json_encode($roomKeys), true));

            $employee = new Employee($employeeData->id, $employeeData->jmeno, $employeeData->prijmeni, $employeeData->pozice, $employeeData->mzda, $employeeData->mistnostID, $keys, $employeeData->admin, $employeeData->login);
            $this->data = $employee->getData();

            return;
        }


        $employee = Employee::readData();
        $validate = $employee->validate($this->errors);
        $this->data = $employee->getData();


        if(!$validate) return;

        if(!Employee::checkLoggin())
            self::redirectTo("/../employees/employees.php", false, Page::ERROR_NOTADMIN);

        $success =  $employee->updateEmployee();
        self::redirectTo("/../employees/employees.php", $success, Page::ACTION_UPDATE);
    }

    protected function body()
    {
        return EmployeeForm::addNewEmployeeForm($this->errors, $this->data, "updateEmployee.php", "Upravit", true);
    }
}

$loadedPage = new UpdateEmployeePage();
$loadedPage->renderPage();