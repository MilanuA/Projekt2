<?php
require_once __DIR__ . "/../../autoloader/autoloader.php";

session_start();


class  EmployeesListPage extends  Page {

    private  array $data;
    private  Table $table;

    public function __construct()
    {
        $this->pageTitle = "Prohlížeč zaměstnanců";
    }

    protected function prepare() : void
    {
        if(Database::getNumberOfRows("SELECT 1 FROM employee") <= 0){
            self::showError(self::ERROR_NOTFOUND);
            return;
        }

        $this->data = Database::fetch("SELECT employee.name as 'jmeno', employee.surname as 'prijmeni',
        employee.job as 'pozice', employee.employee_id as 'zamestnanecID', room.name as 'mistnost', room.phone as 'telefon'
        FROM employee
            JOIN room ON employee.room = room.room_id");

        $this->table = new Table();
    }

    protected function body()
    {
         return $this->table->buildEmployeeTable(array("Jméno", "Místnost", "Telefon", "Pozice"), "Úprava", $this->data);
    }
}

$pageLoaded = new EmployeesListPage();
$pageLoaded->renderPage();
