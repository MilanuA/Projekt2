<?php

require_once __DIR__ . "/../../autoloader/autoloader.php";

session_start();

class  AddNewRoom extends  Page{

    private array $errors = [];
    private array $data = [];

    public function __construct()
    {
        $this->pageTitle = "Vytvoření místnosti";
    }

    protected function prepare(): void
    {
        if($_SERVER['REQUEST_METHOD'] !== 'POST'){
            $room = new Room();
            $this->data = $room->getData();
            return;
        }

        $room = Room::readData();
        $validate = $room->validate($this->errors);
        $this->data = $room->getData();

        if(!$validate) return;

        if(!Room::checkLoggin())
            self::redirectTo("/../rooms/rooms.php", false, Page::ERROR_NOTADMIN);

        $success = $room->addRoom();

        self::redirectTo("/../rooms/rooms.php", $success, Page::ACTION_NEW);
    }

    protected function body(): string
    {
        return RoomForm::addNewRoomForm($this->errors, $this->data, "addNewRoom.php", "Přidat");
    }
}

$loadedPage = new AddNewRoom();
$loadedPage->renderPage();