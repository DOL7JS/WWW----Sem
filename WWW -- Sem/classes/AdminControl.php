<?php


class AdminControl
{

    public static function exportToJSon()
    {
        $allCat = GoodsDB::selectCategories();
        $json = json_encode($allCat);
        ob_start();
        $htmlStr = ob_get_contents();
        ob_end_clean();
        $file_url = 'category.json';

        header('Content-Type: application/octet-stream');
        header("Content-Transfer-Encoding: Binary");
        header("Content-disposition: attachment; filename=\"" . basename($file_url) . "\"");

        ob_clean();
        flush();
        echo $json;
        exit();
    }

    public static function importJson()
    {
        if(file_exists('category.json')){
            $categories = json_decode(file_get_contents('category.json'),true);
            foreach ($categories as $category){
                $checkCategory = GoodsDB::checkUniqueNameCategory($category["czech_name"],$category["name"]);
                if($checkCategory==null){//TODO predelat
                    GoodsDB::addCategory($category["czech_name"],$category["name"],$category["image"],$category["deleted"]);
                }else{
                    GoodsDB::updateCategoryStatusAndImage($category["id_category"],0,$category["image"]);
                }
            }
        }else{
            UserControl::printInformation('Neexistuje soubor category.json, doplnte ho ve vhodnem formatu');
        }
    }
    //----------------------SALES------------------------
    public static function printFormAddSale()
    {
        $goods = GoodsDB::selectAllAvailableGoods();
        echo '<div class=listRow>';
        echo '<div class=detailsInRow>';
        echo '<form method="post" id="addSaleForm">';
        echo '<label>Zboží: </label><select name="nameOfGoods" class="pRelBottom7">';
        foreach ($goods as $item){
            echo '<option>'.$item["name"].'</option>';
        }
        echo '</select>';
        echo '<label>  Sleva: </label><input type="number" name="sale" class="pRelBottom7">';
        echo '<input type="submit" value="Přidat slevu" class="pRelBottom7" id="addSaleButton" name="addSale">';
        echo '</form>';
        echo '</div>';
        echo '</div>';
    }

    public static function printSalesAsAdmin()
    {
        $goodsInSale = GoodsDB::selectGoodsInSale(null,null,null,null,null);
        foreach($goodsInSale as $goods){//prochazeni slev a jejich vypis
            echo '<div class=listRow>';
            echo '<div class="detailsInRow detailsInSale">';
            echo 'Zboží: '.$goods["name"];
            echo '<br>';
            echo "Sleva: ".$goods["sale"]." %";
            echo '<br>';
            echo "Původní cena: ".$goods["price"]." Kč";
            echo '<br>';
            echo "Po slevě: ".$goods["price"]*(1-$goods["sale"]/100)." Kč";
            echo '</div>';
            echo '<div id="btnsInSaleManagement" class="btnsInList">';
            echo '<a href="index.php?pages=saleManagement&action=deleteSale&goodsId=' . $goods["id_goods"] . '"><img class="w50h50 iconDeleteSale" src="./imgs/icons/trash.png"></a>';
            echo '<br>';
            echo '</div>';
            echo '</div>';
        }
    }

    public static function deleteSale($goodsId)
    {
        GoodsDB::deleteSale($goodsId);
    }
    public static function addSale($goodsName,$sale){//pridani slevy ke zbozi
        $goods = GoodsDB::selectGoodsByName($goodsName);
        GoodsDB::updateAttributeSale($goods["id_goods"],$sale);
    }
    //----------------CATEGORY--------------------
    public static function printFormAddCategory()
    {
        echo '<div class=listRow>';
        echo '<div class=detailsInRow>';
        echo '<form method="post" id="addCategoryForm"  enctype="multipart/form-data">';

        echo '<label class="w250" >Typ (česky): </label>';
        echo '<input id="categoryNameCzech" type="text" name="categoryNameCzech" class="inputsNextLabel">';
        echo '<br>';

        echo '<label class="w250">Typ (anglicky): </label>';
        echo '<input id="categoryNameEnglish" type="text" name="categoryNameEnglish" class="inputsNextLabel">';
        echo '<br>';

        echo '<input name="Filename" type="file" class="inputsNextLabel w300 addFile"/>';
        echo '<input type="submit" value="Přidat typ" class="pRelBottom7 pRelRight70" id="addCategoryButton" name="addCategoryButton">';
        echo '</form>';
        echo '</div>';
        echo '</div>';
    }
    public static function addCategory($czechNameOfCategory, $englishNameOfCategory, $file)
    {
        $image = "imgs/imgs_section/".$file;
        $category = GoodsDB::checkUniqueNameCategory($czechNameOfCategory, $englishNameOfCategory);
        if($category==null){
            $fileMove = move_uploaded_file($_FILES["Filename"]["tmp_name"],$image);//pridani obrazku do slozky 'imgs/imgs_section'
            if($fileMove){
                GoodsDB::addCategory($czechNameOfCategory, $englishNameOfCategory, $image,0);
                return true;
            }
            return false;
        }else{
            $category = GoodsDB::selectCategoryByName($englishNameOfCategory);
            GoodsDB::updateCategoryStatusAndImage($category["id_category"],0,$image);
            return true;
        }
    }
    public static function printAllCategoriesAsAdmin()
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

