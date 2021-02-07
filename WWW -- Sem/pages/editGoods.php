<?php
if(!empty($_GET["action"])&&$_GET["action"]=="addAttribute"&&ValidityChecker::checkAddAttribute()){
    $sizes = array("XS", "S", "L", "XL","XXL","3XL");
    if(in_array($_POST["attributeSizeAdd"],$sizes)||is_numeric($_POST["attributeSizeAdd"])){
        if(AdminControl::addAttribute($_GET["goodsID"],$_POST["attributeSizeAdd"],$_POST["attributeColorAdd"],"imgs/imgs_goods/".$_FILES["Filename"]["name"])) {
            UserControl::printInformation('Atribut přidán');
        }
    }else{
        UserControl::printInformation('Zadal jste neplatnou velikost');
    }
}
if(!empty($_GET["action"])&&$_GET["action"]=="deleteAttribute"&&!empty($_POST["deleteAttributeButton"])){//TODO VALIDITYCHECKER
    AdminControl::deleteAttribute($_GET["goodsID"],$_POST["attributeSizeDelete"],$_POST["attributeColorDelete"]);
    $checkGoods = GoodsDB::getGoodsWithAvailableAttributeById($_GET["goodsID"]);
    if($checkGoods==null){
        GoodsControl::deleteGoods($_GET["goodsID"]);
        $_SESSION["goodsDeleted"] = 1;
        Header("Location:index.php?pages=goodsManagement");
    }else{
        Header("Location:index.php?pages=editGoods&goodsID=".$_GET["goodsID"]);
    }
}
if(!empty($_GET["action"])&&$_GET["action"]=="updateGoods"&&ValidityChecker::checkUpdateGoods()){
    GoodsControl::updateGoods($_GET["goodsID"],$_POST["name"],$_POST["price"]);
    UserControl::printInformation('Zboží upraveno');

}

echo '<div class="list">';
AdminControl::printFormEditGoods($_GET["goodsID"]);
AdminControl::printFormAddAttribute($_GET["goodsID"]);
AdminControl::printFormDeleteAttribute($_GET["goodsID"]);

echo '</div>';





?>