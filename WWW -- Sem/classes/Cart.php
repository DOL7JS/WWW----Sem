<?php


class Cart
{
    static function addToCart($goodsName)
    {
        if(!empty($_SESSION["cart"])){
            if (!array_key_exists($goodsName, $_SESSION["cart"])) {
                $_SESSION["cart"][$goodsName]["quantity"] = 1;
            } else {
                $_SESSION["cart"][$goodsName]["quantity"]++;
            }
        }else{
            $_SESSION["cart"][$goodsName]["quantity"] = 1;
        }
    }
    static function removeGoods($goodsName)
    {
        if($_SESSION["cart"][$goodsName]["quantity"]==1){
            Cart::deleteGoods($goodsName);
        }else{
            $_SESSION["cart"][$goodsName]["quantity"]--;
        }
    }
    static function deleteGoods($goodsName)
    {
        unset($_SESSION["cart"][$goodsName]);
    }
    static function printCart(){
        if(!empty($_SESSION["cart"])){//kontrola zda v kosiku je zbozi
            $conn = connection::getConnection();
            $totalPrice = 0;
            echo '<div class=list>';
            foreach ($_SESSION["cart"] as $key => $value) {//prochazeni polozek v kosiku
                $idSize = explode(",",$key);//rozdeleni nazvu a velikosti
                $result = $conn->query("SELECT name,price,image,sale,color FROM db_dev.goods 
                                                JOIN db_dev.attribute a on goods.id_goods = a.goods_id_goods WHERE id_goods='$idSize[0]'");//zjisteni informaci o zbozi na zaklade jmena
                $row = $result->fetch_assoc();
                $price = ($row["price"]*(1-$row["sale"]/100)) * $value["quantity"];//spocitani ceny zbozi na zaklade ceny, slevy a a poctu kusu
                echo '<div class="listRow">';
                    echo '<div id="detailsInRowCart" class="detailsInRow">';
                        echo "<p class='detailCart'>Zboží: ".$row["name"]."</p>";
                        echo "<br>";
                        echo "<p class='detailCart'>Mnozstvi: ".$value["quantity"]."</p>";
                        echo "<br>";
                        echo "<p class='detailCart'>Cena: ".$price."</p>";
                        echo "<br>";
                        echo "<p class='detailCart'>Velikost: ".$idSize[1]."</p>";
                        echo "<br>";
                        echo "<p class='detailCart'>Barva: ".Goods::getColor($row["color"])."</p>";
                    echo '</div>';

                    echo '<div class="imgOrderDetail">';
                        echo '<img id="imgCart" src='.$row["image"].' alt='.$row["image"].'>';
                    echo '</div>';
                    echo '<div id="btnsInCart" class="btnsInList">';
                        //delete - odebrat vsechno zbozi jednoho typu
                        //remove - snizit mnozstvi jednoho zbozi
                        echo'<a href="index.php?pages=cart&action=add&goodsName=' . $key . '" ><img class="w50h50" src="./imgs/icons/plus.png" alt=" . $key . "></a>';
                        echo'<a href="index.php?pages=cart&action=remove&goodsName=' . $key . '"><img class="w50h50" src="./imgs/icons/minus.png" alt=" . $key . "></a>';
                        echo'<a href="index.php?pages=cart&action=delete&goodsName=' . $key . '"><img class="w50h50" src="./imgs/icons/trash.png" alt=" . $key . "></a>';
                        echo '<br>';
                    echo '</div>';
                echo '</div>';
                $totalPrice+=$price;
            }
                echo '<div id="completeCart" >';
                    echo '<div id="totalPrice">';
                        echo 'Celkova cena objednavky: '.$totalPrice." Kč";
                    echo '</div>';
                    echo '<form class="completeOrder" action="index.php?pages=cart" method="post">';
                        echo '<input name="completeOrder" type="submit" value="Přejít k platbě">';
                    echo '</form>';
                echo '</div>';
            echo '</div>';
    }else{
            echo '<p id="noGoods" class="center accountBox">';
            echo "V kosiku nic neni";
            echo '</p>';
        }
    }
}