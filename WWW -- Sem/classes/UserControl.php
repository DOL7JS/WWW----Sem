<?php


class UserControl
{
    public static function addUser($email,$password,$role){//pridani uzivatele
        $emailCheck = UserDB::checkEmail($email);
        if($emailCheck==null){//kontrola zda uz v systemu neni uzivatel se stejnym emailem
            UserDB::addUser($email,password_hash($password, PASSWORD_DEFAULT),$role);
            return true;
        }else{
            UserControl::printInformation('Email je jiz obsazen');
            return false;
        }
    }
    public static function deleteUser($idUser){//odstraneni uzivatele, jeho adres, objednaneho zbozi, objednavek
        $orders = OrderDB::selectOrdersOfUser($idUser);
        if($orders!=null){
            GoodsDB::deleteOrderedGoodsByOrder($orders["id_order"]);
            OrderDB::deleteOrdersByUser($idUser);
        }
        UserDB::deleteUser($idUser);
    }
    public static function editUser($idUser,$email,$password,$role){//uprava uzivatele
        $user = UserDB::checkEmail($email);
        if($user==null||$user["id_user"]==$idUser){//kontrola zda uz neexistuje uzivatel s timto emailem
            if(empty($password)){//pokud je heslo prazdne, aktualizuje se pouze email a role
                UserDB::updateUserEmailRole($idUser,$email,$role);
            }else{//pokud neni heslo prazdne, aktualizuje se email, heslo a role
                UserDB::updateUserEmailPasswordRole($idUser,$email,password_hash($_POST["password"], PASSWORD_DEFAULT),$role);
            }
            return true;
        }
        return false;
    }

    public static function isUserCustomer(){
        return $_SESSION["role"]=="Zákazník";
    }

    static function login(){//prihlaseni, nastaveni udaju se $_SESSION
        $user = UserDB::checkEmail($_POST["email"]);
        if(!empty($user)&&password_verify($_POST["heslo"], $user["password"])){
            $_SESSION["idUser"] = $user["id_user"];
            $_SESSION["loggedIn"] = true;
            $_SESSION["email"] = $user["email"];
            $_SESSION["password"] = $user["password"];
            $_SESSION["role"] = $user["role"];
            header("Location:index.php?pages=homePage");
            exit;
        } else {
            UserControl::printInformation('Nespravne udaje');
        }
    }
    public static function signUp()
    {
        if ($_POST["password"] == $_POST["passwordConfirm"]) {//kontrola zda se hesla shoduji
            $user = UserDB::checkEmail($_POST["email"]);
            if ($user==null) {//kontrola zda uz neexistuje uzivatel s timto emailem
                $email = $_POST["email"];
                $password = password_hash($_POST["password"], PASSWORD_DEFAULT);
                $role = "Zákazník";
                UserDB::addUser($email,$password,$role);
                header("Location:index.php?pages=logIn");
            } else {
                UserControl::printInformation('Email už je používán');
            }
        } else {
            UserControl::printInformation('Hesla nejsou shodná');
        }
    }
    //------------PRINT_USER-------------
    static function printProfileInfo(){
        echo '<div class="accountBox">';
        echo 'Email: '.$_SESSION["email"];
        echo '<br>';
        echo 'Role: '.$_SESSION["role"];
        if(self::isUserCustomer()){//pokud je role zákazník, může se prokliknout na své dodací adresy
            echo '<form action="index.php?pages=addresses" method="post" >';
                echo '<div><input value="Adresy" type="submit" name="show_delivery_info"></div>';
            echo '</form>';
        }
        echo '</div>';
    }

