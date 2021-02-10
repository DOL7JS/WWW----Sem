<?php
if(!empty($_POST["editCategoryButton"])&&ValidityChecker::checkEditAttribute()){
    $categoryCheck = GoodsDB::checkUniqueCategory($_POST["categoryNameCzech"],$_POST["categoryNameEnglish"],0);
    if($categoryCheck==null||$categoryCheck["id_category"]==$_GET["categoryID"]){
        if(!empty($_FILES['Filename']['name'])){
            GoodsDB::updateCategoryWithImage($_GET["categoryID"],$_POST["categoryNameCzech"],$_POST["categoryNameEnglish"],"imgs/imgs_section/".$_FILES['Filename']['name']);
            move_uploaded_file($_FILES["Filename"]["tmp_name"],"imgs/imgs_section/".$_FILES['Filename']['name']);//pridani obrazku do slozky 'imgs/imgs_goods'
        }else{
            GoodsDB::updateCategoryWithoutImage($_GET["categoryID"],$_POST["categoryNameCzech"],$_POST["categoryNameEnglish"]);
        }
        UserControl::printInformation('Kategorie byla upravena');
    }else{
        UserControl::printInformation('Kategorie s tímto názvem již existuje');
    }
}

if(!empty($_GET["categoryID"])){
    $category = GoodsDB::selectCategoryById($_GET["categoryID"]);
    echo '<div class="list">';
    echo '<h1>Úprava kategorie</h1>';
    echo '<div class=listRow>';
    echo '<div class=detailsInRow>';
    echo '<form method="post" id="addCategoryForm"  enctype="multipart/form-data" action="index.php?pages=editCategory&categoryID=' . $category["id_category"] . '"">';
    echo '<label class="w250" >Typ (česky): </label>';
    echo '<input id="categoryNameCzech" type="text" name="categoryNameCzech" class="inputsNextLabel" value="'.$category["czech_name"].'">';
    echo '<br>';
    echo '<label class="w250">Typ (anglicky): </label>';
    echo '<input id="categoryNameEnglish" type="text" name="categoryNameEnglish" class="inputsNextLabel" value="'.$category["name"].'">';
    echo '<br>';
    echo '<input name="Filename" type="file" class="inputsNextLabel w300 addFile"/>';
    echo '<input type="submit" value="Upravit typ" class="pRelBottom7 pRelRight70" id="addCategoryButton" name="editCategoryButton">';
    echo '</form>';
    echo '</div>';
    echo '<img id="imageEditGoods" src="'.$category["image"].'" >';
    echo '</div>';
    echo '</div>';
}
?>