<?php

if(!empty($_GET["action"])&&$_GET["action"]=="add"&&!empty($_GET["idGoods"])){
    Cart::addToCart($_GET["idGoods"].",".$_POST["goodsSize"]);//přidání zboží do košíku
    header('Location: '.$_SESSION['actualURL'].'');
    unset($_SESSION['actualURL']);
}

if(!empty($_GET["goods"])&&!empty($_GET["gender"])){
    $_SESSION["actualURL"] = $_SERVER['REQUEST_URI'];
    Goods::printGoods($_GET["goods"]);
}


?>

