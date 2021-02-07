<?php
echo '<h1>Správa zboží</h1>';
if(!empty($_SESSION["goodsDeleted"])){
    UserControl::printInformation('Zbozi bylo odebrano');
    unset($_SESSION["goodsDeleted"]);
}
if(!empty($_POST["addGoods"])&&ValidityChecker::checkValidityGoods()){
    if(GoodsControl::addGoods($_FILES['Filename']['name'],$_POST["name"],$_POST["price"],$_POST["size"],$_POST["gender"],$_POST["typeOfGoods"],$_POST["color"])){
        UserControl::printInformation('Zboží přidáno');
    }else{
        UserControl::printInformation('Zboží se stejným jménem už je v systému');
    }
}
if(!empty($_GET["action"])&&$_GET["action"]=="unavailable"){
    GoodsControl::setGoodsUnavailable($_GET["goodsID"]);
    header("Location:index.php?pages=goodsManagement");
}
if(!empty($_GET["action"])&&$_GET["action"]=="available"){
    GoodsControl::setGoodsAvailable($_GET["goodsID"]);
    header("Location:index.php?pages=goodsManagement");
}
if(!empty($_GET["action"])&&$_GET["action"]=="deleteGoods"){
    GoodsControl::deleteGoods($_GET["goodsID"]);
    header("Location:index.php?pages=goodsManagement");
    UserControl::printInformation('Zboží odstraněno');
}

if(!empty($_GET["action"])&&$_GET["action"]=="edit"){
    header("Location:index.php?pages=editGoods&goodsID=".$_GET['goodsID']."");
}
echo '<div class=list>';
    AdminControl::printFormAddGoods();
    AdminControl::printFilterInGoodsManagement();
    $orderByPrice = empty($_POST["orderByPrice"])?null:$_POST["orderByPrice"];
    $filterByCategory = empty($_POST["filterByCategory"])?null:$_POST["filterByCategory"];
    $orderByAvailable = empty($_POST["orderByAvailable"])?null:$_POST["orderByAvailable"];
    AdminControl::printGoodsAsAdmin($orderByPrice,$filterByCategory,$orderByAvailable);
echo '</div>';
?>

