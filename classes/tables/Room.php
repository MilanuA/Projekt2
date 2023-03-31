<?php

require_once __DIR__ . "/../../autoloader/autoloader.php";


class Room
{
    private  object $roomData;

    private int $roomID;
    private string $name;
    private int $no;
    private  string $phone;

    //vypůjčeno =)
    public function __construct(?int $roomID = 0, ?string $name = "", ?int $no = 0, ?string $phone = "")
    {
        $this->roomID = $roomID;
        $this->name = $name;
        $this->no = $no;
        $this->phone = $phone;
    }

    public  function  getRoomData($id) : void {
        $this->roomData = Database::select("SELECT no, name, phone, room_id FROM room WHERE room_id=:roomID", ['roomID' => $id]);
    }

    public  function  showRoomDetail($id) : string
    {
        $keysData = Database::fetch("Select employee.name as 'name', employee.surname as 'surname', employee.employee_id as 'employeeID' from `key` klice JOIN employee ON klice.employee = employee.employee_id WHERE klice.room =:roomID ORDER BY employee.surname",
            ['roomID' => $id] );

        $echoString = "<div class='container'>";

        $phoneNumber =  $this->roomData->phone === null? "—" :  $this->roomData->phone;

        $echoString .= "<h1>Místnost č. ".$this->roomData->no." </h1> 
                        <dl class='dl-horizontal'>
                        <dt>Číslo</dt> <dd>".$this->roomData->no."</dd>
                        <dt>Název</dt> <dd> ".$this->roomData->name."</dd> 
                        <dt>Telefon</dt><dd>$phoneNumber </dd>" ;

        $echoString .= self::generatePeople($id);
        $echoString .= "<dt>Klíče</dt>";

        for ($i = 0; $i < count($keysData); $i++) {
            $name = $keysData[$i]->name;
            $surname = $keysData[$i]->surname;
            $echoString.= "<a href='/../employees/employeeDetail.php?humanID=".$keysData[$i]->employeeID."'><dd>$surname $name[0].</dd></a> ";
        }

        $echoString .= " </dl> <div class='end'><a href='rooms.php'> <span class='glyphicon glyphicon-arrow-left' aria-hidden='true'></span> Zpět na seznam místností</a></div></div></div>";

       return $echoString;
    }

    public  function  deleteRoom($id) : bool {

        return Database::checkSuccess("DELETE FROM `key` where room =:id", ["id" => $id]) && Database::checkSuccess("DELETE FROM room WHERE room_id =:id", ["id" => $id]);
    }

    public  function  updateRoom() : bool {
        $phone = $this->phone ?: null;

        return Database::checkSuccess("UPDATE room SET name = :name, no = :no, phone = :phone WHERE room_id = :id",["name" => $this->name, "no" => $this->no, "phone" => $phone, "id" => $this->roomID ]);
    }
    public function addRoom(): bool
    {
        return Database::checkSuccess("INSERT INTO room (name, no, phone) VALUES (:name, :no, :phone)", ["name" => $this->name, "no" => $this->no, "phone" => $this->phone]);

    }

    public  function  getRoomNumber() : int {
        return $this->roomData->no;
    }


    private function  generatePeople($roomID) : string{
        $people = Database::fetch("SELECT employee_id, name, surname, room FROM employee WHERE room = :roomID", ['roomID' => $roomID]);

        $stringToReturn = "";

        if(count($people)===0){
            $stringToReturn .= "<dt>Lidé</dt> <dd>—</dd>";
            $stringToReturn .= "<dt>Průměrná mzda</dt> <dd>—</dd>";

            return $stringToReturn;
        }

        $stringToReturn .= "<dt>Lidé:</dt>";

        for ($i = 0; $i < count($people); $i++) {
            $name = $people[$i]->name;
            $surname = $people[$i]->surname;

            $stringToReturn .= "<dd><a href='/../employees/employeeDetail.php?humanID= ".$people[$i]->employee_id."'>$surname $name[0]. </a></dd>";
        }

        $wageData = Database::select("SELECT AVG(wage) as 'prumerna' FROM employee WHERE room=:roomID", ['roomID' => $roomID]);
        $wage = number_format($wageData->prumerna, 2, '.', ',');

        $stringToReturn .= "<dt>Průměrná mzda</dt> <dd>$wage</dd>";


        return  $stringToReturn;
    }

    //vypůjčeno =)
    public function validate(&$errors = []): bool
    {
        if (!isset($this->name) || (!$this->name))
            $errors['name'] = 'Jméno místnosti nesmí být prázdné';

        if (!isset($this->no) || (!$this->no))
            $errors['no'] = 'Číslo místnosti nesmí být prázdné';

        if(!$this->checkPhone($this->phone) && $this->phone)
            $errors['phone'] = "Telefon musí být číslo";

        $phoneError = self::checkInDatabase("phone",$this->phone);

        if($phoneError !== "" && $this->phone)
            $errors['phone'] = $phoneError;

        $noError = self::checkInDatabase("no",$this->no);

        if($noError !== "" && $this->no)
            $errors['no'] = $noError;

        if(!$this->checkString($this->name) && $this->name)
            $errors['name'] = 'Jméno místnosti nesmí obsahovat číslice';

        return count($errors) === 0;
    }

    //vypůjčeno =)
    public static function readData(): self
    {
        $room = new Room();

        $room->roomID = filter_input(INPUT_POST, 'roomId');
        $room->name = filter_input(INPUT_POST, 'name');

        if ($room->name)
            $room->name = trim($room->name);

        $room->phone = filter_input(INPUT_POST, 'phone');
        $room->no = filter_input(INPUT_POST, 'no', FILTER_VALIDATE_INT);

        return $room;
    }

    public function getData(): array
    {
        return array("id" => $this->roomID, "name" => $this->name, "phone" => $this->phone, "no"=>$this->no);
    }

    public static function  checkLoggin() : bool{
        return $_SESSION["isAdmin"] == 1;
    }

    //kontrola cizích klíčů
    private  function  checkInDatabase($where,$value) : string{
        if(!$this->checkValue($value)) return  "Nechceme přeci zápornou hodnotu :) ";

       $id = Database::select("SELECT room_id FROM room WHERE ".$where." = :equalValue",  ["equalValue" => $value] );

       $checkID = $id->room_id ?? $this->roomID;

       if($this->roomID !==  $checkID) return "Bohužel už je obsazeno. Zkuste jinou hodnotu";

       return  "";
    }

    private function checkValue($value) : bool {
        return $value >= 0;
    }

    private  function  checkString($input) : bool {
        return  preg_match('/[A-Za-z]/', $input);
    }

    private  function  checkPhone($input) : bool {
        return  preg_match('/[0-9]/', $input);
    }
}