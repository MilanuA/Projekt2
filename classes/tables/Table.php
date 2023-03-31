<?php

class Table
{

    public function buildEmployeeTable($headers, $specialHeader, $data) : string {

        $buttonEditable = $_SESSION["isAdmin"] == 1 ? "" : "disabled";

        $tableString =  "<div class='container'> <table class='table'> <tr>";

        foreach ($headers as $header)
            $tableString .= "<th>$header</th>";


        $tableString .= "<th>$specialHeader</th></tr>";

        for ($i = 0; $i < count($data); $i++) {
            $loggedAcc = $data[$i]->zamestnanecID == $_SESSION["userID"];
            $removeButtonEditable = $_SESSION["isAdmin"] == 1 ? $loggedAcc ?  "disabled" : "" : "disabled";

           $tableString .= "<tr> <td> <a href='employeeDetail.php?humanID=".$data[$i]->zamestnanecID."'> ". $data[$i]->prijmeni ." ".$data[$i]->jmeno ."</a></td><td>".$data[$i]->mistnost."</td> <td>".$data[$i]->telefon."</td> <td>".$data[$i]->pozice."</td>";
           $tableString .= "<td>
            <form action='updateEmployee.php' method='get' style='display: inline-block'>
                <input type='hidden' name='humanId' value='".$data[$i]->zamestnanecID."'>
                <input type='submit' value='Upravit' class='btn btn-info' ".$buttonEditable.">
            </form>
            <form action='deleteEmployee.php' method='post' style='display: inline-block'>
                <input type='hidden' name='humanId' value='".$data[$i]->zamestnanecID."'>
                <input type='submit' value='Smazat' class='btn btn-danger' onclick='return confirm('Opravdu chcete smazat místnost?\nAkce je nevratná!');' ".$removeButtonEditable.">
            </form>
            </td> </tr>";
        }


        $tableString .= "</table> <a href='/../crossroad.php' class='btn btn-primary'>Vrátit se</a> <a href='/../employees/addNewEmployee.php' class='btn btn-primary ".$buttonEditable."' >Přidat nového člena</a> </div> ";

       return $tableString;
    }

    public function  buildRoomTable($headers, $specialHeader, $data) : string {
        $buttonEditable = $_SESSION["isAdmin"] == 1 ? "" : "disabled";

        $tableString =  "<div class='container'> <table class='table'> <tr>";

        foreach ($headers as $header)
            $tableString .= "<th>$header</th>";

        $tableString .= "<th>$specialHeader</th></tr>";

        for ($i = 0; $i < count($data); $i++) {
            $tableString .= "<tr> <td> <a href='roomDetail.php?roomID=".$data[$i]->room_id."'> ".$data[$i]->name."</a></td><td>".$data[$i]->no."</td> <td>".$data[$i]->phone."</td>";
            $tableString .= "<td>
            <form action='roomUpdate.php' method='get' style='display: inline-block'>
                <input type='hidden' name='roomId' value='".$data[$i]->room_id."'>
                <input type='submit' value='Upravit' class='btn btn-info' ".$buttonEditable.">
            </form>
            <form action='deleteRoom.php' method='post' style='display: inline-block'>
                <input type='hidden' name='roomId' value='".$data[$i]->room_id."'>
                <input type='submit' value='Smazat' class='btn btn-danger' onclick='return confirm('Opravdu chcete smazat místnost?\nAkce je nevratná!');' ".$buttonEditable.">
            </form>
            </td> </tr>";
        }

        $tableString .= "</table><a href='/../crossroad.php' class='btn btn-primary'>Vrátit se</a> <a href='/../rooms/addNewRoom.php' class='btn btn-primary ".$buttonEditable."' >Založit novou místnost</a>  </div> ";

        return $tableString;
    }
}