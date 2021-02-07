<?php
if(!empty($_POST["detailOrder"])||!empty($_GET["detailOrder"])){
    $idOrder = empty($_POST["detailOrder"])?$_GET["detailOrder"]:$_POST["detailOrder"];
    OrderControl::printOrderDetail($idOrder);
}
?>