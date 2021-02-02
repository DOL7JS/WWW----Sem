
<h1>Košík</h1>
<?php

if(!empty($_GET["action"])){
    if($_GET["action"]=="add"&&!empty($_GET["idGoods"])){
        Cart::addToCart($_GET["idGoods"]);//navýšení zboží v košíku
    }
    if($_GET["action"]=="remove"&&!empty($_GET["idGoods"])){
        Cart::removeGoods($_GET["idGoods"]);//snížení počtu zboží v košíku
    }
    if($_GET["action"]=="delete"&&!empty($_GET["idGoods"])){
        Cart::deleteGoods($_GET["idGoods"]);//smazání všech kusů daného zboží
    }
    header("Location: index.php?pages=cart");
}
if(!empty($_POST["completeOrder"])){
    if(!empty($_SESSION["loggedIn"])&&$_SESSION["loggedIn"]){//kontrola zda je uživatel přihlášen=>aby mohl nakupovat
        header("Location: index.php?pages=payment");
    }else{
        Users::printInformation('Přihlašte se !');
    }
}

Cart::printCart();
?>

