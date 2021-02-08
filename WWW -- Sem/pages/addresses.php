<?php

if(!empty($_POST["addAddress"])&&ValidityChecker::checkValidityAddAddresses()){
    UserControl::addDeliveryInfo($_POST["first_name"],$_POST["last_name"],$_POST["phone_number"],$_POST["city"],$_POST["street"],
        $_POST["home_number"],$_POST["zip_code"],$_SESSION["idUser"]);
}
if(!empty($_GET["action"])&&$_GET["action"]=="deleteAddress"){
    UserControl::deleteDeliveryInfo($_GET["first_name"],$_GET["last_name"],$_GET["phone_number"],$_GET["city"],$_GET["street"],$_GET["home_number"],$_GET["zip_code"],$_SESSION["idUser"]);
    header("Location:index.php?pages=addresses");
}
echo '<div class=list>';
UserControl::printFormAddAddress();
UserControl::printAllAddressesOfUser();
echo '</div>';
?>
