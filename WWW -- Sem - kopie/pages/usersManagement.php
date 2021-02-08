<?php
echo '<h1>Správa uživatelů</h1>';
if(!empty($_SESSION["userEdited"])){
    if($_SESSION["userEdited"]=='true'){
        UserControl::printInformation('Uživatel upraven');
    }else{
        UserControl::printInformation('Uživatel s tímto emailem existuje');
    }
    unset($_SESSION["userEdited"]);
}
if(!empty($_GET["action"])&&$_GET["action"]=="deleteUser"){
    UserControl::deleteUser($_GET["idUser"]);
    UserControl::printInformation('Uživatel odstraněn');
    header("Location:index.php?pages=usersManagement");//prokliknuti na upravu uzivatele

}
if(!empty($_GET["action"])&&$_GET["action"]=="editUser"){
    header("Location:index.php?pages=editUser&idUser=".$_GET["idUser"]);//prokliknuti na upravu uzivatele
}

if(!empty($_POST["addUserButton"])&&ValidityChecker::checkValidityUsers()){
    if(UserControl::addUser($_POST["email"],$_POST["password"],$_POST["role"])){
        UserControl::printInformation('Uživatel přidán');
    }
}
echo '<div class=list>';
    AdminControl::printFormAddUser();
    AdminControl::printAllUsersAsAdmin();
echo '</div>';
?>