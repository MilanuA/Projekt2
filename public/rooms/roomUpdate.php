<?php

require_once __DIR__ . "/../../autoloader/autoloader.php";

session_start();


class RoomUpdate extends  Page{

    private array $errors = [];
    private array $data = [];


    public function __construct()
    {
        $this->pageTitle = "Úprava místnosti";
    }

    protected function prepare(): void
    {
        if($_SERVER['REQUEST_METHOD'] !== 'POST'){

            $id = filter_input(INPUT_GET, 'roomId',FILTER_VALIDATE_INT, ["options" => ["min_range"=> 1]]);

            if (!$id) {
                self::showError(self::ERROR_BADREQUEST);
                return;
            }

            if(Database::getNumberOfRows("SELECT 1 FROM room WHERE room_id = :id", ['id' =>$id]) <= 0){
                self::showError(self::ERROR_NOTFOUND);
                return;
            }

            $roomData = Database::select("SELECT * FROM room WHERE room_id = :id",['id' =>$id] );
            $phone = $roomData->phone ?: "";

            $room = new Room($roomData->room_id, $roomData->name, $roomData->no, $phone);
            $this->data = $room->getData();
            return;
        }


        $room = Room::readData();
        $validate = $room->validate($this->errors);
        $this->data = $room->getData();

       if(!$validate)  return;

        if(!Room::checkLoggin())
            self::redirectTo("/../rooms/rooms.php", false, Page::ERROR_NOTADMIN);

       $success = $room->updateRoom();
       self::redirectTo("/../rooms/rooms.php", $success, Page::ACTION_UPDATE);
    }

    protected function body()
    {
        return RoomForm::addNewRoomForm($this->errors, $this->data, "roomUpdate.php", "Upravit");
    }
}

$loadedPage = new RoomUpdate();
$loadedPage->renderPage();