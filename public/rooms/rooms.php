<?php

require_once __DIR__ . "/../../autoloader/autoloader.php";

session_start();

class  RoomListPage extends  Page{

    private  array $data;
    private Table $table;

    public function __construct()
    {
        $this->pageTitle="Prohlížeč místností";
    }

    protected function prepare(): void
    {
        if(Database::getNumberOfRows("SELECT 1 FROM room") <= 0)  {
            self::showError(self::ERROR_NOTFOUND);
            return;;
        }

        $this->data = Database::fetch("SELECT *  from room");
        $this->table = new Table();
    }

    protected function body()
    {
        return $this->table->buildRoomTable(array("Název", "Číslo", "Telefon"),"Úprava",$this->data);
    }
}

$loadedPage = new RoomListPage();
$loadedPage->renderPage();
