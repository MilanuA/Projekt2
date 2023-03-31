<?php
require_once __DIR__ . "/../../autoloader/autoloader.php";

session_start();

class RemoveRoomPage extends  Page{

    protected function prepare(): void
    {
        if(!Room::checkLoggin())
        self::redirectTo("/../rooms/rooms.php", false, Page::ERROR_NOTADMIN);

        $id = filter_input(INPUT_POST, 'roomId', FILTER_VALIDATE_INT);
        $rowCount = Database::getNumberOfRows("SELECT 1 FROM employee WHERE room= :id", ["id" => $id]);
        
        if($rowCount > 0)   Page::redirectTo("/../rooms/rooms.php", false, Page::ACTION_HOMEROOM);

        $room = new Room();
        $room->deleteRoom($id);
        Page::redirectTo("/../rooms/rooms.php", true, Page::ACTION_REMOVE);
    }

    protected function body()
    {
       return "";
    }
}

$loadedPage = new RemoveRoomPage();
$loadedPage->renderPage();