<?php


class Sales
{

static function addSale(){//pridani slevy ke zbozi
    $conn = connection::getConnection();
    $nameGoods = $_POST["nameOfGoods"];
    $result = $conn->query("SELECT id_goods FROM db_dev.goods WHERE name = '$nameGoods'");
    $row = $result->fetch_assoc();
    $idGoods = $row["id_goods"];
    $sale = $_POST["sale"];
    $conn->query("UPDATE db_dev.attribute SET sale='$sale' WHERE goods_id_goods='$idGoods';");
}
static function printAllSales(){//vypis vsech slev
    $conn = connection::getConnection();
    $result = $conn->query("SELECT * FROM db_dev.goods");
    echo '<div class=list>';//okno pro pridani slevy
    echo '<div class=listRow>';
    echo '<div class=detailsInRow>';
    echo '<form method="post" id="addSaleForm">';
    echo '<label>Zboží: </label><select name="nameOfGoods" class="pRelBottom7">';
    while ($row = $result->fetch_assoc()){
        echo '<option>'.$row["name"].'</option>';
    }
    echo '</select>';
    echo '<label>  Sleva: </label><input type="number" name="sale" class="pRelBottom7">';
    echo '<input type="submit" value="Přidat slevu" class="pRelBottom7" id="addSaleButton" name="addSale">';
    echo '</form>';
    echo '</div>';
    echo '</div>';

    $result = $conn->query("SELECT DISTINCT id_goods,name,sale,price FROM db_dev.goods JOIN db_dev.attribute a on goods.id_goods = a.goods_id_goods WHERE sale !=0 ");
    while($row = $result->fetch_assoc()){//prochazeni slev a jejich vypis
        echo '<div class=listRow>';
        echo '<div class=detailsInRow>';
        echo 'Zboží: '.$row["name"];
        echo '<br>';
        echo "Sleva: ".$row["sale"]." %";
        echo '<br>';
        echo "Původní cena: ".$row["price"]." Kč";
        echo '<br>';
        echo "Po slevě: ".$row["price"]*(1-$row["sale"]/100)." Kč";
        echo '</div>';

        echo '<div class="btnsInList">';
        echo '<a href="index.php?pages=saleManagement&action=deleteSale&saleId=' . $row["id_goods"] . '"><img id="iconDeleteSale" class="w50h50" src="./imgs/icons/trash.png"></a>';
        echo '<br>';
        echo '</div>';
        echo '</div>';
    }
    echo '</div>';

}
static function deleteSale(){//odebrani slevy
    $conn = connection::getConnection();
    $idGoods = $_GET["saleId"];
    $conn->query("UPDATE db_dev.attribute SET sale=NULL WHERE goods_id_goods='$idGoods';");
    header("Location:index.php?pages=saleManagement");
}
static function printActualSales(){//vypis vsech aktualnich slev
    $_SESSION["actualURL"] = $_SERVER['REQUEST_URI'];
    $conn = connection::getConnection();
    $gender = array(array("m","Muži"),array("f","Ženy"),array("k","Děti"));
    $typeOfGoods = array(array("shirts","Triko"),array("shorts","Trenýrky"),array("bags","Taška"),
        array("balls","Míč"),array("shoes","Kopačky"),array("protectors","Chrániče"),array("goalkeeper","Brankář"));
    $colors = array(array("black","Černá"),array("white","Bílá"),array("blue","Modrá"),
        array("green","Zelená"),array("yellow","Žlutá"),array("orange","Oranžová"),array("red","Červená"));
    $orderBy = array(array("ascending","Vzestupně"),array("descending","Sestupně"));
    if(!empty($_POST["reset"])){
        unset($_POST["gender"]);
        unset($_POST["typeOfGoods"]);
        unset($_POST["color"]);
        unset($_POST["orderBy"]);
    }
    echo '<form method="post" class="filter">';
    echo '<label class="filterCategory">Pohlaví: </label><select name="gender" onchange="this.form.submit()">';
    echo '<option disabled selected>Pohlaví . . .</option>';
    for ($i = 0;$i<3;$i++){
        if(!empty($_POST["gender"])&&$gender[$i][0] == $_POST["gender"]){
            echo '<option selected value='.$gender[$i][0].'>'.$gender[$i][1].'</option>';
        }else{
            echo '<option value='.$gender[$i][0].'>'.$gender[$i][1].'</option>';
        }
    }
    echo '</select>';

    echo '<label class="filterCategory">Typ: </label>
        <select name="typeOfGoods" onchange="this.form.submit()">';
    echo '<option disabled selected>Typ . . .</option>';

    for ($i = 0;$i<7;$i++){
        if(!empty($_POST["typeOfGoods"])&&$typeOfGoods[$i][0] == $_POST["typeOfGoods"]){
            echo '<option selected value='.$typeOfGoods[$i][0].'>'.$typeOfGoods[$i][1].'</option>';
        }else{
            echo '<option value='.$typeOfGoods[$i][0].'>'.$typeOfGoods[$i][1].'</option>';
        }
    }
    echo '</select>';
    echo '<br>';

    echo '<label class="filterCategory">Barva: </label><select name="colorFilter" onchange="this.form.submit()">';
    echo '<option disabled selected>Barva . . .</option>';

    for ($i = 0;$i<7;$i++){
        if(!empty($_POST["color"])&&$colors[$i][0] == $_POST["color"]){
            echo '<option selected value='.$colors[$i][0].'>'.$colors[$i][1].'</option>';
        }else{
            echo '<option value='.$colors[$i][0].'>'.$colors[$i][1].'</option>';
        }
    }
    echo '</select>';

    echo '<label class="filterCategory">Cena: </label><select name="orderByFilter" class="pRelLeft5" id="orderByFilterSelect"  onchange="this.form.submit()">';
    echo '<option disabled selected>Cena . . .</option>';

    for ($i = 0;$i<2;$i++){
        if(!empty($_POST["orderBy"])&&$orderBy[$i][0] == $_POST["orderBy"]){
            echo '<option selected value='.$orderBy[$i][0].'>'.$orderBy[$i][1].'</option>';
        }else{
            echo '<option value='.$orderBy[$i][0].'>'.$orderBy[$i][1].'</option>';
        }
    }
    echo '</select>';
    Sales::setSize();
    echo '<input type="submit" name="reset" value="Resetovat filtr">';
    echo '</form>';
    $sql = "SELECT DISTINCT id_goods,name,price, gender,color,image,sale,category_id_category,available FROM db_dev.goods
                                                              JOIN db_dev.attribute a on db_dev.goods.id_goods = a.goods_id_goods
    JOIN db_dev.goods_category gc on db_dev.goods.id_goods = gc.goods_id_goods WHERE sale !=0 AND available=1";
    if(!empty($_POST["gender"])){
        $sql = $sql." AND gender='".$_POST["gender"]."'";
    }
    if(!empty($_POST["typeOfGoods"])){
        $typeOfGoods = Goods::getIdCategory($_POST["typeOfGoods"]);
        $sql = $sql." AND category_id_category='$typeOfGoods'";
    }
    if(!empty($_POST["colorFilter"])){
        $sql = $sql." AND color='".$_POST["colorFilter"]."'";
    }
    if(!empty($_POST["sizeFilter"])){
        $sql = $sql." AND size='".$_POST["sizeFilter"]."'";
    }
    $sql = $sql." ORDER BY price";
    if(!empty($_POST["orderByFilter"])){
        if($_POST["orderByFilter"]=="descending"){
            $sql = $sql." DESC";
        }
    }
    $result = $conn->query($sql);
    echo '<div class="allGoods">';
    while($row = $result->fetch_assoc()) {//prochazeni a vypis zbozi se slevou

        echo '<div  class="goods p50">';
        echo '<div class="triangle-topright"><p class="textInSale"">SLEVA</p></div>';

        echo '<tr><td> <img width="248" height="248" src='.$row["image"].'/> </td>
                <section id="textZbozi">
                <div><td> '.$row["name"].'</td></div>
                <div><td>Původní cena: '.$row["price"]." Kč".'</td></div>
                <div><td>Aktuální cena: '.$row["price"]*(1-($row["sale"]/100))." Kč, -".$row["sale"].' %</td></div>
                </section>
                <form method="post" action='.$_SERVER['REQUEST_URI'].'&action=add&nameOfGoods='.$row["name"].'>
                <select name="goodsSize">';

        $res = $conn->query("SELECT size FROM db_dev.goods JOIN db_dev.attribute a on db_dev.goods.id_goods = a.goods_id_goods WHERE id_goods = '{$row["id_goods"]}'");
        while($ro=$res->fetch_assoc()){
            echo $ro["size"];
            echo '<option value='.$ro["size"].'>'.$ro["size"].'</option>';

        }
        echo '</select>';

        if(empty($_SESSION["role"])){
            echo '<input type="submit" value="Do košíku"></tr>';
        }else if ($_SESSION["role"]!="Admin"&&$_SESSION["role"]!="Zaměstnanec"){
            echo '<input type="submit" value="Do košíku"></tr>';
        }

        echo '</form></div>';
    }
    echo '</div>';
}
    static function setSize(){
        $conn = connection::getConnection();
        $sql = "SELECT DISTINCT size FROM db_dev.goods JOIN db_dev.attribute a on db_dev.goods.id_goods = a.goods_id_goods
    JOIN db_dev.goods_category gc on db_dev.goods.id_goods = gc.goods_id_goods WHERE sale !=0 ORDER BY size";
        $result = $conn->query($sql);
        $size = array();
        $i = 0;
        while($row=$result->fetch_assoc()){
            $size[$i] = $row["size"];
            $i++;
        }
        echo '<label class="filterCategory">Velikost: </label><select name="sizeFilter"  onchange="this.form.submit()">
                <option disabled selected>Velikost . . .</option>';
        if(!empty($_POST["reset"])) {
            unset($_POST["size"]);
        }
        for ($i = 0;$i<$result->num_rows;$i++){
            if($size[$i] == $_POST["size"]){
                echo '<option selected value='.$size[$i].'>'.$size[$i].'</option>';
            }else{
                echo '<option value='.$size[$i].'>'.$size[$i].'</option>';
            }
        }
        echo '</select>';
    }

}