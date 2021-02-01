<?php
echo '<h1>Správa uživatelů</h1>';

if(!empty($_POST["addUserButton"])){
    ValidityChecker::checkValidityUsers();
}

if(!empty($_GET["action"])&&$_GET["action"]=="deleteUser"){
    Users::deleteUser($_GET["idUser"]);//odstraneni uzivatele
    Users::printInformation('Uživatel odstraněn');
}
if(!empty($_GET["action"])&&$_GET["action"]=="editUser"){
    $_SESSION["idUser"] = $_GET["idUser"];
    header("Location:index.php?pages=editUser");//prokliknuti na upravu uzivatele
}

if(!empty($_POST["email"])&&!empty($_POST["password"])&&!empty($_POST["role"])){
    Users::addUser();//pridani uzivatele
    Users::printInformation('Uživatel přidán');
}
Users::printAllUsers();
?>