<?php


session_start();

if(empty($_GET["pages"])){
    $_GET["pages"] = "";
}

require_once "./classes/Connection.php";
require_once "./classes/Section.php";
require_once "./classes/CartControl.php";
require_once "./classes/ValidityChecker.php";
require_once "./classes/GoodsControl.php";
require_once "./classes/GoodsDB.php";
require_once "./classes/UserControl.php";
require_once "./classes/UserDB.php";
require_once "./classes/OrderControl.php";
require_once "./classes/OrderDB.php";
require_once "./classes/AdminControl.php";
require_once "./classes/Colors.php";




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
print_r($_SESSION);
echo '<br>';
print_r($_POST);

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