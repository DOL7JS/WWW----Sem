<?php


class GoodsControl
{
    //-------------FILTER-------------------
    public static function setFilter(){
        echo '<form method="post" class="filter">';
        self::setOrderByPriceFilter();
        self::setOrderBySizeFilter();
        self::setOrderByColorFilter();
        echo '<input type="submit" name="reset" value="Resetovat filtr" >
            </form>';
    }
    public static function setFilterInSale()
    {
        echo '<form method="post" class="filter">';
        self::setOrderByCategoryInSale();
        self::setOrderByGenderInSale();
        self::setOrderByPriceFilter();
        echo '<br>';
        self::setOrderBySizeFilterInSale();
        self::setOrderByColorFilterInSale();
        echo '<input type="submit" name="reset" value="Resetovat filtr" id="resetFilterSale" >
            </form>';
    }

    private static function resetFilter($filterType){
        if(!empty($_POST["reset"])) {//reset filtru
            unset($_POST[$filterType]);
        }
    }
    public static function setOptionInSelect($postValue, $filterArray){
        for ($i = 0;$i<count($filterArray);$i++){//nastaveni vybraneho optionu
            if(!empty($_POST[$postValue])&&$filterArray[$i][0] == $_POST[$postValue]){
                echo '<option selected value='.$filterArray[$i][0].'>'.$filterArray[$i][1].'</option>';
            }else{
                echo '<option value='.$filterArray[$i][0].'>'.$filterArray[$i][1].'</option>';
            }
        }
    }
    private static function setOptionInSelectSize($postValue, $filterArray)
    {
        for ($i = 0;$i<count($filterArray);$i++){//nastaveni vybraneho optionu
            if(!empty($_POST[$postValue])&&$filterArray[$i]["size"] == $_POST[$postValue]){
                echo '<option selected value='.$filterArray[$i]["size"].'>'.$filterArray[$i]["size"].'</option>';
            }else{
                echo '<option value='.$filterArray[$i]["size"].'>'.$filterArray[$i]["size"].'</option>';
            }
        }
    }
    private static function setOptionInSelectColor($postValue, $filterArray)
    {
        for ($i = 0;$i<count($filterArray);$i++){//nastaveni vybraneho optionu
            if(!empty($_POST[$postValue])&&$filterArray[$i]["color"] == $_POST[$postValue]){
                echo '<option selected value='.$filterArray[$i]["color"].'>'.Colors::getBarva($filterArray[$i]["color"]).'</option>';
            }else{
                echo '<option value='.$filterArray[$i]["color"].'>'.Colors::getBarva($filterArray[$i]["color"]).'</option>';
            }
        }
    }
    private static function setOptionInSelectCategory($postValue, $categories)
    {
        for ($i = 0;$i<count($categories);$i++){//nastaveni vybraneho optionu
            if(!empty($_POST[$postValue])&&$categories[$i] == $_POST[$postValue]){
                echo '<option selected value='.$categories[$i].'>'.$categories[$i].'</option>';
            }else{
                echo '<option value='.$categories[$i].'>'.$categories[$i].'</option>';
            }
        }
    }
    private static function setOrderByGenderInSale(){
        $gender = array(array("m","Muži"),array("f","Ženy"),array("k","Děti"));
        echo '<label class="filterCategory">Pohlaví: </label><select name="filterByGender" onchange="this.form.submit()">';
        echo '<option disabled selected>Pohlaví . . .</option>';
        self::resetFilter("filterByGender");
        self::setOptionInSelect("filterByGender",$gender);
        echo '</select>';
    }
    public static function setOrderByCategoryInGoodsManagement(){
        $categories = GoodsDB::selectCategoriesCzechNames();
        echo '<label class="filterCategory w130">Typ: </label>
        <select name="filterByCategory" class="inputsNextLabel" onchange="this.form.submit()">';
        echo '<option disabled selected>Typ . . .</option>';
        self::resetFilter("filterByCategory");
        self::setOptionInSelectCategory("filterByCategory",$categories);
        echo '</select>';
    }
    public static function setOrderByCategoryInSale(){
        $categories = GoodsDB::selectCategoriesCzechNames();
        echo '<label class="filterCategory">Typ: </label><select name="filterByCategory" onchange="this.form.submit()">';
        echo '<option disabled selected>Typ . . .</option>';
        self::resetFilter("filterByCategory");
        self::setOptionInSelectCategory("filterByCategory",$categories);
        echo '</select>';
    }

