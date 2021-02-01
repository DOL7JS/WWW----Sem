<?php
class Users{
    static function printAllUsers(){//vypis formulare pro pridani uzivatele a a take vsech uzivatelu
        $conn = connection::getConnection();
        $result = $conn->query('SELECT * FROM db_dev.USER');
        echo '<div class=list>';
            echo '<div class=listRow>
                    <div class=detailsInRow >
                        <form name="addUser" class="dFlex" id="addUserForm" method="post" action="index.php?pages=usersManagement">';
                                    echo '<div>';
                                    echo '<label class="w130" >Email: </label>';
                                    echo '<input class="pRelBottom7"  name="email" type="email" >
                                          </div>';
                                    echo '<div >';
                                    echo '<label class="w130" >Heslo: </label>';
                                    echo '<input class="pRelBottom7"  name="password" type="password" >
                                </div>';
                                echo '<div>';
                                echo '<label class="w130" >Role: </label>';
                                echo '
                                <select name="role" class="pRelRight5 pRelBottom7"><option>Zákazník</option><option>Zaměstnanec</option></select>
                                </div>
                                <div >  
                                <input type="submit" value="Přidat" id="addUserButton" name="addUserButton" >
                                </div>
                        </form>
                    </div>
                </div>';
        while($row=$result->fetch_assoc()){//prochazeni a vypis vsech uzivatelu
            echo '<div class=listRow>';
            echo '<div class=detailsInRow id="detailsInRowUsers">';
            echo $row['email'];
            echo "<br>";
            echo "Role: ".$row["role"];
            echo '</div>';
            echo '<div class="btnsInList">';
            if($row["role"]!="Admin"){
                echo'<a href="index.php?pages=usersManagement&action=deleteUser&idUser='.$row['id_user']. '" ><img id="iconsUserManagement" class="w50h50" src="./imgs/icons/trash.png"></a>';
            }
            echo'<a href="index.php?pages=usersManagement&action=editUser&idUser='.$row['id_user']. '" ><img id="iconsUserManagement"  class="w50h50" src="./imgs/icons/edit.png"></a>';//TODO ikona upravy
            echo '</div>';
            echo '</div>';
        }
        echo '</div>';
    }
    static function deleteUser($idUser){//odstraneni uzivatele, jeho adres, objednaneho zbozi, objednavek
        $conn = connection::getConnection();
        $conn->query("DELETE FROM db_dev.delivery_info WHERE user_id_user= '$idUser'");
        $conn->query("DELETE FROM db_dev.ordered_goods WHERE order_id_order= (SELECT id_order FROM orders WHERE user_id_user = '$idUser')");
        $conn->query("DELETE FROM db_dev.orders WHERE user_id_user= '$idUser'");
        $conn->query("DELETE FROM db_dev.user WHERE id_user= '$idUser'");
    }
    static function addUser(){//pridani uzivatele
        $conn = connection::getConnection();
        $email = $_POST["email"];
        $password = $password = password_hash($_POST["password"], PASSWORD_DEFAULT);
        $role = $_POST["role"];
        $result = $conn->query("SELECT * FROM db_dev.USER WHERE email = '$email'");
        if($result->num_rows==0){//kontrola zda uz v systemu neni uzivatel se stejnym emailem
            $conn->query("INSERT INTO db_dev.USER (email, password, role) VALUES ('$email','$password','$role')");
        }else{
            Users::printInformation('Email je jiz obsazen');
        }
    }
    static function addDeliveryInfo(){//pridani dodaci adresy
        $conn = connection::getConnection();
        $idCustomer = $_SESSION["idUser"];
        $result = $conn->query("SELECT * FROM db_dev.delivery_info WHERE first_name='{$_POST["first_name"]}' AND last_name='{$_POST["last_name"]}' AND
          phone_number='{$_POST["phone_number"]}' AND city='{$_POST["city"]}' AND street='{$_POST["street"]}' AND home_number='{$_POST["home_number"]}'
           AND zip_code='{$_POST["zip_code"]}' AND user_id_user='{$idCustomer}'");
        if($result->num_rows<1){//kontrola zda uz dana dodaci adresa neexistuje
            $conn->query("INSERT INTO db_dev.delivery_info (first_name, last_name, phone_number, city, street, home_number, zip_code, user_id_user,saved_address) 
                                VALUES ('{$_POST["first_name"]}','{$_POST["last_name"]}','{$_POST["phone_number"]}','{$_POST["city"]}'
                                ,'{$_POST["street"]}','{$_POST["home_number"]}','{$_POST["zip_code"]}','$idCustomer',1)");
        }else{
            Users::printInformation('Adresa už je zaregistrovaná na Váš účet');
        }
    }
    static function deleteDeliveryInfo(){//odstraneni dodaci adresy
        $conn = connection::getConnection();
        $idCustomer = $_SESSION["idUser"];
        $conn->query("UPDATE db_dev.delivery_info SET saved_address=0 WHERE first_name='{$_GET["first_name"]}' AND last_name='{$_GET["last_name"]}' AND
          phone_number='{$_GET["phone_number"]}' AND city='{$_GET["city"]}' AND street='{$_GET["street"]}' AND home_number='{$_GET["home_number"]}'
           AND zip_code='{$_GET["zip_code"]}' AND user_id_user='{$idCustomer}'");

    }
    static function printAllAddresses(){//vypis formulare pro pridani adresy a nasledne vypis adres daneho uzivatele
        echo '<h1>Adresy</h1>';
        $conn = connection::getConnection();
        $result = $conn->query("SELECT * FROM db_dev.delivery_info WHERE user_id_user = {$_SESSION["idUser"]} AND saved_address=1 ORDER BY id_delivery_info DESC");
        echo '<div class=list>';
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
                    <label class="pRelLeft314">PSČ: </label><input name="zip_code" type="text" class="inputsNextLabel l344"/>
                    <label></label><input name="addAddress" value="Přidat" type="submit" class="inputsNextLabel l300"/>
                    </div>
                    </div>';

        while ($row = $result->fetch_assoc()){//prochazeni adres a jejich vypis
            echo '<div class="listRow">';
            echo '<div id="detailsInRowAdr" class="detailsInRow">';
            echo "Jméno: ".$row["first_name"]."<br>Příjmení: ".$row["last_name"]."<br>Tel.:".$row["phone_number"];
            echo "<br>Město: ".$row["city"].", ".$row["zip_code"]."<br>Ulice: ".$row["street"]."<br>Číslo popisné: ".$row["home_number"];
            echo '</div>
            <div class="btnsInList">';
              echo'<a href="index.php?pages=addresses&first_name='.$row["first_name"].'&last_name='.$row["last_name"].'&phone_number='.$row["phone_number"].'&city='.$row["city"].'&street='.$row["street"].'&home_number='.$row["home_number"].'&zip_code='.$row["zip_code"].'"><img class="w50h50" src="./imgs/icons/trash.png"></a>
                    </div>';
            echo '</div>';
        }
        echo '</div>';
    }
    static function editUser(){//uprava uzivatele
        $idUser = $_SESSION["idUser"];
        $email = $_POST["email"];
        $password = $_POST["password"];
        $role = empty($_POST["role"])?"Admin":$_POST["role"];
        $conn = connection::getConnection();
        $result = $conn->query("SELECT * FROM db_dev.user WHERE email = '$email'");
        $row = $result->fetch_assoc();
        if($result->num_rows==0){//kontrola zda uz neexistuje uzivatel s timto emailem
            if(empty($password)){//pokud je heslo prazdne, aktualizuje se pouze email a role
                $conn->query("UPDATE db_dev.user SET email = '$email' ,role = '$role'  WHERE id_user = '$idUser'");
            }else{//pokud neni heslo prazdne, aktualizuje se email, heslo a role
                $password = password_hash($_POST["password"], PASSWORD_DEFAULT);
                $conn->query("UPDATE db_dev.user SET email = '$email' ,password = '$password',role = '$role'  WHERE id_user = '$idUser'");
            }
            header("Location:index.php?pages=usersManagement");
        }else{//uzivatel s timto emailem uz existuje
            if($row["id_user"]==$idUser){//uzivatel s timto emailem je upravovany uzivatel
                if(empty($password)){
                    $conn->query("UPDATE db_dev.user SET email = '$email' ,role = '$role'  WHERE id_user = '$idUser'");
                }else{
                    $password = password_hash($_POST["password"], PASSWORD_DEFAULT);
                    $conn->query("UPDATE db_dev.user SET email = '$email' ,password = '$password',role = '$role'  WHERE id_user = '$idUser'");
                }
                header("Location:index.php?pages=usersManagement");
            }else{
                Users::printInformation('Zvolte jiný email');
            }
        }
    }
    static function login(){//prihlaseni, nastaveni udaju se $_SESSION
        $conn = connection::getConnection();
        $email = $_POST["email"];
        $stmt = $conn->query("SELECT * FROM db_dev.user WHERE email='$email'");
        if ($stmt->num_rows > 0) {
            $row = $stmt->fetch_assoc();
            if (password_verify($_POST["heslo"], $row["password"])) {
                $_SESSION["idUser"] = $row["id_user"];
                $_SESSION["loggedIn"] = true;
                $_SESSION["email"] = $row["email"];
                $_SESSION["password"] = $row["password"];
                $_SESSION["role"] = $row["role"];
                header("Location:index.php?pages=homePage");
                exit;
            } else {
                Users::printInformation('Nespravne udaje');
            }
        }
    }
    static function signUp(){//registrace
            $conn = connection::getConnection();
            if (!empty($_POST["email"]) && !empty($_POST["password"]) && !empty($_POST["passwordConfirm"])) {
                if ($_POST["password"] == $_POST["passwordConfirm"]) {//kontrola zda se hesla shoduji
                    if ($conn->connect_error) {
                        die("Connection failed: " . $conn->connect_error);
                    }
                    $result = $conn->query("SELECT * FROM db_dev.user WHERE email='{$_POST['email']}'");
                    $result->fetch_assoc();
                    if ($result->num_rows == 0) {//kontrola zda uz neexistuje uzivatel s timto emailem
                        $email = $_POST["email"];
                        $password = password_hash($_POST["password"], PASSWORD_DEFAULT);
                        $role = "Zákazník";
                        $conn->query("INSERT INTO db_dev.user (email, password,role) VALUES ('$email', '$password','$role')");
                        echo $password;
                        header("Location:index.php?pages=homePage");
                    } else {
                        Users::printInformation('Email už je používán');
                    }
                } else {
                    Users::printInformation('Hesla nejsou shodná');
                }
            } else {
                Users::printInformation('Nevyplnil jste email nebo heslo');
            }
            $conn->close();
    }
    static function printEditUser(){//vypis informaci k upravovanemu uzivateli
        echo '<h1>Úprava účtu</h1>';
        $conn = connection::getConnection();
        $idUser = $_SESSION["idUser"];
        $result = $conn->query("SELECT * FROM db_dev.user WHERE id_user = '$idUser'");
        if($result->num_rows>0){
            $row = $result->fetch_assoc();
            echo '<form id="editUserForm" action="index.php?pages=editUser&action=edited" method="post" class="pRelLeft40p accountBox">
                    <div><label>Email: </label><input type="email" name="email" placeholder="Email . . ."  value="'.$row["email"]. '" ></div>
                    <div><label>Heslo: </label><input type="password" name="password"  placeholder="Heslo . . ."></div>';
            echo '<div><label>Role: </label>';
            if($row["role"]=="Admin"){
                echo '<select disabled name="role">';
            }else{
                echo '<select name="role">';
            }
            if(($row["role"])=="Zákaznik"){
                echo '<option selected>Zákazník</option>
                  <option>Zaměstnanec</option>
                  <option>Admin</option>';
            }else if(($row["role"])=="Zaměstnanec") {
                echo '<option >Zákazník</option>
                  <option selected>Zaměstnanec</option>
                  <option>Admin</option>';
            }else if(($row["role"])=="Admin"){
                echo '
                <option>Zákazník</option>
                <option>Zaměstnanec</option>
                <option selected>Admin</option>';
            }else{
                echo '
                <option>Zákazník</option>
                <option>Zaměstnanec</option>
                <option>Admin</option>';
            }
            echo '</select></div>';
            echo '<div><label></label><input type="submit" value="Potvrdit" name=""></div>
    </form>';
        }
    }
    static function printFilledNameAddress(){//vypis formulare platebni metody, zpusobu doruceni a dodaci adresy
            echo '<form action="index.php?pages=payment" method="post" class="pRelLeft700">
            <div class="paymentBox">
            Způsob doručení:    
            <div>
            <label class="w170">Česká pošta Balík do ruky</label><input type="radio" name="deliveryMethod" value="1" class="radioButton"">
            </div>
            <div>
            <label>GLS</label><input type="radio" name="deliveryMethod" value="2" class="radioButton">
            </div>
            <div>
            <label>PPL</label><input type="radio" name="deliveryMethod" value="3" class="radioButton">
            </div>
            </div>
            <div class="paymentBox">
            Způsob platby:
            <div>
            <label >Platba kartou</label><input type="radio" name="paymentMethod" value="1" class="radioButton">
            </div>
            <div>
            <label >Dobírka</label><input type="radio" name="paymentMethod" value="2" class="radioButton">
            </div>
            </div>
            <div class="paymentBox">';
        if(!empty($_POST["addressOption"])){//pokud jsme vybrali adresu, sama se nam vyplni do fieldu
            $conn = connection::getConnection();
            $result = $conn->query("SELECT * FROM db_dev.delivery_info WHERE id_delivery_info = {$_POST["addressOption"]}");
            $row = $result->fetch_assoc();
            echo '
            <div>
            <label>Jméno: </label><input type="text" name="first_name" value="'.$row["first_name"].'">
            </div>
            <div>
            <label>Příjmení: </label><input type="text" name="last_name" value="'.$row["last_name"].'">
            </div>
            <div>
            <label>Telefonní číslo: </label><input type="text" name="phone_number" value="'.$row["phone_number"].'">
            </div>
            <div>
            <label>Město: </label><input type="text" name="city" value="'.$row["city"].'">
            </div>
            <div>
            <label>Ulice: </label><input type="text" name="street" value="'.$row["street"].'">
            </div>
            <div>
            <label>Číslo popisné: </label><input type="text" name="home_number" value="'.$row["home_number"].'">
            </div>
            <div>
            <label>PSČ: </label><input type="text" name="zip_code" value="'.$row['zip_code'].'">
            </div>';
        }else{//pokud jsme adresu nevybrali, fieldy budou prazdne
            echo '
            <div>
            <label>Jméno: </label><input type="text" name="first_name">
            </div>
            <div>
            <label>Příjmení: </label><input type="text" name="last_name">
            </div>
            <div>
            <label>Telefonní číslo: </label><input type="text" name="phone_number" >
            </div>
            <div>
            <label>Město: </label><input type="text" name="city" >
            </div>
            <div>
            <label>Ulice: </label><input type="text" name="street">
            </div>
            <div>
            <label>Číslo popisné: </label><input type="text" name="home_number">
            </div>
            <div>
            <label>PSČ: </label><input type="text" name="zip_code">
            </div>';
        }
        echo '<div id = "saveAdressInfo">
                    <label class="w300">Uložit info pro další objednávku? </label>
                    <input id="checkBoxSaveAddress" class="pRelTop10" type="checkbox" name="save_delivery_info">
            </div>
            <div>
                <form method="post" name="address" action="index.php?pages=payment">
                    <label>Vyplnit adresu: </label>
                    <select onchange="this.form.submit()"  name="addressOption">
                        <option disabled selected>Vyberte adresu</option>';
                        $conn = connection::getConnection();
                        $result = $conn->query("SELECT * FROM db_dev.delivery_info WHERE user_id_user = {$_SESSION["idUser"]} AND saved_address=1");
                        while ($row = $result->fetch_assoc()) {//prochazeni adres k vyplneni
                            if(!empty($_POST["addressOption"])&&$_POST["addressOption"]==$row["id_delivery_info"]){
                                echo '<option selected value=' . $row["id_delivery_info"] . ' >' . $row["city"] . '</option>';
                            }else{
                                echo '<option  value=' . $row["id_delivery_info"] . ' >' . $row["city"] . '</option>';
                            }
                        }
            echo '</select>
            </div>
        </div>
            <div>
                <input type="submit" value="Objednat" id="orderButton" name="orderButton">
            </div>
        </div>
                </form>
    </form>';
    }

    static function printInformation($textInfo){//vyskakovaci okno
        echo "<script type='text/javascript'>alert('$textInfo');</script>";
    }


}
?>