<?php

class EmployeeForm
{
    public  static function addNewEmployeeForm($errors = [], $data = [], $formAction, $btnName, $isUpdating) : string {

        $roomData = Database::fetch("SELECT room_id, name, no FROM room");

        $nameError =  array_key_exists('name', $errors)? "<div class='d-block invalid-feedback'>".$errors["name"]."</div>" : "";
        $surnameError =  array_key_exists('surname', $errors) ? "<div class='d-block invalid-feedback'>".$errors["surname"]."</div>  " : "";
        $jobError = array_key_exists('job', $errors) != null ? " <div class='d-block invalid-feedback'>".$errors["job"]."</div>" : "";
        $roomError = array_key_exists('room', $errors) != null ? " <div class='d-block invalid-feedback'>".$errors["room"]."</div>" : "";
        $wageError =   array_key_exists('wage', $errors) != null ? " <div class='d-block invalid-feedback'>".$errors["wage"]."</div>" : "";
        $loginError =   array_key_exists('login', $errors) != null ? " <div class='d-block invalid-feedback'>".$errors["login"]."</div>" : "";
        $passwordError =   array_key_exists('password', $errors) != null ? " <div class='d-block invalid-feedback'>".$errors["password"]."</div>" : "";

        $isAdmin = $data['isAdmin'] == 1 ? "checked": "";

        $logged = $_SESSION["userID"] == $data['id'];


        $returnString = " <div class='container'> 
                <form action='".$formAction."' method='post'>      
              <div class='form-group'>
                       <input type='hidden' name='humanId' value='" .$data['id']."'>
                         <input type='hidden' name='isUpdating' value='" .$isUpdating."'>
                <label>Jméno</label>
                <input type='text' class='form-control' name='name' placeholder='Jméno' maxlength='30' value='".$data['name']."' > ".$nameError."
              </div>
              <div class='form-group'>
                <label>Přijmení</label>
                <input type='text' class='form-control' name='surname' placeholder='Přijmení' maxlength='30' value='".$data["surname"]."'>   ".$surnameError."
                
                
              </div>
              <div class='form-group'>
                <label>Pozice</label>
                <input type='text' class='form-control' name='job' placeholder='Pozice' maxlength='30' value='".$data["job"]."'> ".$jobError."
              </div>
              <div class='form-group'>
                <label>Mzda</label>
                <input type='number' class='form-control' name='wage' placeholder='Mzda'  value='".$data["wage"]."'> ".$wageError."
                   
              </div>";

        $returnString .= " <div class='form-group'>
                        <label>Místnost</label>
                       
                       <select class='form-control' name='room'>
                       <option value='' disabled selected>Výběr místnosti</option>";

        for ($i = 0; $i < count($roomData); $i++){
            $isSelected = $roomData[$i]->room_id == $data["room"] ? "selected" : "";
            $returnString .= "<option value='".$roomData[$i]->room_id."' ".$isSelected." >".$roomData[$i]->name." (".$roomData[$i]->no.") </option>";
        }
        $returnString .= "</select>  ". $roomError." </div>";


        $returnString .= " <div class='form-group'>
                        <label>Klíče - prosím, držte ctrl při výběru 🐛</label>
                         <select multiple class='form-control' name='keys[]'>";

        for ($i = 0; $i < count($roomData); $i++){
            $isSelected = in_array($roomData[$i]->room_id, $data["keys"]) ? "selected" : "";

            $returnString .= "<option value='".$roomData[$i]->room_id." '".$isSelected." >".$roomData[$i]->name." (".$roomData[$i]->no.") </option>";
        }


        $returnString .= " </select></div>";

        $returnString .= " 
               <div class='form-group'>
                <label>Login</label>
                <input type='text' class='form-control' name='login' placeholder='Login' maxlength='30' value='".$data["login"]."'> ".$loginError."               
               </div> ";

        if(!$logged){
            $returnString .= "<div class='form-group'>
                <label>Nové heslo</label>
                <input type='password' class='form-control' name='password' placeholder='Password' maxlength='30'>     ".$passwordError."           
              </div>
              
                <div class='form-check'>
               <input class='form-check-input' name='isAdmin' type='checkbox'  value='1' ".$isAdmin." >
              <label class='form-check-label' for='flexCheckChecked'>   Je adminem   </label> </div>";
        }

        $returnString .= "        
              <button type='submit' class='btn btn-primary'>".$btnName."</button>
            </form> 
            <a href='/../employees/employees.php' class='btn btn-primary'>Vrátit se</a>
            </div>";

        return $returnString;
    }
}