<?php
echo '<h1>AKCE</h1>';
if(!empty($_GET["action"])&&!empty($_GET["idGoods"])&&$_GET["action"]=="add"&&!empty($_POST["addToCart"])){
    CartControl::addToCart($_GET["idGoods"],$_POST["goodsSize"],$_POST["goodsColor"]);
    header("Location:index.php?pages=sales");
}
GoodsControl::setFilterInSale();
GoodsControl::printGoodInSale();
?>
