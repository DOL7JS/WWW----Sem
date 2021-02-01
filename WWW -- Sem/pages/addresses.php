<?php

if(!empty($_POST["addAddress"])){
    ValidityChecker::checkValidityAddresses();//kontrola zda jsou vyplněny potřebné fieldy
}
if(!empty($_POST["first_name"])&&!empty($_POST["last_name"])&&!empty($_POST["phone_number"])
    &&!empty($_POST["city"])&&!empty($_POST["street"])&&!empty($_POST["home_number"])&&!empty($_POST["zip_code"])){
    Users::addDeliveryInfo();//přidání dodací adresy
}
if(!empty($_GET["first_name"])&&!empty($_GET["last_name"])&&!empty($_GET["phone_number"])
    &&!empty($_GET["city"])&&!empty($_GET["street"])&&!empty($_GET["home_number"])&&!empty($_GET["zip_code"])){
    Users::deleteDeliveryInfo();//odebrání dodací adresy
    header("Location:index.php?pages=addresses");
}
Users::printAllAddresses();
?>