    public static function deleteCategory($deleteCategory)
    {
        GoodsDB::updateCategoryStatus($deleteCategory,1);
    }

    //---------------------ATTRIBUTE----------------------
    public static function printFormAddAttribute($idGoods)
    {
        $colors = Colors::getColors();
        echo '<div class=listRow>
                        <div class=detailsInRow>
                        <h3 class="textCenter">Přidání atributu</h3>
                        <form name="frmImage" enctype="multipart/form-data" action="index.php?pages=editGoods&action=addAttribute&goodsID=' . $idGoods. '"  method="post">';
        echo '<label>Velikost: </label>';
        echo '<input name="attributeSizeAdd" type="text" placeholder="Velikost . . ." class="inputsNextLabel">';
        echo '<label class="w105">Barva: </label>';
        echo '<select name="attributeColorAdd" class="inputsNextLabel">';
        GoodsControl::setOptionInSelect("attributeColor",$colors);
        echo '</select>
                <label class="dInline">Obrázek:</label>
                    <input name="Filename" type="file" class="w205 inputsNextLabel addFile" />';

        echo '<input type="submit" value="PŘIDAT" class="inputsNextLabel">';
        echo '</form>
                        </div>
                   </div>';
    }

    public static function deleteAttribute($idGoods, $size, $color)
    {
        GoodsDB::deleteAttribute($idGoods,$size,$color);
    }

    public static function printFormDeleteAttribute($goodsID)
    {
        $sizes = GoodsDB::getSizesOfGoodsById($goodsID);
        echo '<div class=listRow>
                        <div class=detailsInRow>
                         <h3 class="pRelLeft400">Odebrání atributu</h3>
                        <form action="index.php?pages=editGoods&action=deleteAttribute&goodsID=' . $goodsID. '"  method="post">';
        echo '<label>Velikost: </label>';
        echo '<select name="attributeSizeDelete" onchange="this.form.submit()" class="inputsNextLabel">';
        foreach ($sizes as $size){
            if(!empty($_POST["attributeSizeDelete"])&&$_POST["attributeSizeDelete"]==$size["size"]){
                echo '<option selected value="'.$size["size"].'">'.$size["size"].'</option>';
            }else{
                echo '<option  value="'.$size["size"].'">'.$size["size"].'</option>';
            }
        }
        echo '</select>';
        echo '<label class="w105">Barva: </label>';
        echo '<select name="attributeColorDelete" class="inputsNextLabel">';
        if(empty($_POST["attributeSizeDelete"])){
            $colors = GoodsDB::selectColorsByGoodsIdBySize($goodsID,$sizes[0]["size"]);
            foreach ($colors as $item){
                echo '<option value="'.$item["color"].'">'.Colors::getBarva($item["color"]).'</option>';
            }
        }else{
            $colors = GoodsDB::selectColorsByGoodsIdBySize($goodsID, $_POST["attributeSizeDelete"]);
            foreach ($colors as $item) {
                echo '<option value="' . $item["color"] . '">' . Colors::getBarva($item["color"]) . '</option>';
            }
        }
        echo '</select>';
        echo '<input type="submit" value="ODEBRAT" class="inputsNextLabel" name="deleteAttributeButton">';
        echo '</form>
                </div>
                </div>';
    }
    public static function addAttribute($id_goods, $size, $color,$image)
    {
        $attribute = GoodsDB::selectAttributeByIdGoodsBySizeByColor($id_goods,$size,$color);
        if($attribute==null){
            $goods = GoodsDB::getGoodsWithAvailableAttributeById($id_goods);
            GoodsDB::addAttribute($id_goods,$size,$color,$goods[0]["gender"],$goods[0]["sale"],$goods[0]["deleted"],$image);
            move_uploaded_file($_FILES["Filename"]["tmp_name"],$image);//pridani obrazku do slozky 'imgs/imgs_goods'
            return true;
        }else{
            UserControl::printInformation('Atribut již existuje, zkuste změnit velikost, barvu nebo název souboru');
            return false;
        }
    }
    //-----------------------GOODS------------------------

