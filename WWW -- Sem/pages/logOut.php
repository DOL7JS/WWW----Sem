<?php
//$_SESSION["loggedIn"] = false;
//unset($_SESSION["idUser"]);
//unset($_SESSION["email"]);
//unset($_SESSION["password"]);
//unset($_SESSION["role"]);
//unset($_SESSION["cart"]);
//unset($_SESSION["actualURL"]);
session_destroy();
header("Location:index.php?pages=homePage");
?>

