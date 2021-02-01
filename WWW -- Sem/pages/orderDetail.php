<?php
if(!empty($_POST["detailOrder"])||!empty($_GET["detailOrder"])){
    Orders::printOrderDetail();
}
?>