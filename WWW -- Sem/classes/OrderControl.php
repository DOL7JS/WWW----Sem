<?php


class OrderControl
{

    static function updateOrders($ordersInArray){//aktualizace stavu objednavek
        OrderDB::updateOrderStatusToFalse();
        OrderDB::updateOrderStatusToTrue($ordersInArray);//TODO predelat v OrderDB
        UserControl::printInformation('Objednávky uloženy');
    }
    public static function addOrder($first_name,$last_name,$phone_number,$city,$street,$home_number,$zip_code)
    {
        if(UserDB::checkUserUniqueAddress($first_name,$last_name,$phone_number,$city,$street,$home_number,$zip_code,$_SESSION["idUser"])){//zjisti se jestli je adresa unikatni
            if(empty($_SESSION["delivery_info"]["save_delivery_info"])){//rozhoduje se, zda se ma adresa ulozit
                UserDB::insertAddressToDeliveryInfo($first_name,$last_name,$phone_number,$city,$street,$home_number,$zip_code,0,$_SESSION["idUser"]);
            }else{
                UserDB::insertAddressToDeliveryInfo($first_name,$last_name,$phone_number,$city,$street,$home_number,$zip_code,1,$_SESSION["idUser"]);
            }
            //pridani objednavky s posledni vlozenou adresou
            OrderDB::addOrder($_SESSION["idUser"],$_SESSION["delivery_info"]["paymentMethod"],$_SESSION["delivery_info"]["deliveryMethod"],UserDB::selectIdOfLastAddedDeliveryInfo());

        }else{
            //pridani objednavky s adresou, ktera uz existuje
            OrderDB::addOrder($_SESSION["idUser"],$_SESSION["delivery_info"]["paymentMethod"],$_SESSION["delivery_info"]["deliveryMethod"],
                UserDB::selectIdAddress($first_name,$last_name,$phone_number,$city,$street,$home_number,$zip_code,$_SESSION["idUser"])["id_delivery_info"]);
            if(!empty($_SESSION["delivery_info"]["save_delivery_info"])){
                UserDB::updateAddressStatus($first_name,$last_name,$phone_number,$city,$street,$home_number,$zip_code,$_SESSION["idUser"],1);
            }
        }
        $order = OrderDB::selectLastAddedOrder();
        $i =0;
        foreach ($_SESSION["cart"] as $id => $value) {//prochazeni polozek v kosiku
            foreach ($value as $size=>$vv){
                foreach ($vv as $color=>$vvv){
                    foreach ($vvv as $quantity=>$vvvv) {
                        $goods = GoodsDB::getGoodsWithAvailableAttributeById($id);
                        $goods = $goods[$i++];
                        $attribute = GoodsDB::selectIdAttribute(GoodsDB::selectGenderOfGoodsById($id),$color,$size,$goods["sale"],$goods["id_goods"]);
                        echo  (($goods["sale"]/100)+1)*$goods["price"];
                        OrderDb::addGoodsToOrderedGoods($order["id_order"],$goods["id_goods"],$attribute,((1-$goods["sale"]/100))*$goods["price"],$vvv["quantity"]);
                    }
                }
            }
            $i = 0;
        }
        CartControl::clearCart();
    }


