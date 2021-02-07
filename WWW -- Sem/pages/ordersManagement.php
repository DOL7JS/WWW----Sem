<?php
if(!empty($_POST["updateOrder"])){
    if(!empty($_POST["checkBox_list"])){
        OrderControl::updateOrders($_POST["checkBox_list"]);
    }
}
echo '<h1>Správa objednávek</h1>';
echo '<form action="index.php?pages=ordersManagement" method="post">
        <input type="submit" name="updateOrder" value="Uložit" id="saveOrdersButton">
        <div class=list>';
            OrderControl::printOrdersAsAdmin();
 echo '</div>
      </form>';
?>

