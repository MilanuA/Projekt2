<?php
    require_once __DIR__ . "/../../autoloader/autoloader.php";

    session_start();

    class EmployeeDetailPage extends  Page{

        private int $id;
        private Employee $employee;

        protected function prepare(): void
        {
            $this->id = filter_input(INPUT_GET, 'humanID',FILTER_VALIDATE_INT, ["options" => ["min_range"=> 1]]);

            if (!$this->id) {
                self::showError(self::ERROR_BADREQUEST);
                return;
            }

            if(Database::getNumberOfRows("SELECT 1 FROM employee WHERE employee_id = :id", ['id' =>$this->id]) <= 0){
                self::showError(self::ERROR_NOTFOUND);
                return;
            }

            $this->employee = new Employee();
            $this->employee->getEmployeeData($this->id);
            $this->pageTitle = $this->employee->getName();
        }

        protected function body()
        {
            return $this->employee->showEmployeeDetail($this->id);
        }
    }

    $loadedPage = new EmployeeDetailPage();
    $loadedPage->renderPage();
