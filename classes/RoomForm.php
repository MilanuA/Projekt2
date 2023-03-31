<?php

class RoomForm
{
    public  static function addNewRoomForm($errors = [], $data = [], $formAction, $btnName) : string {

        $nameError =  array_key_exists('name', $errors)? "<div class='d-block invalid-feedback'>".$errors["name"]."</div>" : "";
        $numberError =  array_key_exists('no', $errors) ? "<div class='d-block invalid-feedback'>".$errors["no"]."</div>  " : "";
        $phoneError = array_key_exists('phone', $errors) != null ? " <div class='d-block invalid-feedback'>".$errors["phone"]."</div>" : "";


        return " <div class='container'> 
                <form action='".$formAction."' method='post'>      
              <div class='form-group'>
                       <input type='hidden' name='roomId' value='" .$data['id']."'>
                <label>Jméno</label>
                <input type='text' class='form-control' name='name' placeholder='Jméno' maxlength='30' value='".$data["name"]."' > ".$nameError."
              </div>
              <div class='form-group'>
                <label>Číslo</label>
                <input type='number' class='form-control' name='no' placeholder='Číslo' value='".$data["no"]."'>   ".$numberError."
              </div>
              <div class='form-group'>
                <label>Telefon</label>
                <input type='text' class='form-control' name='phone' placeholder='Telefon' maxlength='30'  value='".$data["phone"]."'> ".$phoneError."         
              </div>  
              <button type='submit' class='btn btn-primary'>".$btnName."</button>
            </form> 
            <a href='/../rooms/rooms.php' class='btn btn-primary'>Vrátit se</a>
            </div>";
    }
}