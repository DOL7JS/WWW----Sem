<?php

if(!empty($_POST["addAddress"])&&ValidityChecker::checkValidityAddresses()){
    UserControl::addDeliveryInfo();
}
if(!empty($_GET["first_name"])&&!empty($_GET["last_name"])&&!empty($_GET["phone_number"])
    &&!empty($_GET["city"])&&!empty($_GET["street"])&&!empty($_GET["home_number"])&&!empty($_GET["zip_code"])){
    UserControl::deleteDeliveryInfo();
    header("Location:index.php?pages=addresses");
}
echo '<div class=list>';
UserControl::printFormAddAddress();
UserControl::printAllAddressesOfUser();
echo '</div>';
?>
