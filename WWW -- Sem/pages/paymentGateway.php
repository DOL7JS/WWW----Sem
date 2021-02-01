<?php
if(!empty($_POST["Potvrdit"])){
    ValidityChecker::checkValidityPaymentGateway();
}
if(!empty($_SESSION["delivery_info"]["deliveryMethod"])&&!empty($_SESSION["delivery_info"]["paymentMethod"])&&
    !empty($_SESSION["delivery_info"]["first_name"])&&!empty($_SESSION["delivery_info"]["last_name"])&&
    !empty($_SESSION["delivery_info"]["phone_number"])&&!empty($_SESSION["delivery_info"]["city"])&&
    !empty($_SESSION["delivery_info"]["street"])&&!empty($_SESSION["delivery_info"]["home_number"])&&!empty($_SESSION["delivery_info"]["zip_code"]) &&
    !empty($_POST["numberOfCard"])&&!empty($_POST["validityMonth"])&&!empty($_POST["validityYear"])&&!empty($_POST["CVC"])) {//kontrola zda je vse vyplnene
        Orders::addOrder();//pridani/potvrzeni objednavky
        unset($_SESSION["delivery_info"]);
        Orders::printOrderConfirm();
}
?>
<form action="" method="post">
    <div id="debitCard">
        <p class="pRelT10">Číslo platební karty:</p>
        <input class="w90" name="numberOfCard" type="number" placeholder="Cislo platebni karty">
        <div id ="debitDetails"> Platnost do:
            <input name="validityMonth" type="number" placeholder="00" class="w40">/
            <input name="validityYear" type="number" placeholder="00" class="w40">
             <p id="w20dInline">CVC:
            <input name="CVC" type="number" placeholder="CVC" class="w70pRelR-20"></p>
        </div>
        <input type="submit" name="Potvrdit" value="Potvrdit" class="w90">
    </div>
</form>