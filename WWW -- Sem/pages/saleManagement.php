<?php
echo '<h1>Správa akcí</h1>';

if(!empty($_POST["addSale"])){
    ValidityChecker::checkValiditySale();
}
if(!empty($_GET["action"])&&$_GET["action"]=="deleteSale"){
    Sales::deleteSale();//odebrani slevy
    Users::printInformation('Sleva odstraněna');
}
if(!empty($_POST["sale"])&&!empty($_POST["nameOfGoods"])){
    if($_POST["sale"]>0&&$_POST["sale"]<100) {
        Sales::addSale();//pridani slevy
        Users::printInformation('Sleva přidána');
    }
}
Sales::printAllSales();
?>
