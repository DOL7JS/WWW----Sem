
<h1>Košík</h1>
<?php

if(!empty($_GET["action"])){
    if($_GET["action"]=="add"&&!empty($_GET["idGoods"])){
        CartControl::addToCart($_GET["idGoods"],$_GET["size"],$_GET["color"]);//navýšení zboží v košíku
    }
    if($_GET["action"]=="remove"&&!empty($_GET["idGoods"])){
        CartControl::removeGoods($_GET["idGoods"],$_GET["size"],$_GET["color"]);//snížení počtu zboží v košíku
    }
    if($_GET["action"]=="delete"&&!empty($_GET["idGoods"])){
        CartControl::deleteGoods($_GET["idGoods"],$_GET["size"],$_GET["color"]);//smazání všech kusů daného zboží
    }
    header("Location: index.php?pages=cart");
}
if(!empty($_POST["completeOrder"])){
    if(!empty($_SESSION["loggedIn"])&&$_SESSION["loggedIn"]){//kontrola zda je uživatel přihlášen=>aby mohl nakupovat
        header("Location: index.php?pages=payment");
    }else{
        UserControl::printInformation('Přihlašte se !');
    }
}

CartControl::printCart();
?>