    public static function printGoodsAsAdmin($orderByPrice, $orderByCategory, $available)
    {
        $category = $orderByCategory==null?null: GoodsDB::selectCategoryByNameInCzech($orderByCategory)["id_category"];
        $allGoods = GoodsDB::selectAllGoodsWithAttributeOrderByPriceOrderByCategoryOrderByAvailable($category,$available,$orderByPrice);
        foreach ($allGoods as $goods){
            self::printGoodsInManagementGoods($goods);
        }
    }

    private static function printGoodsInManagementGoods($goods)
    {
        $attribute = GoodsDB::selectGoodsWithAttributeById($goods["id_goods"]);
        $attribute = $attribute[0];
        $category = GoodsDB::selectCategoryByGoodsName($goods["name"]);
        echo '<div class="listRow listRowGoods">';
        echo '<div class="goodsInfo">';
        echo '<div class="detailsInRow">';

        echo "Zboží: ".$goods["name"];
        echo '<br>';
        echo "Cena: ".$goods["price"];
        echo '<br>';
        echo "Typ: ".$category["czech_name"];
        echo '</div>';
        echo '</div>';

        echo '<div class="goodsEditAttribute">';
        echo '<div class="w100h100Goods">';
        echo '<img class=" imageGoodsManagement w100h100"  src='.$attribute["image"].'>';
        echo '</div>';
        echo '</div>';

        echo '<div id="btnsInGoodsManagement" class="btnsInList">';
        echo'<a href="index.php?pages=goodsManagement&action=edit&goodsID=' . $goods["id_goods"] . '"><img class="w50h50" src="./imgs/icons/edit.png"></a>';
        if($goods["available"]==0) {//pokud zbozi neni k dispozici, muzeme ho smazat kompletne
            echo'<a href="index.php?pages=goodsManagement&action=available&goodsID=' . $goods["id_goods"] . '"><img class="w50h50" src="./imgs/icons/plus.png"></a>';
            echo'<a href="index.php?pages=goodsManagement&action=deleteGoods&goodsID=' . $goods["id_goods"] . '"><img class="w50h50" src="./imgs/icons/trash.png"></a>';
        }else{//pokud je zbozi K dispozici, muzeme ho uvest do stavu kdy neni k dispozici
            echo'<a href="index.php?pages=goodsManagement&action=unavailable&goodsID=' . $goods["id_goods"] . '"><img class="w50h50" src="./imgs/icons/trash.png"></a>';
        }
        echo '<br>';
        echo '</div>';
        echo '</div>';
    }

    public static function printFormEditGoods($idGoods)
    {
        $goods = GoodsDB::selectGoodsById($idGoods);
        echo '<div class=listRow>
                <div class=detailsInRow>            
                    <h3 class="pRelLeft460">Úprava zboží</h3>
                    <form action="index.php?pages=editGoods&action=updateGoods&goodsID=' . $idGoods. '" method="post"  >
                                <label>Název: </label><input value='.$goods["name"].' name="name" type="text" class="inputsNextLabel"/>
                                <label class="w130">Cena: </label><input value='.$goods["price"].' name="price" type="number" class="inputsNextLabel"/>
                                <input  type="submit" value="Upravit zboží" name="addGoods" class="inputsNextLabel addGoodsButton"/>
                    </form>
                </div>
              </div>';

    }

