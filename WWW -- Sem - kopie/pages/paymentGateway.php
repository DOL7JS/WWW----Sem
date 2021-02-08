<?php

if(!empty($_POST["Potvrdit"])&&ValidityChecker::checkValidityPaymentGateway()){//kontrola zda je vse vyplnene
        OrderControl::addOrder($_SESSION["delivery_info"]["first_name"],$_SESSION["delivery_info"]["last_name"],
            $_SESSION["delivery_info"]["phone_number"],$_SESSION["delivery_info"]["city"],
            $_SESSION["delivery_info"]["street"],$_SESSION["delivery_info"]["home_number"],$_SESSION["delivery_info"]["zip_code"]);
        unset($_SESSION["delivery_info"]);
        OrderControl::printOrderConfirm();
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