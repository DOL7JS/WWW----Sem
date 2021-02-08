<?php
echo '<h1>Shrnutí objednávky</h1>';
if(!empty($_POST["completeOrder"])){
    if($_SESSION["delivery_info"]["paymentMethod"]==1){//pokud je platba kartou, proskoci se na "platebni branu"
        header("Location:index.php?pages=paymentGateway");
    }else{
        OrderControl::addOrder($_SESSION["delivery_info"]["first_name"],$_SESSION["delivery_info"]["last_name"],
            $_SESSION["delivery_info"]["phone_number"],$_SESSION["delivery_info"]["city"],
            $_SESSION["delivery_info"]["street"],$_SESSION["delivery_info"]["home_number"],$_SESSION["delivery_info"]["zip_code"]);
        unset($_SESSION["delivery_info"]);
        OrderControl::printOrderConfirm();
    }
}
if(!empty($_SESSION["cart"])){
    OrderControl::printOrderSummary();
}