    public static function printFormAddGoods()
    {
        $colors =Colors::getColors();
        echo '               
        <div class=listRow>
        <div class=detailsInRow>
        <form name="frmImage" enctype="multipart/form-data" action="" method="post"  >
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
       <label class="w130">Typ: </label>
       <select name="typeOfGoods" class="inputsNextLabel pRelRight10" id="typeOfGoodsAddGoods">';
        $categories = GoodsDB::selectCategories();
        foreach ($categories as $category){
            echo '<option value="'.$category["name"].'">'.$category["czech_name"].'</option>';
        }
                    echo '</select>
                        <br>
                     <label  class="w130">Barva: </label>
                     <select name="color" class="inputsNextLabel pRelRight10" id="colorAddGoods">';
                    GoodsControl::setOptionInSelect("",$colors);
                    echo '</select>
                    <br>
                    <label class="dInline">Přidat obrázek:</label>
                    <input name="Filename" type="file" class="inputsNextLabel w300 addFile"/>
                    <input type="submit" value="Přidat zboží" name="addGoods" class="inputsNextLabel addGoodsButton"/>
                </form>
            </div>
        </div>';
    }
    //------------------FILTER---------------------
    public static function printFilterInGoodsManagement()
    {
        echo '<div class=listRow>
                  <div class=detailsInRow>
                      <form action="" method="post">';
        GoodsControl::setOrderByPriceFilterInGoodsManagement();
        GoodsControl::setOrderByCategoryInGoodsManagement();
        GoodsControl::setOrderByAvailableInGoodsManagement();
        echo '<input type="submit" name="reset" value="Resetovat filtr" class="inputsNextLabel">';
        echo '</form>
                </div>
            </div>';
    }

    public static function printFormAddUser()
    {
        echo '<div class=listRow>
                    <div class=detailsInRow >
                        <form name="addUser" class="dFlex" id="addUserForm" method="post" action="index.php?pages=usersManagement">
                                    <div>
                                    <label class="w130" >Email: </label>
                                    <input class="pRelBottom7"  name="email" type="email" >
                                          </div>
                                    <div >
                                    <label class="w130" >Heslo: </label>
                                    <input class="pRelBottom7"  name="password" type="password" >
                                </div>
                                <div>
                                <label class="w130" >Role: </label>
                                <select name="role" class="pRelRight5 pRelBottom7"><option>Zákazník</option><option>Zaměstnanec</option></select>
                                </div>
                                <div >  
                                <input type="submit" value="Přidat" id="addUserButton" name="addUserButton" >
                                </div>
                        </form>
                    </div>
                </div>';
    }
    public static function printAllUsersAsAdmin()
    {
        $users = UserDB::selectAllUsers();
        foreach($users as $user){//prochazeni a vypis vsech uzivatelu
            echo '<div class=listRow>';
            echo '<div class="detailsInRow detailsInRowUsers">';
            echo $user['email'];
            echo "<br>";
            echo "Role: ".$user["role"];
            echo '</div>';
            echo '<div id="btnsInUsersManagement" class="btnsInList">';
            if($user["role"]!="Admin"){
                echo'<a href="index.php?pages=usersManagement&action=deleteUser&idUser='.$user['id_user']. '" ><img class="w50h50 iconsUserManagement" src="./imgs/icons/trash.png" alt="trash.png"></a>';
            }
            echo'<a href="index.php?pages=usersManagement&action=editUser&idUser='.$user['id_user']. '" ><img class="w50h50 iconsUserManagement" src="./imgs/icons/edit.png" alt="edit.png"></a>';
            echo '</div>';
            echo '</div>';
        }
    }

    public static function printEditUserAsAdmin($idUser){//vypis informaci k upravovanemu uzivateli
        echo '<h1>Úprava účtu</h1>';
        $user = UserDB::selectUserById($idUser);
        if($user!=null){
            echo '<form id="editUserForm" action="index.php?pages=editUser&action=edited" method="post" class="pRelLeft40p accountBox">
                    <div><label>Email: </label><input type="email" name="email" placeholder="Email . . ."  value="'.$user["email"]. '" ></div>
                    <div><label>Heslo: </label><input type="password" name="password"  placeholder="Heslo . . ."></div>
                    <div><label>Role: </label>';
            if($user["role"]=="Admin"){
                echo '<select disabled name="role">';
            }else{
                echo '<select name="role">';
            }
            switch ($user["role"]){
                case "Zákazník":
                    echo '<option selected>Zákazník</option>
                          <option>Zaměstnanec</option>
                          <option>Admin</option>';break;
                case "Zaměstnanec":
                    echo '<option >Zákazník</option>
                          <option selected>Zaměstnanec</option>
                          <option>Admin</option>';break;
                case "Admin":
                    echo '<option>Zákazník</option>
                          <option>Zaměstnanec</option>
                          <option selected>Admin</option>';break;
            }
            echo '</select></div>
               <div><label></label><input type="submit" value="Potvrdit"></div></form>';
        }
    }
}