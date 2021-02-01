<?php
if(!empty($_GET["action"])&&$_GET["action"]=="edited"){
    Users::editUser();
}
if(!empty($_SESSION["idUser"])){
    Users::printEditUser();
}

?>