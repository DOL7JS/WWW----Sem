<?php
echo '<h1>AKCE</h1>';
if(!empty($_GET["action"])&&!empty($_GET["nameOfGoods"])&&$_GET["action"]=="add"){
    Cart::addToCart($_GET["nameOfGoods"].",".$_POST["goodsSize"]);
    header("Location:index.php?pages=sales");
}
Sales::printActualSales();
?>
