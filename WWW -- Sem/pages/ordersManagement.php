<?php
if(!empty($_POST["updateOrder"])){
    Orders::updateOrder();
}
echo '<h1>Správa objednávek</h1>';
    Orders::printAllOrders();
?>