    private static function setOrderBySizeFilterInSale()
    {
        $sizes = GoodsDB::selectAllSizesOfGoodsInSale();
        echo '<label class="filterCategory">Velikost: </label><select name="filterBySize" onchange="this.form.submit()">';
        echo '<option disabled selected>Typ . . .</option>';
        self::resetFilter("filterBySize");
        self::setOptionInSelectCategory("filterBySize",$sizes);
        echo '</select>';
    }


    public static function setOrderByPriceFilter(){
        $orderBy = array(array("ascending","Vzestupně"),array("descending","Sestupně"));
        self::resetFilter("orderByPrice");
        echo '<label class="w130" >Cena: </label>
              <select class="w130" name="orderByPrice"  onchange="this.form.submit()">
              <option disabled selected>Cena . . .</option>';
        self::setOptionInSelect("orderByPrice",$orderBy);
        echo '</select>';
    }
    public static function setOrderByPriceFilterInGoodsManagement(){
        $orderBy = array(array("ascending","Vzestupně"),array("descending","Sestupně"));
        self::resetFilter("orderByPrice");
        echo '<label class="orderByPriceFilter w130" >Cena: </label>
              <select name="orderByPrice" class="inputsNextLabel" onchange="this.form.submit()">
              <option disabled selected>Cena . . .</option>';
        self::setOptionInSelect("orderByPrice",$orderBy);
        echo '</select>';
    }

    private static function setOrderBySizeFilter(){
        $sizes = GoodsDB::getSizesOfAllGoods(GoodsDB::selectCategoryByName($_GET["goods"])["id_category"],$_GET["gender"]);
        echo '<label id="sizeFilter" class="w130">Velikost: </label>
              <select name="filterBySize" class="w130" onchange="this.form.submit()">
                <option disabled selected>Velikost . . .</option>';
        self::resetFilter("filterBySize");
        self::setOptionInSelectSize("filterBySize",$sizes);
        echo '</select>';
    }

    private static function setOrderByColorFilter(){
        $colors = Colors::getColors();
        echo '<label id="colorFilter" class="w130" >Barva:</label>
              <select class="w130" name="filterByColor" onchange="this.form.submit()">';
        echo '<option disabled selected>Barva . . .</option>';
        self::resetFilter("filterByColor");
        self::setOptionInSelect("filterByColor",$colors);
        echo '</select>';
    }
    private static function setOrderByColorFilterInSale(){
        $colors = Colors::getColors();
        echo '<label id="colorFilter">Barva:</label>
                      <select name="filterByColor" class="pRelRight5" onchange="this.form.submit()">';
        echo '<option disabled selected>Barva . . .</option>';
        self::resetFilter("filterByColor");
        self::setOptionInSelect("filterByColor",$colors);
        echo '</select>';
    }

    public static function setOrderByAvailableInGoodsManagement()
    {
        $available = array(array("available","K dispozici"),array("unavailable","Není k dispozici"));
        if(empty($_POST["orderByAvailable"])){
            $_POST["orderByAvailable"] = "available";
        }
        echo '<label class="filterCategory w200" >K dispozici: </label>
              <select name="orderByAvailable" class="inputsNextLabel" onchange="this.form.submit()">';
        self::setOptionInSelect("orderByAvailable",$available);
        echo '</select>';
    }

    public static function setGoodsByCategoryAndGender($category,$gender){
        $filterSize = empty($_POST["filterBySize"])?null:$_POST["filterBySize"];
        $filterColor = empty($_POST["filterByColor"])?null:$_POST["filterByColor"];
        $filterPrice = empty($_POST["orderByPrice"])?null:$_POST["orderByPrice"];
        self::printListOfGoods(GoodsDB::getGoodsByCategoryAndGender(GoodsDB::selectCategoryByName($category)["id_category"],$gender,$filterColor,$filterSize,$filterPrice));
    }

    private static function printListOfGoods($goods){
        echo '<div class="allGoods">';
           foreach($goods as $value) {
               $goodsAttr = GoodsDB::getGoodsWithAvailableAttributeById($value["id_goods"]);
               if ($value["sale"] != 0) {
                       self::printOneGoodsWithSale($goodsAttr[0]);
                   } else {
                       self::printOneGoods($goodsAttr[0]);
                   }
               }
        echo '</div>';
    }
    public static function printGoodInSale()
    {
        $goodsInSale = GoodsDB::selectGoodsInSale();
        echo '<div class="allGoods">';
        foreach ($goodsInSale as $goods){
            $goodsAttr = GoodsDB::getGoodsWithAvailableAttributeById($goods["id_goods"]);
            self::printOneGoodsWithSale($goodsAttr[0]);
        }
        echo '</div>';
    }
    private static function printOneGoods($row){
        $sizes = GoodsDB::getSizesOfGoodsById($row["id_goods"]);
        $colors = GoodsDB::getColorsOfGoodsById($row["id_goods"]);
        echo '<div class="goods pad100-50-50-50">
              <img class="imageGoods" src=' .$row["image"].' alt='.$row["image"].'>
              <section id="textZbozi">
                <div> Zboží: '.$row["name"].'</div>
                <div>Cena: '.$row["price"]*(1-$row["sale"]/100)." Kč".'</div>
                </section>
              <form class="pTop25" action='.$_SERVER['REQUEST_URI'].'&action=add&idGoods='.$row["id_goods"].'  method="post">
                <select name="goodsSize">';
        self::setOptionInSelectSize("goodsSize",$sizes);
        echo '</select>';
        echo '<select name="goodsColor" >';
        self::setOptionInSelectColor("goodsColor",$colors);
        echo '</select>';
        self::printAddToCartButton();
        echo '</form></div>';
    }

