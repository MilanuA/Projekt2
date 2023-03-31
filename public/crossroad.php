<?php

    require_once __DIR__ . "/../autoloader/autoloader.php";

    session_start();

class CrossroadPage extends Page
{
    public function __construct()
    {
        $this->pageTitle = "Prohlížeč";
    }

    protected function body(): string
    {
        return'
        <div class="container">
            <h1>Prohlížec databáze</h1>
            <ul class="list-group">
                <li class="list-group-item">  <a href="employees/employees.php">Seznam zaměstnanců</a>    </li>
                <li class="list-group-item"> <a href="rooms/rooms.php">Seznam místností</a>  </li>
                <li class="list-group-item"> <a href="editPassword.php">Upravit heslo</a> </li>
                <li class="list-group-item"> <a href="logout.php">Odhlásit se</a> </li>
            </ul>
        </div>';
    }
}

$loadedPage = new CrossroadPage();
$loadedPage->renderPage();

