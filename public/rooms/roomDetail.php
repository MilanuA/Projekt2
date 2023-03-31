<?php

require_once __DIR__ . "/../../autoloader/autoloader.php";

session_start();


class  RoomDetailPage extends  Page{

    private int $id;
    private Room $room;

    protected function prepare(): void
    {
        $this->id = filter_input(INPUT_GET, 'roomID',FILTER_VALIDATE_INT, ["options" => ["min_range"=> 1]]);

        if (!$this->id) {
            page::showError(self::ERROR_BADREQUEST);
            return;
        }

        if(Database::getNumberOfRows("SELECT 1 FROM room WHERE room_id = :id", ['id' => $this->id]) <= 0){
            page::showError(self::ERROR_NOTFOUND);
            return;
        }

        $this->room =  new Room();
        $this->room->getRoomData($this->id);
        $this->pageTitle = "Místnost číslo ".$this->room->getRoomNumber();
    }

    protected function body(): string
    {
        return  $this->room->showRoomDetail($this->id);
    }
}


$loadedPage = new RoomDetailPage();
$loadedPage->renderPage();