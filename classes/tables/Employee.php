<?php
require_once __DIR__ . "/../../autoloader/autoloader.php";

class Employee
{
    private object $data;

    private int $employeeID;
    private string $name;
    private string $surname;
    private string $job;
    private int $wage;
    private int $room;
    private array $keys;
    private int $isAdmin;
    private string $login;


    private bool $passEntered;
    private string $password;


    //vypůjčeno =)
    public function __construct(?int $employeeID = 0, ?string $name = "", ?string $surname = "", ?string $job = "", ?int $wage = 0, ?int $selectedRoom = 0, ?array $keys = [], ?int $isAdmin = 0, ?string $login = "")
    {
        $this->employeeID = $employeeID;
        $this->name = $name;
        $this->surname = $surname;
        $this->job = $job;
        $this->wage = $wage;
        $this->room = $selectedRoom;
        $this->keys = $keys;

        $this->isAdmin = $isAdmin;
        $this->login = $login;
    }

    public function getEmployeeData($id): void
    {
        $this->data = Database::select("SELECT employee.name as 'jmeno', employee.surname as 'prijmeni', employee.job as 'pozice', employee.wage as 'mzda', room.room_id as 'mistnostID', room.name as 'mistnost' 
                                    FROM employee
                                    JOIN room ON employee.room = room.room_id WHERE employee_id=:humanID", ['humanID' => $id]);

        $this->name = $this->data->jmeno;
        $this->surname = $this->data->prijmeni;
    }

    public function showEmployeeDetail($id): string
    {
        $wage = number_format($this->data->mzda, 2, '.', ',');
        $room = $this->data->mistnost;

        $echoString = "<div class='container'>";
        $echoString .= " <h1>Karta osoby:  " . $this->getName() . " </h1>
                         <dl class='dl-horizontal'>
                         <dt>Jméno:</dt> <dd> $this->name</dd>
                         <dt>Přijmení</dt> <dd>$this->surname</dd>
                         <dt>Pozice:</dt> <dd>" . $this->data->pozice . "</dd>
                         <dt>Mzda:</dt> <dd>$wage</dd>
                         <dt>Místnost:</dt> <dd><a href='/../rooms/roomDetail.php?roomID= " . $this->data->mistnostID . "'>$room</a></dd>
                        ";

        $echoString .= "<dt>Klíče: </dt> ";

        $roomKeys = Database::fetch("Select room.name as 'mistnostNazev', room.room_id as 'mistnostID' from `key` klic JOIN room ON klic.room = room.room_id WHERE klic.employee =:humanID", ['humanID' => $id]);

        for ($i = 0; $i < count($roomKeys); $i++)
            $echoString .= "<dd><a href='/../rooms/roomDetail.php?roomID= " . $roomKeys[$i]->mistnostID . "'>" . $roomKeys[$i]->mistnostNazev . " </a></dd>";


        $echoString .= "</dl> <div class='end'><a href='employees.php'><span class='glyphicon glyphicon-arrow-left' aria-hidden='true'></span> Zpět na seznam zaměstnanců </a></div></div> </div>";

        return $echoString;
    }

    public function getName(): string
    {
        return $this->surname. " " . $this->name[0]. ".";
    }

    public function addEmployee(): bool
    {
        $hash = password_hash($this->password, PASSWORD_DEFAULT, array('cost' => 15));
        $admin = $this->isAdmin == 1 ? "1" : "0";

        $employeeID = Database::insert("INSERT INTO employee (name, surname, job, wage, room,admin, login, password) VALUES (:name, :surname, :job, :wage, :room, :admin, :login, :password)",
            ['name' => $this->name, 'surname' => $this->surname, 'job' => $this->job, 'wage' => $this->wage, 'room' => $this->room, 'admin' => $admin, 'login'=> $this->login,'password' => $hash, ]);

        if(!$employeeID) return false;

        if(count($this->keys) <= 0)  return true;

        $params = array();

        for ($i = 0; $i < count($this->keys); $i++) {
            $params[] = $employeeID;
            $params[] = $this->keys[$i];
        }

        $sql = "INSERT INTO `key` (employee, room) VALUES (?,?)".str_repeat(',(?,?)', count($this->keys) - 1); //vkládání klíčů do tabulky s klíči

        return Database::checkSuccess($sql, $params);
    }

    public  function  updateEmployee() : bool {

        $admin = $this->isAdmin == 1 ? "1" : "0";

        $updateEmployeeInfo = Database::checkSuccess("UPDATE employee SET name= :name, surname= :surname, job = :job, wage = :wage, room = :room, login =  :login, admin = :admin WHERE employee_id = :id",
            ['name' => $this->name, 'surname' => $this->surname, 'job' => $this->job, 'wage' => $this->wage, 'room' => $this->room, 'id' => $this->employeeID, 'login' => $this->login, 'admin' => $admin ]);


        if( isset($this->password))
            $this->updatePassword();


        $roomKeys = Database::fetch("Select room.room_id as 'mistnostID' from `key` klic JOIN room ON klic.room = room.room_id WHERE klic.employee =:humanID", ['humanID' => $this->employeeID]);
        $keys = array_map('current',json_decode( json_encode($roomKeys), true)); // https://stackoverflow.com/questions/8754980/convert-array-of-single-element-arrays-to-a-one-dimensional-array a https://stackoverflow.com/questions/30260076/object-of-class-stdclass-could-not-be-converted-to-string-laravel

        if(!$updateEmployeeInfo) return  false;

        return $this->updateKeys($keys);
    }

    //vypůjčeno =)
    public function validate(&$errors = []): bool
    {
        if (!isset($this->name) || (!$this->name))
            $errors['name'] = 'Jméno nesmí být prázdné';

        if (!isset($this->surname) || (!$this->surname))
            $errors['surname'] = 'Přijmení nesmí být prázdné';

        if (!isset($this->job) || (!$this->job))
            $errors['job'] = 'Pozice nesmí být prázdná';

        if (!isset($this->room) || (!$this->room))
            $errors['room'] = 'Musí být vybrána domovská místnost';


        if(!$this->checkString($this->name) && $this->name )
            $errors['name'] = 'Jméno není jménem. Zkontrolujte ho';

        if(!$this->checkString($this->surname) && $this->surname)
            $errors['surname'] = 'Přijmení není přijmením. Zkontrolujte ho';

        if(!$this->checkString($this->job) && $this->job)
            $errors['job'] = 'Pozice nesmí obsahovat čísla';

        if(!$this->checkWage($this->wage) && $this->wage)
            $errors['wage'] = 'Přeci nechceme zápornou mzdu :)';

        if (!isset($this->login) || (!$this->login))
            $errors['login'] = 'Login nesmí být prázdný';

        //kontrola pouze v případě, že se upravuje employee
        if($_POST['isUpdating']){
            if(!$this->checkLogin($this->login) && $this->login)
                $errors['login'] = 'Login už je obsazený, zkuste jiný';
        }

        //kontrola pouze v případě, že se vytváří nový employee
        if(!$_POST['isUpdating']){
            if (!isset($this->password) || (!$this->password) )
                $errors['password'] = 'Heslo musí být vyplněné';

            if(!$this->checkLoginRowCount($this->login) && $this->login)
                $errors['login'] = 'Login už je obsazený, zkuste jiný';
        }

        return count($errors) === 0;
    }

    //vypůjčeno =)
    public static function readData(): self
    {
        $employee = new Employee();

        $employee->employeeID = filter_input(INPUT_POST, 'humanId', FILTER_VALIDATE_INT);

        $employee->name = filter_input(INPUT_POST, 'name');

        if ($employee->name)
            $employee->name = trim($employee->name);

        $employee->surname = filter_input(INPUT_POST, 'surname');

        if ($employee->surname)
            $employee->surname = trim($employee->surname);

        $employee->job = filter_input(INPUT_POST, 'job');

        if ($employee->job)
            $employee->job = trim($employee->job);

        $employee->wage = filter_input(INPUT_POST, 'wage', FILTER_VALIDATE_INT);

        $room = filter_input(INPUT_POST, 'room');

        if ($room)
            $employee->room = $room;

        $keys = filter_input(INPUT_POST, 'keys', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);

        if ($keys)
            $employee->keys = $keys;

        $login = filter_input(INPUT_POST, 'login');

        if($login)
            $employee->login = $login;

        $adminChecked = filter_input(INPUT_POST, 'isAdmin', FILTER_VALIDATE_INT);

        if($adminChecked)
            $employee->isAdmin = $adminChecked;

         $password = filter_input(INPUT_POST, 'password');

         if($password)
             $employee->password = $password;

        return $employee;
    }

    public function getData(): array
    {
        return array("id" => $this->employeeID, "name" => $this->name, "surname" => $this->surname, "job" => $this->job, "wage" => $this->wage, "room" => $this->room, "keys" => $this->keys, "isAdmin" => $this->isAdmin, "login" => $this->login);
    }

    public static function  checkLoggin() : bool{
        return $_SESSION["isAdmin"] == 1;
    }

    private function  checkLoginRowCount($login) : bool {
        $count = Database::getNumberOfRows("SELECT employee_id FROM employee WHERE login =:login", ["login" => $login]);

        return $count <= 0;
    }

    private  function  checkLogin($login) : bool {
        $id = Database::select("SELECT employee_id FROM employee WHERE login =:login", ["login" => $login]);
        return $id->employee_id ?? $this->employeeID;
    }

    private  function  checkString($input) : bool {
       return  preg_match('/[A-Za-z]/', $input);
    }

    private  function checkWage($wage) :bool{
        return $wage >= 0;
    }

    //metoda, která se stará o mazání a přidávání klíčů u employee
    private  function updateKeys($keysFromDatabase): bool
    {
        $arrayMap = array_map('intval', $this->keys);

        $delKeys = array_diff($keysFromDatabase, $arrayMap); //příprava klíčů na odstranění z databáze

        $newKeys = array_diff($arrayMap, $keysFromDatabase ); //příprava klíču na přidání do databáze

        $success = true;
        $success2 = true;

        if(count($delKeys) > 0)  $success = $this->deleteKeys($delKeys);

        if(count($newKeys) > 0)  $success2 = $this->addKeys($newKeys);

        return $success && $success2;
    }

    private  function  deleteKeys($keys) : bool{
        $params = array();

        foreach ($keys as $delKey)
            $params[] = $delKey;

        $params[] = $this->employeeID;

        $sql = "DELETE FROM `key` WHERE room IN (?".str_repeat(',?', count($keys) - 1) .") AND employee = ?";
        return  Database::checkSuccess($sql, $params);
    }

    private  function  addKeys($keys) : bool{
        $params = array();

        foreach ( $keys as $newKey){
            $params[] = $this->employeeID;
            $params[] = $newKey;
        }

        $sql =  "INSERT INTO `key` (employee, room) VALUES (?,?)".str_repeat(',(?,?)', count($keys) - 1);

        return  Database::checkSuccess($sql, $params);
    }


    private  function  updatePassword() : bool {
        $hash = password_hash($this->password, PASSWORD_DEFAULT, array('cost' => 15));
        return Database::checkSuccess("UPDATE employee SET password = :password WHERE employee_id = :id", ['id' => $this->employeeID, 'password' => $hash]);
    }
}