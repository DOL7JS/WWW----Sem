<?php
echo '<h1>AKCE</h1>';
if(!empty($_GET["action"])&&!empty($_GET["idGoods"])&&$_GET["action"]=="add"){
    Cart::addToCart($_GET["idGoods"].",".$_POST["goodsSize"]);
    header("Location:index.php?pages=sales");
}
Sales::printActualSales();
?>
