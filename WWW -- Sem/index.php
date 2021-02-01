<?php


session_start();

if(empty($_GET["pages"])){
    $_GET["pages"] = "";
}

require_once "./classes/Connection.php";
require_once "./classes/Section.php";
require_once "./classes/Goods.php";
require_once "./classes/Cart.php";
require_once "./classes/Users.php";
require_once "./classes/Orders.php";
require_once "./classes/Sales.php";
require_once "./classes/ValidityChecker.php";


//echo '<br>';
//print_r($_FILES);
//echo '<br>';
//print_r($_POST);
//echo '<br>';
//print_r($_GET);
//echo '<br>';
//if(!empty($_SESSION)){
  //  print_r($_SESSION);
//}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="css/indexCSS.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Eshop</title>
</head>
<body>

<?php

include "menu.php";
?>
<?php
$pathToFile = "./pages/".$_GET["pages"].".php";
if(file_exists($pathToFile)){
    include $pathToFile;
}else{
    include "./pages/homePage.php";
}
?>

<?php
include "footer.php"
?>


</body>
</html>