    private static function printOneGoodsWithSale($row){
        $colors = GoodsDB::getColorsOfGoodsById($row["id_goods"]);
        $sizes = GoodsDB::getSizesOfGoodsById($row["id_goods"]);
        echo '<div class="goods p50-50-100-50">
              <div class="triangle-topright">
              <p class="textInSale">SLEVA</p></div>
              <img class="imageGoods" src=' .$row["image"].' alt='.$row["image"].'>
              <section id="textZbozi">
                <div> '.$row["name"].'</div>
                <div>Původní cena: '.$row["price"]." Kč".'</div>
                <div>Cena: '.$row["price"]*(1-$row["sale"]/100)." Kč".'</div>
                </section>
              <form  method="post" action='.$_SERVER['REQUEST_URI'].'&action=add&idGoods='.$row["id_goods"].'>
                 <select name="goodsSize" onchange="this.form.submit()">';
        self::setOptionInSelectSize("goodsSize",$sizes);
        echo '</select>';
        echo '<select name="goodsColor">';
        self::setOptionInSelectColor("goodsColor",$colors);
        echo '</select>';
        self::printAddToCartButton();
        echo '</form></div>';
    }

    private static function printAddToCartButton(){
        if(self::canUserBuy()) {//pokud je nezaregistrovany uzivatel, muze dat do kosik
            echo '<input type="submit" value="Do košíku" name="addToCart">';
        }
    }

    private static function canUserBuy(){
        return empty($_SESSION["role"])||UserControl::isUserCustomer();//pokud je nezaregistrovany uzivatel, muze dat do kosik
    }

    public static function printAllCategories()
    {
        $categories = GoodsDB::selectCategories();
        foreach ($categories as $category){
            echo '<div class=listRow>';
            echo '<div class=detailsInRow>';
            echo "Český název: ".$category["czech_name"];
            echo '<br>';
            echo "Anglický název: ".$category["name"];
            echo '</div>';
            echo '<img src="'.$category["image"].'" class="w100h100">';
            echo '<div id="btnsInCategoryManagement" class="btnsInList">';
            echo '<a href="index.php?pages=categoryManagement&action=deleteCategory&goodsID=' . $category["id_category"] . '"><img class="w50h50" src="./imgs/icons/trash.png"></a>';
            echo '<br>';

            echo '</div>';
            echo '</div>';
        }
    }
    public static function updateGoods($idGoods, $name, $price)
    {
        GoodsDB::updateGoods($idGoods, $name, $price);
    }
    public static function addGoods($filename,$goodsName,$price,$size,$gender,$category,$color){

        $goodsCheck = GoodsDB::selectGoodsByName($goodsName);
        if($goodsCheck==null){
            $image = "imgs/imgs_goods/".$filename;
            $fileMove = move_uploaded_file($_FILES["Filename"]["tmp_name"],$image);//pridani obrazku do slozky 'imgs/imgs_goods'
            if($fileMove){
                $category = GoodsDB::selectCategoryByName($category)["id_category"];
                GoodsDB::insertGoods($goodsName,$price,$category);
                $insertedGoods = GoodsDB::selectGoodsByName($goodsName);
                GoodsDB::insertAttribute($gender,$color,$size,$insertedGoods["id_goods"],$image);
                return true;
            }
        }
        return false;
    }
    static function setGoodsUnavailable($idOfGoods){//nastaveni zbozi na 'Neni k dispozici'
        GoodsDB::setGoodsUnavailable($idOfGoods);
    }

    public static function setGoodsAvailable($idOfGoods)
    {
        GoodsDB::setGoodsAvailable($idOfGoods);
    }

    public static function deleteGoods($idOfGoods)
    {
        GoodsDB::deleteGoods($idOfGoods);
    }
}