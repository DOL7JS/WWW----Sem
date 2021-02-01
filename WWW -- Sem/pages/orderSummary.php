<?php
echo '<h1>Shrnutí objednávky</h1>';
if(!empty($_POST["completeOrder"])){
    if($_SESSION["delivery_info"]["paymentMethod"]==1){//pokud je platba kartou, proskoci se na "platebni branu"
        header("Location:index.php?pages=paymentGateway");
    }else{
        Orders::addOrder();//přidá/potvrdí se objednávka
        Orders::printOrderConfirm();
    }
}
Orders::printOrderSummary();