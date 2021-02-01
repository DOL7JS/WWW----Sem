
<?php
echo '<h1>Správa zboží</h1>';

if(!empty($_POST["addGoods"])){
    ValidityChecker::checkValidityGoods();//kontrola zda jsou vyplněny potřebné fieldy
}
if(!empty($_FILES['Filename'])){
    if(!empty($fileName = $_FILES['Filename']['name'])){
        Goods::addGoods();//přidání zboží
        Users::printInformation('Zboží přidáno');
    }
}
if(!empty($_GET["action"])&&$_GET["action"]=="unavailable"){
    Goods::setUnavailable($_GET["goodsID"]);//nastavení zboží na nedostupné
    header("Location:index.php?pages=goodsManagement");
}
if(!empty($_GET["action"])&&$_GET["action"]=="available"){
    Goods::setAvailable($_GET["goodsID"]);//nastavení zboží na dostupné
    header("Location:index.php?pages=goodsManagement");
}
if(!empty($_GET["action"])&&$_GET["action"]=="deleteGoods"){
    Goods::deleteGoods($_GET["goodsID"]);//odstranění zboží
    header("Location:index.php?pages=goodsManagement");
    Users::printInformation('Zboží odstraněno');
}
if(!empty($_GET["action"])&&$_GET["action"]=="addAttribute"){
    if(!empty($_POST["attributeSize"])){
        Goods::addAttribute();//přidání atributu/velikosti
        header("Location:index.php?pages=goodsManagement");
    }
}
if(!empty($_GET["action"])&&$_GET["action"]=="deleteAttribute"){
    Goods::deleteAttribute();//odebrání atributu/velikosti
    header("Location:index.php?pages=goodsManagement");
}
if(!empty($_POST["exportJson"])){
    Orders::exportToJson();
}
if(!empty($_POST["importJson"])){
    Orders::importJson();
}
Goods::printAllGoods();
?>