    static function printAllAddressesOfUser(){
        $addresses = UserDB::getAddressesOfUser($_SESSION["idUser"]);
        foreach ($addresses as $row){//prochazeni adres a jejich vypis
            echo '<div class="listRow">
                      <div class="detailsInRow detailsInRowAdr">';
                        echo "Jméno: ".$row["first_name"]."<br>Příjmení: ".$row["last_name"]."<br>Tel.:".$row["phone_number"];
                        echo "<br>Město: ".$row["city"].", ".$row["zip_code"]."<br>Ulice: ".$row["street"]."<br>Číslo popisné: ".$row["home_number"];
                        echo '</div>
                            <div id="btnInAddresses" class="btnsInList">';
                        echo'<a href="index.php?pages=addresses&first_name='.$row["first_name"].'&last_name='.$row["last_name"].'&phone_number='.$row["phone_number"].'&city='.$row["city"].'&street='.$row["street"].'&home_number='.$row["home_number"].'&zip_code='.$row["zip_code"].'"><img class="w50h50" src="./imgs/icons/trash.png"></a>
                      </div>
                  </div>';
        }
    }
    public static function printFormAddAddress(){
        echo '<div class=listRow>';
        echo '<form action="" method="post" >
                    <div>
                    <label>Jméno: </label><input name="first_name" type="text" class="inputsNextLabel"/>
                    <label>Město: </label><input name="city" type="text" class="inputsNextLabel"/>
                    </div>
                    <div>
                    <label>Příjmení: </label><input name="last_name" type="text" class="inputsNextLabel"/>
                    <label>Ulice: </label><input name="street" type="text" class="inputsNextLabel"/>
                    </div>
                    <div>
                    <label>Telefonní číslo: </label><input name="phone_number" type="number" class="inputsNextLabel"/>
                    <label>Číslo popisné: </label><input name="home_number" type="number" class="inputsNextLabel"/>
                    </div>
                    <div>
                    <label id="labelZip_code">PSČ: </label><input id="zip_code" name="zip_code" type="text" class="inputsNextLabel"/>
                    <label></label><input name="addAddress" value="Přidat" type="submit" id="addAddress" class="inputsNextLabel"/>
                    </div>
                    </div>';
    }
    public static function addDeliveryInfo(){
        if(UserDB::checkUserUniqueAddress($_POST["first_name"],$_POST["last_name"],$_POST["phone_number"],$_POST["city"],$_POST["street"],
            $_POST["home_number"],$_POST["zip_code"],$_SESSION["idUser"])){//"JE UNIQUE";
            UserDB::insertAddressToDeliveryInfo($_POST["first_name"],$_POST["last_name"],$_POST["phone_number"],$_POST["city"],
                $_POST["street"], $_POST["home_number"],$_POST["zip_code"],1);
            UserDB::insertIdAddressToUserDeliveryInfo(UserDB::selectIdOfLastAddedDeliveryInfo(),$_SESSION["idUser"]);

        }else{//"Neni UNIQUE";
            UserDB::updateAddressStatus($_POST["first_name"],$_POST["last_name"],$_POST["phone_number"],$_POST["city"],$_POST["street"],
                $_POST["home_number"],$_POST["zip_code"],$_SESSION["idUser"],1);
        }
    }

    public static function deleteDeliveryInfo(){//odstraneni dodaci adresy
        UserDB::updateAddressStatus($_GET["first_name"],$_GET["last_name"],$_GET["phone_number"],$_GET["city"],$_GET["street"],$_GET["home_number"],$_GET["zip_code"],$_SESSION["idUser"],0);
    }

    public static function printPaymentBoxes(){
        echo '<form action="index.php?pages=payment" method="post" class="pRelLeft700">';
        self::printDeliveryBox();
        self::printPaymentBox();
        self::printFilledAddress();
        echo '<div>
                <input type="submit" value="Objednat" id="orderButton" name="orderButton">
            </div>
        </form>';
    }

    private static function printDeliveryBox()
    {
        echo '
            <div class="paymentBox">
            Způsob doručení:    
            <div>
            <label class="w170">Česká pošta Balík do ruky</label><input type="radio" name="deliveryMethod" value="1" class="radioButton" '; if(!empty($_POST["deliveryMethod"])){echo 'checked';}echo '>
            </div>
            <div>
            <label>GLS</label><input type="radio" name="deliveryMethod" value="2" class="radioButton" '; if(!empty($_POST["deliveryMethod"])){echo 'checked';}echo ' >
            </div>
            <div>
            <label>PPL</label><input type="radio" name="deliveryMethod" value="3" class="radioButton" '; if(!empty($_POST["deliveryMethod"])){echo 'checked';}echo '>
            </div>
            </div>';
    }

    private static function printPaymentBox()
    {
            echo '<div class="paymentBox">
            Způsob platby:
            <div>
            <label >Platba kartou</label><input type="radio" name="paymentMethod" value="1" class="radioButton" '; if(!empty($_POST["paymentMethod"])){echo 'checked';}echo '>
            </div>
            <div>
            <label >Dobírka</label><input type="radio" name="paymentMethod" value="2" class="radioButton" '; if(!empty($_POST["paymentMethod"])){echo 'checked';}echo '>
            </div>
            </div>';
    }

    private static function printFilledAddress()
    {
        echo '<div class="paymentBox">';
        $row = "";
        if(!empty($_POST["addressOption"])) {//pokud jsme vybrali adresu, sama se nam vyplni do fieldu
            $row = UserDB::selectAddressById($_POST["addressOption"]);
        }
            echo '
            <div>
            <label>Jméno: </label><input type="text" name="first_name" ';if(!empty($row)){echo 'value="'.$row["first_name"].'"';}echo '>
            </div>
            <div>
            <label>Příjmení: </label><input type="text" name="last_name"  ';if(!empty($row)){echo 'value="'.$row["last_name"].'"';}echo '>
            </div>
            <div>
            <label>Telefonní číslo: </label><input type="text" name="phone_number"  ';if(!empty($row)){echo 'value="'.$row["phone_number"].'"';}echo '>
            </div>
            <div>
            <label>Město: </label><input type="text" name="city"  ';if(!empty($row)){echo 'value="'.$row["city"].'"';}echo '>
            </div>
            <div>
            <label>Ulice: </label><input type="text" name="street"  ';if(!empty($row)){echo 'value="'.$row["street"].'"';}echo '>
            </div>
            <div>
            <label>Číslo popisné: </label><input type="text" name="home_number"  ';if(!empty($row)){echo 'value="'.$row["home_number"].'"';}echo '>
            </div>
            <div>
            <label>PSČ: </label><input type="text" name="zip_code"  ';if(!empty($row)){echo 'value="'.$row["zip_code"].'"';}echo '>
            </div>';
            self::printSaveAddressSelect();
            echo '</div>';
    }

