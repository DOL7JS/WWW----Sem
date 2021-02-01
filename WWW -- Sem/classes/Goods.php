<?php


class Goods
{
        static function printGoods($category){
                echo '<h1>'.Section::getCategory($category).'</h1>';
                echo '<form method="post" class="filter">';
                Goods::setOrderBy("goods");//nastaveni filtru vzestupne/sestupne ceny
                Goods::setSize($category);//nastaveni filtru podle velikosti
                Goods::setColor();//nastaveni filtru podle barvy
                $conn = connection::getConnection();
                $idCategory = Goods::getIdCategory($category);//ziskani id dane kategorie
                $sql = "SELECT DISTINCT id_goods,name,price, gender,color,image,category_id_category,available,sale FROM db_dev.goods
                                                              JOIN db_dev.attribute a on db_dev.goods.id_goods = a.goods_id_goods
                                                              JOIN db_dev.goods_category gc on db_dev.goods.id_goods = gc.goods_id_goods 
                                                              WHERE available = 1 AND gender = '{$_GET["gender"]}' AND category_id_category='$idCategory'";
                if(!empty($_POST["color"])){//pridani filtru k SQL prikazu
                        $sql = $sql." AND COLOR = '".$_POST["color"]."'";
                }
                if(!empty($_POST["size"])){//pridani filtru k SQL prikazu
                        $sql = $sql." AND SIZE = '".$_POST["size"]."'";
                }
                if(!empty($_POST["orderBy"])&&$_POST["orderBy"]=="descending"){//pridani filtru k SQL prikazu
                        $sql = $sql ." ORDER BY price DESC";
                }else{
                        $sql = $sql ." ORDER BY price";
                }

                echo '<input type="submit" name="reset" value="Resetovat filtr" >';
                echo '</form>';
                $result = mysqli_query($conn, $sql) or die("<b>Error:</b> Problem on Retrieving Image BLOB<br/>" . mysqli_error($conn));
                echo '<div class="allGoods">';
                while($row = $result->fetch_assoc()) {
                        if($row["sale"]!=0){//pokud je sleva, vypise se trojuhelnik upozornujici na tow
                                echo '<div  class="goods p50">';
                                echo '<div class="triangle-topright"><p class="textInSale">SLEVA</p></div>';
                        }else{
                                echo '<div  class="goods pad100-50-0-50">';
                        }
                        echo '
                                <img id="imageGoods" src='.$row["image"].' alt='.$row["image"].'>
                                <section id="textZbozi">
                                <div> '.$row["name"].'</div>';
                                if($row["price"]!=$row["price"]*(1-$row["sale"]/100)){
                                        echo '<div>Původní cena: '.$row["price"]." Kč".'</div>';
                                }
                                echo '<div>
                                            Cena: '.$row["price"]*(1-$row["sale"]/100)." Kč".'
                                     </div>';
                                echo '</section>';
                                echo '<form '; if($row["sale"]==0) {echo 'class="pTop25"';} echo ' method="post" action='.$_SERVER['REQUEST_URI'].'&action=add&nameOfGoods='.$row["id_goods"].'>
                                <select name="goodsSize">';
                                        $res = $conn->query("SELECT size FROM db_dev.goods JOIN db_dev.attribute a on db_dev.goods.id_goods = a.goods_id_goods WHERE id_goods = '{$row["id_goods"]}'");
                                        while($ro=$res->fetch_assoc()){
                                                echo $ro["size"];
                                                echo '<option value='.$ro["size"].'>'.$ro["size"].'</option>';
                                        }
                                echo '</select>';
                                if(empty($_SESSION["role"])){//pokud je nezaregistrovany uzivatel, muze dat do kosiku
                                        echo '<input type="submit" value="Do košíku">';
                                }else if ($_SESSION["role"]!="Admin"&&$_SESSION["role"]!="Zaměstnanec"){//pokud neni uzivatel Admin nebo Zamestnanec, muze dat do kosiku
                                        echo '<input type="submit" value="Do košíku">';
                                }
                        echo '</form></div>';
                }
                echo '</div>';
        }
        static function setOrderBy($position){//nastaveni selectu pro filtr dle ceny
                $orderBy = array(array("ascending","Vzestupně"),array("descending","Sestupně"));
                if(!empty($_POST["available"])&&$_POST["available"]=="unavailable"){
                        if($position=="goods"){
                                echo '<label class="orderByFilter" >Cena: </label><select  disabled name="orderBy"  onchange="this.form.submit()">';
                        }else{
                                echo '<label class="w130">Cena: </label><select id="orderByAddGoods" name="orderBy" class="orderByAddGoods inputsNextLabel"  disabled  onchange="this.form.submit()">';
                        }
                }else{
                        if($position=="goods"){
                                echo '<label class="orderByFilter">Cena: </label><select name="orderBy"  onchange="this.form.submit()">';
                        }else{
                                echo '<label class="w130">Cena: </label><select id="orderByAddGoods" name="orderBy" class="orderByAddGoods inputsNextLabel"  onchange="this.form.submit()">';
                        }
                }
                echo' <option disabled selected>Cena . . .</option>';
                if(!empty($_POST["reset"])) {//reset filtru
                        unset($_POST["orderBy"]);
                }
                for ($i = 0;$i<2;$i++){//nastaveni vybraneho optionu
                        if(!empty($_POST["orderBy"])&&$orderBy[$i][0] == $_POST["orderBy"]){
                                echo '<option selected value='.$orderBy[$i][0].'>'.$orderBy[$i][1].'</option>';
                        }else{
                                echo '<option value='.$orderBy[$i][0].'>'.$orderBy[$i][1].'</option>';
                        }
                }
                echo '</select>';
        }
        static function setSize($category){//nastaveni selectu pro filtr dle velikosti
                $conn = connection::getConnection();
                $idCategory = Goods::getIdCategory($category);
                $sql = "SELECT DISTINCT size FROM db_dev.goods JOIN db_dev.attribute a on db_dev.goods.id_goods = a.goods_id_goods
                                                               JOIN db_dev.goods_category gc on db_dev.goods.id_goods = gc.goods_id_goods 
                                                               WHERE gender = '{$_GET["gender"]}' AND category_id_category='$idCategory'";//vybrani moznych velikosti, podle kterych muzeme filtrovat
                $result = $conn->query($sql);
                $size = array();
                $i = 0;
                while($row=$result->fetch_assoc()){
                        $size[$i] = $row["size"];//vkladani jednotlivych velikosti do array
                        $i++;
                }
                echo '<label id="sizeFilter">Velikost: </label><select name="size"  onchange="this.form.submit()">
                <option disabled selected>Velikost . . .</option>';
                if(!empty($_POST["reset"])) {//reset filtru
                        unset($_POST["size"]);
                }
                for ($i = 0;$i<$result->num_rows;$i++){//nastaveni vybrane optionu
                        if($size[$i] == $_POST["size"]){
                                echo '<option selected value='.$size[$i].'>'.$size[$i].'</option>';
                        }else{
                                echo '<option value='.$size[$i].'>'.$size[$i].'</option>';
                        }
                }
                echo '</select>';
        }
        static function setColor(){//nastaveni selectu pro filtr dle barvy
                $colors = array(array("black","Černá"),array("white","Bílá"),array("blue","Modrá"),
                                array("green","Zelená"),array("yellow","Žlutá"),array("orange","Oranžová"),array("red","Červená"));
                echo '<label id="colorFilter">Barva:</label>
                      <select name="color" onchange="this.form.submit()">';
                echo '<option disabled selected>Barva . . .</option>';
                if(!empty($_POST["reset"])){//reset filtru
                        unset($_POST["color"]);
                }
                for ($i = 0;$i<7;$i++){//nastaveni vybraneho optionu
                        if(!empty($_POST["color"])&&$colors[$i][0] == $_POST["color"]){
                                echo '<option selected value='.$colors[$i][0].'>'.$colors[$i][1].'</option>';
                        }else{
                                echo '<option value='.$colors[$i][0].'>'.$colors[$i][1].'</option>';
                        }
                }
                echo '</select>';
        }
        static function getIdCategory($nameOfCategory){
                $conn = connection::getConnection();
                $sql = "SELECT id_category FROM db_dev.CATEGORY WHERE name = '$nameOfCategory'";
                $result = $conn->query($sql);
                if($result->num_rows>0) {
                        $id_category = $result->fetch_assoc();
                        return $id_category["id_category"];
                }
                return -1;
        }
        static function deleteGoods($idOfGoods){//odebrani zbozi
                $conn = connection::getConnection();
                $result = $conn->query("SELECT image FROM db_dev.GOODS WHERE id_goods = '$idOfGoods'");
                $row = $result->fetch_assoc();
                $new_dest = explode("/",$row["image"]);
                $new_dest[1] = "deleted_goods";
                $new_dest = implode("/",$new_dest);
                rename($row['image'],$new_dest);//presun obrazku do slozky deleted_goods
                $conn->query("DELETE FROM db_dev.GOODS WHERE id_goods = '$idOfGoods'");//s timto se spusti trigger pro presun do tabulky deleted_goods
        }
        static function setUnavailable($idOfGoods){//nastaveni zbozi na 'Neni k dispozici'
                $conn = connection::getConnection();
                $conn->query("UPDATE db_dev.GOODS SET available=0 WHERE id_goods = '$idOfGoods'");
        }
        static function setAvailable($idOfGoods){//nastaveni zbozi na 'K dispozici'
                $conn = connection::getConnection();
                $conn->query("UPDATE db_dev.GOODS SET available=1 WHERE id_goods = '$idOfGoods'");
        }
        static function addGoods(){//pridani zbozi
                $conn = connection::getConnection();
                $result = $conn->query("SELECT * FROM db_dev.GOODS WHERE db_dev.GOODS.name = '{$_POST['name']}'");
                if($result->num_rows==0){//zjisteni zda neexistuje zbozi se stejnym jmenem
                        $fileName = $_FILES['Filename']['name'];
                        $target = "imgs/imgs_goods/";
                        $fileTarget = $target.$fileName;//nastaveni cesty k souboru
                        $tempFileName = $_FILES["Filename"]["tmp_name"];
                        $result = $conn->query("SELECT image FROM db_dev.GOODS WHERE image = '$fileTarget'");
                        if($result->num_rows==0){//zjistuje zda neexistuje soubor se stejnou cestou
                                $result = move_uploaded_file($tempFileName,$fileTarget);//pridani obrazku do slozky 'imgs/imgs_goods'
                                if($result) {//cast vlozeni zbozi, atributu a kategorie zbozi
                                        $sql = "INSERT INTO db_dev.goods(name,price,image) VALUES('{$_POST['name']}','{$_POST['price']}','{$fileTarget}')";
                                        $conn->query($sql);
                                        $id_category = Goods::getIdCategory($_POST['typeOfGoods']);
                                        $result = $conn->query("SELECT id_goods FROM db_dev.GOODS WHERE name = '{$_POST['name']}'");
                                        $id_goods = $result->fetch_assoc();
                                        $sql = "INSERT INTO db_dev.attribute(gender,size,color,GOODS_id_goods) VALUES('{$_POST['gender']}','{$_POST['size']}','{$_POST['color']}','{$id_goods['id_goods']}')";
                                        $conn->query($sql);
                                        $sql = "INSERT INTO db_dev.goods_category(GOODS_id_goods,CATEGORY_id_category) VALUES('{$id_goods['id_goods']}','{$id_category}')";
                                        $conn->query($sql);
                                }
                        }else{
                                Users::printInformation('Zadejte jiny nazev souboru');
                        }
                }
                else {
                        Users::printInformation('Zadejte jiny jmeno zbozi');
                }
        }
        static function addAttribute(){//prida atribut/velikost zbozi
                $conn = connection::getConnection();
                $param = $conn->query("SELECT gender,color,sale FROM db_dev.attribute WHERE GOODS_ID_goods = '{$_GET['goodsID']}'");
                $result = $param->fetch_assoc();
                $conn->query("INSERT INTO db_dev.attribute(gender,size,color,sale,GOODS_id_goods) VALUES('{$result['gender']}','{$_POST['attributeSize']}','{$result['color']}','{$result['sale']}','{$_GET['goodsID']}')");
        }
        static function printAllGoods(){//vypis vsech zbozi/vypisuje admin nebo zamestnanec
                if(!empty($_POST["reset"])){//pripadny reset filtru
                        unset($_POST["orderBy"]);
                        unset($_POST["typeOfGoodsFilter"]);
                        unset($_POST["available"]);
                }
                $conn = connection::getConnection();
                echo '<form method="post" action="">';
                echo '<input type="submit" name="exportJson" value="Export JSON" id="exportJsonButton">';
                echo '<input type="submit" name="importJson" value="Import JSON" id="importJsonButton">';
                echo '</form>';
                echo '<div class=list>';
                echo '<div class=listRow>';
                echo '<div class=detailsInRow>';
                echo '<form name="frmImage" enctype="multipart/form-data" action="" method="post"  >
                    <label class="w130">Název: </label><input name="name" type="text" class="inputsNextLabel"/>
                    <label class="w130">Cena: </label><input name="price" type="number" class="inputsNextLabel"/>
                    <label id="w170Size">Velikost: </label><input name="size" type="text" class="inputsNextLabel"/>
                    <br>
                     <label  class="w130">Pro: </label><select name="gender" class="inputsNextLabel">
                        <option value="m">Muže</option>
                        <option value="f">Ženy</option>
                        <option value="k">Děti</option>
                    </select>
                     <br>
                     <label  class="w130">Typ: </label><select name="typeOfGoods" class="inputsNextLabel" id="typeOfGoodsAddGoods">
                        <option value="shirts">Triko</option>
                        <option value="shorts">Trenýrky</option>
                        <option value="bags">Taška</option>
                        <option value="balls">Míč</option>
                        <option value="shoes">Kopačky</option>
                        <option value="protectors">Chrániče</option>
                        <option value="goalkeeper">Brankář</option>
                    </select>
                     <br>
                     <label  class="w130">Barva: </label><select name="color" class="inputsNextLabel" id="colorAddGoods">
                        <option value="black">Černá</option>
                        <option value="white">Bílá</option>
                        <option value="blue">Modrá</option>
                        <option value="green">Zelená</option>
                        <option value="yellow">Žlutá</option>
                        <option value="orange">Oranžová</option>
                        <option value="red">Červená</option>
                    </select>
                    <br>
                    <label class="dInline">Přidat obrázek:</label>
                    <input name="Filename" type="file" class="inputsNextLabel w300" id="addFile"/>
                    <input id="addGoodsButton" type="submit" value="Přidat zboží" name="addGoods" class="inputsNextLabel"/>
                </form>';
                echo '</div>';
                echo '</div>';
                echo '<div class=listRow>';
                echo '<div class=detailsInRow>';
                echo '<form action="" method="post">';

                Goods::setOrderBy("");
                Goods::setGoodsType();
                echo '<label id="availableGoods" class="w200">K dispozici: </label>
                      <select name="available" class="inputsNextLabel" onchange="this.form.submit()">';
                if(!empty($_POST["available"])&&$_POST["available"]=="available"){//nastaveni vybraneho optionu
                        echo '<option selected value="available">K dispozici</option>';
                        echo '<option  value="unavailable">Není k dispozici</option>';

                }else if (!empty($_POST["available"])&&$_POST["available"]=="unavailable"){
                        echo '<option  value="available">K dispozici</option>';
                        echo '<option selected value="unavailable">Není k dispozici</option>';
                }else{
                        echo '<option selected value="available">K dispozici</option>';
                        echo '<option  value="unavailable">Není k dispozici</option>';
                        $_POST["available"] = "available";
                }
                echo '</select>';
                echo '<input type="submit" name="reset" value="Resetovat filtr" class="inputsNextLabel">';

                echo '</form>';
                echo '</div>';
                echo '</div>';
                $sql = "SELECT DISTINCT image,id_goods,price,goods.name as goodsName,available,c.name,color FROM db_dev.goods
                                                    JOIN db_dev.goods_category gc on db_dev.goods.id_goods = gc.goods_id_goods
                                                    JOIN db_dev.category c on c.id_category = gc.category_id_category
                                                    JOIN db_dev.attribute a on goods.id_goods = a.goods_id_goods ";//vyber zbozi
                if(!empty($_POST["typeOfGoodsFilter"])){//pridani filtru k SQL dotazu
                        $sql = $sql." WHERE c.name = '{$_POST["typeOfGoodsFilter"]}'";
                }
                if(!empty($_POST["available"])){//pridani filtru k SQL dotazu
                        if(!empty($_POST["typeOfGoodsFilter"])){
                                if($_POST["available"]=="available"){
                                        $sql = $sql." AND available = 1";
                                }else{
                                        $sql = $sql." AND available = 0";
                                }
                        }else{
                                if($_POST["available"]=="available"){
                                        $sql = $sql." WHERE available =1";
                                }else{
                                        $sql = $sql." WHERE available =0";
                                }
                        }
                }
                if(!empty($_POST["orderBy"])&&$_POST["orderBy"]=="descending"){//pridani filtru k SQL dotazu
                        $sql = $sql." ORDER BY price DESC";
                }else{
                        $sql = $sql." ORDER BY price";
                }
                $result = $conn->query($sql);
                while($row = $result->fetch_assoc()){//prochazeni vybraneho zbozi
                        echo '<div class="listRow" id="listRowGoods">';
                        echo '<div class="goodsInfo">';

                        echo '<div class="detailsInRow">';
                        echo "Zboží: ".$row["goodsName"];
                        echo '<br>';
                        echo "Cena: ".$row["price"];
                        echo '<br>';
                        echo 'Barva: '. Goods::getColor($row["color"]);
                        echo '</div>';

                        echo '</div>';

                        echo '<div class="goodsSizeEdit">';
                        echo '<div class="w100h100Goods">';
                        echo '<img class="w100h100"  src='.$row["image"].'>';
                        echo '</div>';
                        if($_POST["available"]=="available") {//pokud se vypisuje zbozi ktere je K dispozici, muzeme mu pridat/odebrat atribut/velikost
                                echo '<div>';
                                        echo '<form action="index.php?pages=goodsManagement&action=addAttribute&goodsID=' . $row["id_goods"] . '" method="post" id="goodsForm">';
                                        echo '<input name="attributeSize" type="text" placeholder="Velikost . . .">';
                                        echo '<input type="submit" value="PŘIDAT">';
                                        echo '</form>';
                                        $result2 = $conn->query("SELECT color,size FROM db_dev.attribute WHERE GOODS_ID_goods = '{$row["id_goods"]}'");
                                        echo '<form method="post" action="index.php?pages=goodsManagement&action=deleteAttribute&goodsID=' . $row["id_goods"] . '">';
                                        echo '<select name="attributeColorSizeDelete" class="inputsNextLabel">';
                                        while ($row2 = $result2->fetch_assoc()) {
                                                echo '<option value=' . $row2["color"] . "," . $row2["size"] . '>' . $row2["size"] . '</option>';
                                        }
                                        echo '</select>';
                                        echo '<input class="inputsNextLabel" type="submit" value="SMAZAT">';
                                        echo '</form>';
                                echo '</div>';
                        }
                        echo '</div>';

                        echo '<div class="btnsInList">';
                        if($row["available"]==0) {//pokud zbozi neni k dispozici, muzeme ho smazat kompletne
                                echo'<a href="index.php?pages=goodsManagement&action=available&goodsID=' . $row["id_goods"] . '"><img class="w50h50" src="./imgs/icons/plus.png"></a>';
                                echo'<a href="index.php?pages=goodsManagement&action=deleteGoods&goodsID=' . $row["id_goods"] . '"><img class="w50h50" src="./imgs/icons/trash.png"></a>';
                        }else{//pokud je zbozi K dispozici, muzeme ho uvest do stavu kdy neni k dispozici
                                echo'<a href="index.php?pages=goodsManagement&action=unavailable&goodsID=' . $row["id_goods"] . '"><img class="w50h50" src="./imgs/icons/trash.png"></a>';
                        }
                        echo '<br>';
                        echo '</div>';
                        echo '</div>';
                }
                echo '</div>';
        }
        static function deleteAttribute(){//odebrani atributu
                $row = explode(",",$_POST["attributeColorSizeDelete"]);//zjisteni velikosti a nazvu zbozi
                $size = $row[1];
                $color = $row[0];
                $conn = connection::getConnection();
                $conn->query("DELETE FROM db_dev.attribute WHERE size = '$size' AND color = '$color' AND GOODS_ID_goods = '{$_GET["goodsID"]}'");
                $result = $conn->query("SELECT size FROM db_dev.attribute WHERE GOODS_ID_goods = '{$_GET["goodsID"]}'");
                if($result->num_rows<1){//pokud nezbyva zadna velikost, smaze se zbozi
                        Goods::deleteGoods($_GET["goodsID"]);
                }
        }
        static function setGoodsType(){//nastaveni selectu filtru podle typu zbozi
                $types = array(array("shirts","Triko"),array("shorts","Trenýrky"),array("bags","Taška"),
                         array("balls","Míč"),array("shoes","Kopačky"),array("protectors","Chrániče"),array("goalkeeper","Brankář"));
                if(!empty($_POST["available"])&&$_POST["available"]=="unavailable"){
                        echo '<label class="w130">Typ: </label><select disabled name="typeOfGoodsFilter" class="inputsNextLabel" onchange="this.form.submit()">';
                }else{
                        echo '<label class="w130">Typ: </label><select name="typeOfGoodsFilter" class="inputsNextLabel" onchange="this.form.submit()">';
                }
                echo '<option disabled selected>Typ . . .</option>';
                for ($i = 0;$i<7;$i++){
                        if(!empty($_POST["typeOfGoodsFilter"])&&$types[$i][0] == $_POST["typeOfGoodsFilter"]){
                                echo '<option selected value='.$types[$i][0].'>'.$types[$i][1].'</option>';
                        }else{
                                echo '<option value='.$types[$i][0].'>'.$types[$i][1].'</option>';
                        }
                }
                echo '</select>';
        }

        static function getColor($color){//preklad anglicke barvy na ceskou
                switch ($color) {
                        case "black":
                                return "Černá";
                        case "white":
                                return "Bílá";
                        case "blue":
                                return "Modrá";
                        case "green":
                                return "Zelená";
                        case "yellow":
                                return "Žlutá";
                        case "orange":
                                return "Oranžová";
                        case "red":
                                return "Červená";
                }
        }

}
?>

