<?php


class CartControl
{
    static function addToCart($id,$size,$color)
    {
        if(!empty($_SESSION["cart"])){
            if (array_key_exists($id, $_SESSION["cart"])) {
                if(array_key_exists($size,$_SESSION["cart"][$id])){
                    if(array_key_exists($color,$_SESSION["cart"][$id][$size])) {
                        $_SESSION["cart"][$id][$size][$color]["quantity"]++;
                    } else {
                        $_SESSION["cart"][$id][$size][$color]["quantity"] = 1;
                    }
                } else {
                    $_SESSION["cart"][$id][$size][$color]["quantity"] = 1;
                }
            } else {
                $_SESSION["cart"][$id][$size][$color]["quantity"] = 1;
            }
        }else{
            $_SESSION["cart"][$id][$size][$color]["quantity"] = 1;
        }
    }
    static function removeGoods($id,$size,$color)
    {
        if($_SESSION["cart"][$id][$size][$color]["quantity"]==1){
            CartControl::deleteGoods($id,$size,$color);
        }else{
            $_SESSION["cart"][$id][$size][$color]["quantity"]--;
        }
    }
    static function deleteGoods($id,$size,$color)
    {
        unset($_SESSION["cart"][$id][$size][$color]);
        if(count($_SESSION["cart"][$id][$size])==0){
            unset($_SESSION["cart"][$id][$size]);
        }
        if(count($_SESSION["cart"][$id])==0){
            unset($_SESSION["cart"][$id]);
        }
    }
    public static function clearCart()
    {
        unset($_SESSION["cart"]);
    }
    static function printCart(){
        if(!empty($_SESSION["cart"])){//kontrola zda v kosiku je zbozi
            $totalPrice = 0;
            echo '<div class=list>';
            $i = 0;
            foreach ($_SESSION["cart"] as $key => $value) {//prochazeni polozek v kosiku
                    foreach ($value as $size=>$vv){
                        foreach ($vv as $color=>$vvv){
                            foreach ($vvv as $quantity=>$vvvv) {
                                $row = GoodsDB::getGoodsWithAvailableAttributeById($key);
                                $attribute = GoodsDB::selectAttributeByIdGoodsBySizeByColor($row[0]["id_goods"],$size,$color);
                                $row = $row[$i++];
                                $price = ($row["price"]*(1-$row["sale"]/100)) * $vvv["quantity"];//spocitani ceny zbozi na zaklade ceny, slevy a a poctu kusu
                                self::printGoodsInCart($row["name"],$vvv["quantity"],$price,$size,Colors::getBarva($color),$key,$attribute["image"]);
                                $totalPrice+=$price;
                        }
                    }
                }
                $i = 0;
            }
            echo '<div class="completeCart" >
                        <div id="totalPrice">';
                            echo 'Celkova cena objednavky: '.$totalPrice." Kč";
                        echo '</div>
                        <form class="completeOrder" action="index.php?pages=cart" method="post">
                            <input class="completeOrderButton" name="completeOrder" type="submit" value="Přejít k platbě">
                        </form>
                      </div>
                    </div>';
        }else{
            echo '<p id="noGoods" class="center accountBox">';
            echo "V kosiku nic neni";
            echo '</p>';
        }
    }
    private static function printGoodsInCart($name,$quantity,$price,$size,$color,$idGoods,$image){

        echo '<div class="listRow">';
        echo '<div class="detailsInRow detailsInRowCart">';
        echo "<p class='detailCart'>Zboží: ".$name."</p>";
        echo "<br>";
        echo "<p class='detailCart'>Mnozstvi: ".$quantity."</p>";
        echo "<br>";
        echo "<p class='detailCart'>Cena: ".$price."</p>";
        echo "<br>";
        echo "<p class='detailCart'>Velikost: ".$size."</p>";
        echo "<br>";
        echo "<p class='detailCart'>Barva: ".$color."</p>";
        echo '</div>';
        echo '<div class="imgOrderDetail">';
        echo '<img class="imgCart" src='.$image.' alt='.$image.'>';
        echo '</div>';
        echo '<div class="btnsInList btnsInCart">';
        //delete - odebrat vsechno zbozi jednoho typu
        //remove - snizit mnozstvi jednoho zbozi
        echo'<a href="index.php?pages=cart&action=add&idGoods=' . $idGoods . '&size='.$size.'&color='.Colors::getColor($color).'" ><img class="w50h50" src="./imgs/icons/plus.png" alt=" . $key . "></a>';
        echo'<a href="index.php?pages=cart&action=remove&idGoods=' . $idGoods . '&size='.$size.'&color='.Colors::getColor($color).'"><img class="w50h50" src="./imgs/icons/minus.png" alt=" . $key . "></a>';
        echo'<a href="index.php?pages=cart&action=delete&idGoods=' . $idGoods . '&size='.$size.'&color='.Colors::getColor($color).'"><img class="w50h50" src="./imgs/icons/trash.png" alt=" . $key . "></a>';
        echo '<br>';
        echo '</div>';
        echo '</div>';

    }
}