    private static function printSaveAddressSelect()
    {
        echo '<div id="saveAdressInfo">
                    <label class="w300">Uložit info pro další objednávku? </label>
                    <input id="checkBoxSaveAddress" class="pRelTop10" type="checkbox" name="save_delivery_info">
            </div>
            <div>
                    <label>Vyplnit adresu: </label>
                    <select onchange="this.form.submit()"  name="addressOption">
                        <option disabled selected>Vyberte adresu</option>';
       $result = UserDB::getAddressesOfUser($_SESSION["idUser"]);
        foreach ($result as $row) {//prochazeni adres k vyplneni
            if(!empty($_POST["addressOption"])&&$_POST["addressOption"]==$row["id_delivery_info"]){
                echo '<option selected value=' . $row["id_delivery_info"] . ' >' . $row["city"] . '</option>';
            }else{
                echo '<option  value=' . $row["id_delivery_info"] . ' >' . $row["city"] . '</option>';
            }
        }
        echo '</select></div>
        </div>';
    }

    public static function printFormAddUser()
    {
            echo '<div class=listRow>
                    <div class=detailsInRow >
                        <form name="addUser" class="dFlex" id="addUserForm" method="post" action="index.php?pages=usersManagement">
                                    <div>
                                    <label class="w130" >Email: </label>
                                    <input class="pRelBottom7"  name="email" type="email" >
                                          </div>
                                    <div >
                                    <label class="w130" >Heslo: </label>
                                    <input class="pRelBottom7"  name="password" type="password" >
                                </div>
                                <div>
                                <label class="w130" >Role: </label>
                                <select name="role" class="pRelRight5 pRelBottom7"><option>Zákazník</option><option>Zaměstnanec</option></select>
                                </div>
                                <div >  
                                <input type="submit" value="Přidat" id="addUserButton" name="addUserButton" >
                                </div>
                        </form>
                    </div>
                </div>';
    }

    public static function printAllUsersAsAdmin()
    {
        $users = UserDB::getAllUsers();
        foreach($users as $user){//prochazeni a vypis vsech uzivatelu
            echo '<div class=listRow>';
            echo '<div class="detailsInRow detailsInRowUsers">';
            echo $user['email'];
            echo "<br>";
            echo "Role: ".$user["role"];
            echo '</div>';
            echo '<div id="btnsInUsersManagement" class="btnsInList">';
            if($user["role"]!="Admin"){
                echo'<a href="index.php?pages=usersManagement&action=deleteUser&idUser='.$user['id_user']. '" ><img class="w50h50 iconsUserManagement" src="./imgs/icons/trash.png" alt="trash.png"></a>';
            }
            echo'<a href="index.php?pages=usersManagement&action=editUser&idUser='.$user['id_user']. '" ><img class="w50h50 iconsUserManagement" src="./imgs/icons/edit.png" alt="edit.png"></a>';//TODO ikona upravy
            echo '</div>';
            echo '</div>';
        }
    }

    static function printEditUser($idUser){//vypis informaci k upravovanemu uzivateli
        echo '<h1>Úprava účtu</h1>';
        $user = UserDB::selectUserById($idUser);
        if($user!=null){
            echo '<form id="editUserForm" action="index.php?pages=editUser&action=edited" method="post" class="pRelLeft40p accountBox">
                    <div><label>Email: </label><input type="email" name="email" placeholder="Email . . ."  value="'.$user["email"]. '" ></div>
                    <div><label>Heslo: </label><input type="password" name="password"  placeholder="Heslo . . ."></div>
                    <div><label>Role: </label>';
            if($user["role"]=="Admin"){
                echo '<select disabled name="role">';
            }else{
                echo '<select name="role">';
            }
            switch ($user["role"]){
                case "Zákazník":
                    echo '<option selected>Zákazník</option>
                          <option>Zaměstnanec</option>
                          <option>Admin</option>';break;
                case "Zaměstnanec":
                    echo '<option >Zákazník</option>
                          <option selected>Zaměstnanec</option>
                          <option>Admin</option>';break;
                case "Admin":
                    echo '<option>Zákazník</option>
                          <option>Zaměstnanec</option>
                          <option selected>Admin</option>';break;
            }
            echo '</select></div>
               <div><label></label><input type="submit" value="Potvrdit"></div></form>';
        }
    }
    static function printInformation($textInfo){//vyskakovaci okno
        echo "<script type='text/javascript'>alert('$textInfo');</script>";
    }


}