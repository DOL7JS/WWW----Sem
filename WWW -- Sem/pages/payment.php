<?php
if(!empty($_POST["orderButton"])){
    ValidityChecker::checkValidityPayment();
}
if(!empty($_POST["deliveryMethod"])&&!empty($_POST["paymentMethod"])&&!empty($_POST["first_name"])&&!empty($_POST["last_name"])&&
    !empty($_POST["phone_number"])&&!empty($_POST["city"])&&!empty($_POST["street"])&&!empty($_POST["home_number"])&&!empty($_POST["zip_code"])){
    $_SESSION["delivery_info"]["deliveryMethod"] = $_POST["deliveryMethod"];//přiřazení proměnné do $_SESSION k pozdějšímu využití
    $_SESSION["delivery_info"]["paymentMethod"] = $_POST["paymentMethod"];
    $_SESSION["delivery_info"]["first_name"] = $_POST["first_name"];
    $_SESSION["delivery_info"]["last_name"] = $_POST["last_name"];
    $_SESSION["delivery_info"]["phone_number"] = $_POST["phone_number"];
    $_SESSION["delivery_info"]["city"] = $_POST["city"];
    $_SESSION["delivery_info"]["street"] = $_POST["street"];
    $_SESSION["delivery_info"]["home_number"] = $_POST["home_number"];
    $_SESSION["delivery_info"]["zip_code"] = $_POST["zip_code"];
    $_SESSION["delivery_info"]["save_delivery_info"] = $_POST["save_delivery_info"];
    header("Location:index.php?pages=orderSummary");
}
Users::printFilledNameAddress();
?>