    //-------------------PRINT_ORDERS-----------------------
    public static function printOrderSummary()
    {
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
                        self::printGoodsInOrderSummary($row["name"],$vvv["quantity"],$price,$size,Colors::getBarva($color),$attribute["image"]);
                        $totalPrice+=$price;
                    }
                }
            }
            $i = 0;
        }
        $deliveryMethod = OrderDB::selectDeliveryMethodById($_SESSION["delivery_info"]["deliveryMethod"]);
        $paymentMethod = OrderDB::selectPaymentMethodById($_SESSION["delivery_info"]["paymentMethod"]);
        $totalPrice+=$deliveryMethod["price"]+$paymentMethod["price"];
        echo '<div class="completeCart" >';
        echo '<div id="totalPrice">';
        if($_SESSION["delivery_info"]["paymentMethod"]==1){
            echo 'Platba kartou: '.$paymentMethod["price"]." Kč";
            echo '<br>';
        }else{
            echo 'Dobírka: '.$paymentMethod["price"]." Kč";
            echo '<br>';
        }
        echo 'Doprava: '.$deliveryMethod["price"]." Kč";
        echo '<br>';
        echo 'Celkova cena objednavky: '.$totalPrice." Kč";
        echo '</div>';

        echo '<form class="completeOrder" action="" method="post">';
        echo '<input class="completeOrderButton" name="completeOrder" type="submit" value="Objednat">';
        echo '</form>';

        echo '</div>';
        echo '</div>';
        echo '</div>';
    }

    private static function printGoodsInOrderSummary($name,$quantity,$price,$size,$color,$image){
        echo '<div class="listRow">
              <div class="detailsInRow detailsInRowCart">';
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
        echo '</div>';
    }

    public static function printMyOrders(){
        echo '<h1>Moje objednávky</h1>';
        $orders = OrderDB::selectOrdersOfUser($_SESSION["idUser"]);
        echo '<div class=list>';
        foreach ($orders as $order){
            self::printOrder($order);
        }
        echo '</div>';
    }

    private static function printOrder($order){
        $orderedGoods = OrderDB::selectOrderedGoodsByOrderId($order["id_order"]);
        $totalPrice = 0;
        foreach ($orderedGoods as $goods){
            $totalPrice+=$goods["price"]*$goods["quantity"];//spocitani ceny objednavky
        }
        echo '<div class="listRowMyOrders listRow">';
        echo '<div class="detailsInRow">';
        $idOrder = $order["id_order"];
        $year = substr($order["date_of_order"],0,4);
        $month = substr($order["date_of_order"],5,2);
        $day = substr($order["date_of_order"],8,2);
        $time = substr($order["date_of_order"],11,5);
        echo "Id objednávky: ".$idOrder."<br> Datum objednání: ".$day.". ".$month.". ".$year." ".$time;
        echo '<br>';
        echo 'Cena objednávky: '.$totalPrice.' Kč';
        echo '<br>';
        $address = UserDB::selectAddressOfOrder($idOrder);
        echo 'Adresa: '.$address["city"]." ".$address["zip_code"].", ".$address["street"]." ".$address["home_number"];
        echo '</div>';
        echo '<div id="orderStatus">';
        echo 'Stav objednávky: ';
        if($order["order_status"]==1){
            echo 'Odesláno';
        }else{
            echo 'Připravujeme zboží';
        }
        echo '</div>';
        echo '<div>';
        echo '<form method="post" action="index.php?pages=orderDetail">
                  <button id="detailButton" name="detailOrder" value='.$order["id_order"].' type="submit">Detail</button>
              </form>
             </div></div>';
    }

    public static function printOrderDetail($idOrder){

        echo '<h1>Objednávka č. '.$idOrder.'</h1>';
        echo '<div class=list>';
        $goodsInOrder = GoodsDB::selectGoodsInOrder($idOrder);
        foreach ($goodsInOrder as $goods){
            self::printGoodsInOrder($goods);
        }
        echo '</div>';
    }

    private static function printGoodsInOrder($goods)
    {
        $attributes = GoodsDB::selectAttributeById($goods["attribute_id_attribute"]);
            echo '<div class="listRow">';
            echo '<div class="detailsInRow detailsInRowOrders">';
            echo 'Název zboží: '.$goods["name"].'<br>
                  Cena: '.$goods[11].' Kč<br><!--//na 11 miste v $goods je odpovidajici cnea-->
                  Kusy: '.$goods["quantity"]."<br>
                  Barva: ".Colors::getBarva($attributes["color"])."<br>
                  Velikost: ".$attributes["size"];
            echo '<br>';
            echo '</div>';
            echo '<img class="imgOrderDetail" src='.$attributes["image"].'>';
            echo '</div>';
    }

    public static function printOrdersAsAdmin()
    {
        $orders = OrderDB::selectAllOrders();
        foreach ($orders as $order){
            $orderedGoods = OrderDB::selectOrderedGoodsByOrderId($order["id_order"]);
            $totalPrice = 0;
            foreach($orderedGoods as $goods){
                $totalPrice+=$goods["price"]*$goods["quantity"];//zjisteni ceny zbozi
            }
            echo '<div class="listRow listRowOrders">';
            echo '<div class=detailsInRow>';
            $user = UserDB::selectUserById($order["user_id_user"]);
            $year = substr($order["date_of_order"],0,4);
            $month = substr($order["date_of_order"],5,2);
            $day = substr($order["date_of_order"],8,2);
            $time = substr($order["date_of_order"],11,5);
            echo "Id objednávky: ".$order["id_order"];//dat do section
            echo '<br>';
            echo "Datum objednání: ".$day.". ".$month.". ".$year." ".$time;
            echo '<br>';
            echo 'Email: '.$user["email"];
            echo '<br>';
            echo 'Cena objednávky: '.$totalPrice.' Kč';
            echo '</div>';
            echo '<div class="dFlex">';
            echo '<div class="dFlexpRelLeft110">';
            echo '<p>Odesláno? </p>';
            echo '<input '; if($order['order_status']==1){echo 'checked'; }echo ' id="'.$order["id_order"].'" name="checkBox_list[]" type="checkbox" value="'.$order["id_order"].'"></form>';
            echo '</div>';
            echo '<div class="pRelTop100">';
            echo '<a href="index.php?pages=orderDetail&detailOrder='.$order["id_order"].'" type="submit"><img  class="w50h50 detailOrderButton"  src="./imgs/icons/detail.png"></a>';
            echo '</div>';
            echo '</div>';
            echo '</div>';
        }
    }
    public static function printOrderConfirm(){//vypis pokud je zbozi objednano
        echo "<script>
        alert('Zboží objednáno');
        window.location.href='index.php?pages=myOrders';
        </script>";
    }
}