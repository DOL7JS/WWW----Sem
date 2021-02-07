<?php
echo '<h1>Správa akcí</h1>';

if(!empty($_GET["action"])&&$_GET["action"]=="deleteSale"){
    AdminControl::deleteSale($_GET["goodsId"]);
    //Sales::deleteSale();//odebrani slevy
    UserControl::printInformation('Sleva odstraněna');
    header("Location:index.php?pages=saleManagement");
}
if(!empty($_POST["addSale"])&&ValidityChecker::checkValiditySale()){
        //Sales::addSale();//pridani slevy
    AdminControl::addSale($_POST["nameOfGoods"],$_POST["sale"]);
    UserControl::printInformation('Sleva přidána');
}
//Sales::printAllSales();

echo '<div class=list>';//okno pro pridani slevy
AdminControl::printFormAddSale();
AdminControl::printSalesAsAdmin();
echo '</div>';
?>
