<?php

if(!empty($_SESSION["noGoods"])){
    UserControl::printInformation('Zboží není na skladě, zkuste jiné');
    unset($_SESSION["noGoods"]);
}
if(!empty($_GET["action"])&&$_GET["action"]=="add"&&!empty($_GET["idGoods"])&&!empty($_POST["addToCart"])){
    $attribute = GoodsDB::selectAttributeByIdGoodsBySizeByColor($_GET["idGoods"],$_POST["goodsSize"],$_POST["goodsColor"]);
    if($attribute!=null){
        CartControl::addToCart($_GET["idGoods"],$_POST["goodsSize"],$_POST["goodsColor"]);//přidání zboží do košíku
    }else{
        $_SESSION["noGoods"] = 1;
    }
    header('Location: '.$_SESSION['actualURL']);
    unset($_SESSION['actualURL']);
}

if(!empty($_GET["goods"])&&!empty($_GET["gender"])){
    $_SESSION["actualURL"] = $_SERVER['REQUEST_URI'];
    GoodsControl::printFilter();
    GoodsControl::printGoodsByCategoryAndGender($_GET["goods"],$_GET["gender"]);
}


?>

