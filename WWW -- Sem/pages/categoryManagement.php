<?php

if(!empty($_POST["addCategoryButton"])&&ValidityChecker::checkAddCategory()){//checkValidity
    AdminControl::addCategory($_POST["categoryNameCzech"],$_POST["categoryNameEnglish"],$_FILES['Filename']['name']);
    Header("Location:index.php?pages=categoryManagement");
}
if(!empty($_GET["action"])&&$_GET["action"]=="deleteCategory"){
    AdminControl::deleteCategory($_GET["goodsID"]);
    Header("Location:index.php?pages=categoryManagement");
}
if(!empty($_POST["exportJson"])){
    AdminControl::exportToJSon();
}
if(!empty($_POST["importJson"])){
    AdminControl::importJson();
}
echo '<h1>Správa typů zboží</h1>';
echo '<form method="post" action="">
        <input type="submit" name="exportJson" value="Export JSON" id="exportJsonButton">
        <input type="submit" name="importJson" value="Import JSON" id="importJsonButton">
    </form>
  <div class=list>';
AdminControl::printFormAddCategory();
AdminControl::printAllCategoriesAsAdmin();
echo '</div>';

?>