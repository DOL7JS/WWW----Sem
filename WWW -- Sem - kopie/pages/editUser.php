<?php
if(!empty($_GET["action"])&&$_GET["action"]=="edited"){
    if(UserControl::editUser($_SESSION["idUserEdit"],$_POST["email"],$_POST["password"],$_POST["role"])){
        $_SESSION["userEdited"] = 'true';
    }else{
        $_SESSION["userEdited"] = 'false';
    }
    unset($_SESSION["idUserEdit"]);
    header("Location:index.php?pages=usersManagement");//prokliknuti na upravu uzivatele
}
if(!empty($_GET["idUser"])){
    AdminControl::printEditUserAsAdmin($_GET["idUser"]);
    $_SESSION["idUserEdit"] = $_GET["idUser"];
}

?>