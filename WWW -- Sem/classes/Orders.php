<?php

class Orders{

    static function printAllOrders(){//vypis vsech objednavek/ vypisuje admin nebo zamestnanec
        $conn = connection::getConnection();
        $result = $conn->query("SELECT * FROM db_dev.orders ORDER BY date_of_order DESC");
        echo '<form action="index.php?pages=ordersManagement" method="post">';
        echo '<input type="submit" name="updateOrder" value="Uložit" id="saveOrdersButton">';

        echo '<div class=list>';

        while($row = $result->fetch_assoc()){//prochazeni vsech objednavek
            $result2 = $conn->query("SELECT * FROM db_dev.ordered_goods WHERE order_id_order = '{$row['id_order']}'");
            $totalPrice = 0;
            while($rowPrice = $result2->fetch_assoc()){
                $totalPrice+=$rowPrice["price"]*$rowPrice["quantity"];//zjisteni ceny zbozi
            }
            $result3 = $conn->query("SELECT * FROM db_dev.deleted_goods WHERE order_id_order = '{$row['id_order']}'");
            while($rowPrice = $result3->fetch_assoc()){//zjisteni ceny zbozi pokud je zbozi v tabulce deleted_goods
                $totalPrice+=$rowPrice["price"]*$rowPrice["quantity"];
            }
            echo '<div class=listRow id="listRowOrders">';
            echo '<div class=detailsInRow>';
            $conn = connection::getConnection();
            $idCustomer = $row["user_id_user"];
            $idOrder = $row["id_order"];
            $resultCustomer = $conn->query("SELECT * FROM db_dev.user WHERE id_user = '$idCustomer'");//zjisteni emailu zakaznika
            $customer = $resultCustomer->fetch_assoc();
            $year = substr($row["date_of_order"],0,4);
            $month = substr($row["date_of_order"],5,2);
            $day = substr($row["date_of_order"],8,2);
            $time = substr($row["date_of_order"],11,5);

            echo "Id objednávky: ".$idOrder;
            echo '<br>';
            echo "Datum objednání: ".$day.". ".$month.". ".$year." ".$time;
            echo '<br>';
            echo 'Email: '.$customer["email"];
            echo '<br>';
            echo 'Cena objednávky: '.$totalPrice.' Kč';
            echo '</div>';
            echo '<div class="dFlex">';
            echo '<div id="dFlexpRelLeft110">';
            echo '<p>Odesláno? </p>';
            echo '<input '; if($row['order_status']==1){echo 'checked'; }echo ' id="'.$idOrder.'" name="checkBox_list[]" type="checkbox" value="'.$idOrder.'"></form>';
            echo '</div>';
            echo '<div class="pRelTop100">';
            echo '<a href="index.php?pages=orderDetail&detailOrder='.$row["id_order"].'" type="submit"><img id="detailOrderButton" class="w50h50"  src="./imgs/icons/detail.png"></a>';
            echo '</div>';
            echo '</div>';
            echo '</div>';
        }
        echo '</form>';
        echo '</div>';
    }
    static function exportToJson(){//exportovani vybranych tabulek do JSON souboru, tabulky goods, attribute, goods_category, deleted_goods, ordered_goods
        $conn = connection::getConnection();
        $result = $conn->query("SELECT * FROM db_dev.goods");
        $fp = fopen('goods.json', 'w');
        while($row = $result->fetch_assoc()){
            fwrite($fp, json_encode($row)."\n");
        }
        fclose($fp);
        $result = $conn->query("SELECT * FROM db_dev.attribute");
        $fp = fopen('attribute.json', 'w');
        while($row = $result->fetch_assoc()){
            fwrite($fp, json_encode($row)."\n");
        }
        fclose($fp);
        $result = $conn->query("SELECT * FROM db_dev.goods_category");
        $fp = fopen('goods_category.json', 'w');
        while($row = $result->fetch_assoc()){
            fwrite($fp, json_encode($row)."\n");
        }
        fclose($fp);
        $result = $conn->query("SELECT * FROM db_dev.deleted_goods");
        $fp = fopen('deleted_goods.json', 'w');
        while($row = $result->fetch_assoc()){
            fwrite($fp, json_encode($row)."\n");
        }
        fclose($fp);
        $result = $conn->query("SELECT * FROM db_dev.ordered_goods");
        $fp = fopen('ordered_goods.json', 'w');
        while($row = $result->fetch_assoc()){
            fwrite($fp, json_encode($row)."\n");
        }
        fclose($fp);
    }
    static function importJson(){//nacteni JSON souboru zpet do tabulek
        $conn = connection::getConnection();
        $handle = fopen("goods.json", "r");
        if ($handle) {
            while (($line = fgets($handle)) !== false) {
                $goods = json_decode($line,true);
                $result = $conn->query("SELECT * FROM db_dev.goods WHERE id_goods = {$goods["id_goods"]}");
                if($result->num_rows==0){//pokud zde zaznam neexistuje, tak se pokracuje ve vkladani
                    $result = $conn->query("SELECT * FROM db_dev.deleted_goods WHERE id_goods = {$goods["id_goods"]}");
                    if($result->num_rows!=0){//pokud je zaznam v tabulce deleted_goods, vymaze se a dale se vlozi z JSONu do tabulky goods
                        $conn->query("DELETE FROM db_dev.deleted_goods WHERE id_goods = {$goods["id_goods"]}");
                    }
                    $conn->query("INSERT INTO db_dev.goods (id_goods, name, price, image, available) VALUES 
                                            ('{$goods["id_goods"]}','{$goods["name"]}','{$goods["price"]}',
                                            '{$goods["image"]}','{$goods["available"]}')");
                    $old_dest = explode("/",$goods["image"]);
                    $old_dest[1] = "deleted_goods";
                    $old_dest = implode("/",$old_dest);
                    rename($old_dest,$goods['image']);//presune se soubor s obrazkem ze slozky deleted_goods do imgs_goods
                }
            }
            fclose($handle);
        }
        $handle = fopen("attribute.json", "r");
        if ($handle) {
            while (($line = fgets($handle)) !== false) {
                $attribute = json_decode($line,true);
                $result = $conn->query("SELECT * FROM db_dev.attribute WHERE id_attribute = {$attribute["id_attribute"]}");
                if($result->num_rows==0){
                    $conn->query("INSERT INTO db_dev.attribute (id_attribute, gender, color, size, sale, goods_id_goods) VALUES 
                                        ('{$attribute["id_attribute"]}','{$attribute["gender"]}','{$attribute["color"]}'
                                          ,'{$attribute["size"]}','{$attribute["sale"]}','{$attribute["goods_id_goods"]}')");
                }
            }
            fclose($handle);
        }
        $handle = fopen("goods_category.json", "r");
        if ($handle) {
            while (($line = fgets($handle)) !== false) {
                $goods_category = json_decode($line,true);
                $result = $conn->query("SELECT * FROM db_dev.goods_category WHERE goods_id_goods = {$goods_category["goods_id_goods"]} 
                                      AND category_id_category = {$goods_category["category_id_category"]}");
                if($result->num_rows==0){
                    $conn->query("INSERT INTO db_dev.goods_category (goods_id_goods, category_id_category) VALUES
                        ('{$goods_category["goods_id_goods"]}','{$goods_category["category_id_category"]}')");
                }
            }
            fclose($handle);
        }
        $handle = fopen("deleted_goods.json", "r");
        if ($handle) {
            while (($line = fgets($handle)) !== false) {
                $deleted_goods = json_decode($line,true);
                $result = $conn->query("SELECT * FROM db_dev.deleted_goods WHERE id_deleted_goods = {$deleted_goods["id_deleted_goods"]}");
                if($result->num_rows==0){
                    $conn->query("INSERT INTO db_dev.deleted_goods(id_deleted_goods, id_goods, name, price, color, image, order_id_order, quantity, size) VALUES 
                                        ('{$deleted_goods["id_deleted_goods"]}','{$deleted_goods["id_goods"]}','{$deleted_goods["name"]}',
                                        '{$deleted_goods["price"]}','{$deleted_goods["color"]}','{$deleted_goods["image"]}',
                                        '{$deleted_goods["order_id_order"]}','{$deleted_goods["quantity"]}','{$deleted_goods["size"]}')");
                }
            }
            fclose($handle);
        }
        $handle = fopen("ordered_goods.json", "r");
        if ($handle) {
            while (($line = fgets($handle)) !== false) {
                $ordered_goods = json_decode($line,true);
                $result = $conn->query("SELECT * FROM db_dev.ordered_goods WHERE goods_id_goods = {$ordered_goods["goods_id_goods"]} AND order_id_order={$ordered_goods["order_id_order"]}");
                if($result->num_rows==0){
                    $conn->query("INSERT INTO  db_dev.ordered_goods (id_ordered_goods, quantity, order_id_order, goods_id_goods, color, size, price) VALUES 
                        ('{$ordered_goods["id_ordered_goods"]}','{$ordered_goods["quantity"]}','{$ordered_goods["order_id_order"]}','{$ordered_goods["goods_id_goods"]}',
                         '{$ordered_goods["color"]}','{$ordered_goods["size"]}','{$ordered_goods["price"]}')");
                }
            }
            fclose($handle);
        }
    }
    static function printUserOrders(){//vypis objednavek jednoho uzivatele
        echo '<h1>Moje objednávky</h1>';
        $conn = connection::getConnection();
        $idUser = $_SESSION['idUser'];
        $result = $conn->query("SELECT * FROM db_dev.orders WHERE user_id_user = '$idUser' ORDER BY id_order DESC");
        echo '<div class=list>';
        while($row = $result->fetch_assoc()){//prochazeni objednavek daneho uzivatele
            $result2 = $conn->query("SELECT * FROM db_dev.ordered_goods WHERE order_id_order = '{$row['id_order']}'");
            $totalPrice = 0;
            while($rowPrice = $result2->fetch_assoc()){
                $totalPrice+=$rowPrice["price"]*$rowPrice["quantity"];//spocitani ceny objednavky
            }
                echo '<div id="listRowMyOrders" class="listRow">';
                echo '<div class="detailsInRow">';
                $idOrder = $row["id_order"];
                $year = substr($row["date_of_order"],0,4);
                $month = substr($row["date_of_order"],5,2);
                $day = substr($row["date_of_order"],8,2);
                $time = substr($row["date_of_order"],11,5);
                echo "Id objednávky: ".$idOrder."<br> Datum objednání: ".$day.". ".$month.". ".$year." ".$time;
                echo '<br>';
                echo 'Cena objednávky: '.$totalPrice.' Kč';
                echo '<br>';
                $resultAdresa = $conn->query("SELECT * FROM db_dev.order_address JOIN db_dev.delivery_info di on di.id_delivery_info = 
                                                            order_address.delivery_info_id_delivery_info WHERE orders_id_order = '$idOrder'");
                $adresaRow = $resultAdresa->fetch_assoc();
                echo 'Adresa: '.$adresaRow["city"]." ".$adresaRow["zip_code"].", ".$adresaRow["street"]." ".$adresaRow["home_number"];
                echo '</div>';

                echo '<div id="orderStatus">';
                echo 'Stav objednávky: ';
                if($row["order_status"]==1){
                    echo 'Odesláno';
                }else{
                    echo 'Připravujeme zboží';
                }
                echo '</div>';

            echo '<div>';
            echo '<form method="post" action="index.php?pages=orderDetail">
                  <button id="detailButton" name="detailOrder" value='.$row["id_order"].' type="submit">Detail</button>
                  </form>';
            echo '</div>';
            echo '</div>';
        }
        echo '</div>';
    }
    static function printOrderDetail(){//vypis zbozi v jedne objednave

        $conn = connection::getConnection();
        $order_id = empty($_GET["detailOrder"])?$_POST["detailOrder"]:$_GET["detailOrder"];
        echo '<h1>Objednávka č. '.$order_id.'</h1>';
        $result = $conn->query("SELECT id_goods,g.price,quantity,name,image,size,color FROM db_dev.orders
                                            JOIN db_dev.ordered_goods og on orders.id_order = og.order_id_order
                                            JOIN db_dev.goods g on og.goods_id_goods = g.id_goods
                                            WHERE id_order={$order_id}");
        echo '<div class=list>';

        while($row = $result->fetch_assoc()){//prochazeni zbozi ve vybrane objednavce
            echo '<div class="listRow">';
            echo '<div id="detailsInRowOrders" class="detailsInRow">';
            $resultSale = $conn->query("SELECT DISTINCT sale FROM db_dev.goods 
                                                    JOIN db_dev.attribute a on goods.id_goods = a.goods_id_goods 
                                                    WHERE goods_id_goods='{$row["id_goods"]}'");
            $sale = $resultSale->fetch_assoc();
            echo 'Nazev zbozi: '.$row["name"].'<br> Cena: '.$row["price"]*(1-$sale["sale"]/100).' Kč<br>Kusy: '.$row["quantity"]."<br>Barva: ".Goods::getColor($row["color"])."<br>Velikost: ".$row["size"];
            echo '<br>';
            echo '</div>';
            echo '<img class="imgOrderDetail" src='.$row["image"].'>';

            echo '</div>';
        }
        $result = $conn->query("SELECT * FROM db_dev.deleted_goods WHERE order_id_order='{$order_id}'");
        while ($row2=$result->fetch_assoc()){
            echo '<div class=listRow>';
            echo '<div class=detailsInRow>';
            echo 'Nazev zbozi: '.$row2["name"].'<br>Cena: '.$row2["price"].' Kč<br>Kusy: '.$row2["quantity"]."<br>Barva: ".Goods::getColor($row2["color"])."<br>Velikost: ".$row2["size"];
            echo '<br>';
            echo '</div>';
            echo '<img  class="imgOrderDetail" src='.$row2["image"].'>';
            echo '</div>';
        }
        echo '</div>';
    }
    static function addOrder(){//pridani objednavky
        $conn = connection::getConnection();
        $idCustomer = $_SESSION["idUser"];
        $deliveryMethod = $_SESSION["delivery_info"]["deliveryMethod"];
        $paymentMethod = $_SESSION["delivery_info"]["paymentMethod"];
        $conn->query("INSERT INTO  db_dev.orders (order_status, user_id_user, delivery_method_id_order_meth, payment_method_id_payment_meth)
                            VALUES (0,'$idCustomer','$deliveryMethod','$paymentMethod')");

        foreach ($_SESSION["cart"] as $key => $value) {
             $idSize  = explode(",",$key);
             $result = $conn->query("SELECT DISTINCT id_goods,price,sale,color FROM db_dev.goods 
                                                JOIN db_dev.attribute a on goods.id_goods = a.goods_id_goods 
                                                WHERE id_goods = '$idSize[0]'");
             while($row=$result->fetch_assoc()){
                 $idGoods = $row["id_goods"];
                 $price = $row["price"]*(1-$row["sale"]/100);
                 $quantity = $value["quantity"];
                 $color = $row["color"];
                 $size = $idSize[1];
                 $conn->query("INSERT INTO db_dev.ordered_goods (price, quantity, order_id_order, goods_id_goods,size,color) 
                                VALUES ('$price','$quantity',(SELECT MAX(id_order) FROM db_dev.orders),'$idGoods','$size','$color')");
             }
        }
        $first_name = $_SESSION["delivery_info"]["first_name"];
        $last_name = $_SESSION["delivery_info"]["last_name"];
        $phone_number = $_SESSION["delivery_info"]["phone_number"];
        $city = $_SESSION["delivery_info"]["city"];
        $street = $_SESSION["delivery_info"]["street"];
        $home_number = $_SESSION["delivery_info"]["home_number"];
        $zip_code = $_SESSION["delivery_info"]["zip_code"];
        $save_delivery_info = 0;
        if(!empty($_SESSION["delivery_info"]["save_delivery_info"])){
                $save_delivery_info = 1;
        }
        $resultSaveAddress = $conn->query("SELECT * FROM db_dev.delivery_info WHERE first_name = '{$first_name}' AND last_name = '{$last_name}' AND phone_number='{$phone_number}'
                    AND city='{$city}' AND street='{$street}' AND home_number='{$home_number}' AND zip_code='{$zip_code}'");
        $rowSaveAddress = $resultSaveAddress->fetch_assoc();
        $resultOrder = $conn->query("SELECT MAX(order_id_order) as order_id_order FROM db_dev.ordered_goods");
        $rowOrder = $resultOrder->fetch_assoc();
        $orderId = $rowOrder["order_id_order"];
        if($resultSaveAddress->num_rows==0){//pokud adresa jeste neexistuje, vlozi se do tabulky
            $conn->query("INSERT INTO db_dev.delivery_info (first_name, last_name, phone_number, city, street, home_number, zip_code, user_id_user,saved_address) 
                                VALUES ('{$first_name}','{$last_name}','{$phone_number}','{$city}'
                                ,'{$street}','{$home_number}','{$zip_code}','$idCustomer','$save_delivery_info')");

            $result = $conn->query("SELECT MAX(id_delivery_info) as id_delivery_info FROM db_dev.delivery_info");
            $row = $result->fetch_assoc();
            $deliveryId = $row["id_delivery_info"];
            $conn->query("INSERT INTO db_dev.order_address (delivery_info_id_delivery_info, orders_id_order) VALUES ('{$deliveryId}','{$orderId}')");
        }else{//pokud adresa existuje, pripoji se objednavka k exitujici adrese
            $conn->query("INSERT INTO db_dev.order_address (delivery_info_id_delivery_info, orders_id_order) VALUES ('{$rowSaveAddress["id_delivery_info"]}','{$orderId}')");
        }
        unset($_SESSION["cart"]);
    }
static function updateOrder(){//aktualizace stavu objednavek
        $conn = connection::getConnection();
        $conn->query("UPDATE db_dev.orders SET order_status=0 WHERE order_status=1");//nastavi se vsechny na 0
        if(!empty($_POST["checkBox_list"])){
            $arr = implode(',',$_POST["checkBox_list"]);
            $conn->query("UPDATE db_dev.orders SET order_status=1  WHERE id_order IN ({$arr})");//pote se nastavi vybrane objednavky na 1(odeslano)
            Users::printInformation('Objednávky uloženy');
        }
}
static function printOrderSummary(){//vypis shrnuti objednavky
    if(!empty($_SESSION["cart"])){
        $conn = connection::getConnection();
        $totalPrice = 0;
        echo '<div class=list>';
        foreach ($_SESSION["cart"] as $key => $value) {//prochazeni kosiku
            $idSize = explode(",",$key);
            $result = $conn->query("SELECT name,price,image,sale,color FROM db_dev.goods JOIN db_dev.attribute a on goods.id_goods = a.goods_id_goods WHERE id_goods='$idSize[0]'");
            $row = $result->fetch_assoc();
            $price = ($row["price"]*(1-$row["sale"]/100)) * $value["quantity"];
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
            echo '</div>';
            $totalPrice+=$price;
        }
        $deliveryId = $_SESSION["delivery_info"]["deliveryMethod"];
        $result = $conn->query("SELECT price FROM db_dev.delivery_method WHERE id_order_meth = '{$deliveryId}'");
        $row = $result->fetch_assoc();
        $deliveryPrice = $row["price"];

        $paymentId = $_SESSION["delivery_info"]["paymentMethod"];
        $result = $conn->query("SELECT price FROM db_dev.payment_method WHERE id_payment_meth = '{$paymentId}'");
        $row = $result->fetch_assoc();
        $paymentPrice = $row["price"];
        $totalPrice+=$paymentPrice + $deliveryPrice;
        echo '<div id="completeCart" >';
        echo '<div id="totalPrice">';
        if($_SESSION["delivery_info"]["paymentMethod"]==1){
            echo 'Platba kartou: '.$paymentPrice." Kč";
            echo '<br>';
        }else{
            echo 'Dobírka: '.$paymentPrice." Kč";
            echo '<br>';
        }
        echo 'Doprava: '.$deliveryPrice." Kč";
        echo '<br>';
        echo 'Celkova cena objednavky: '.$totalPrice." Kč";
        echo '</div>';

        echo '<form class="completeOrder" action="" method="post">';
            echo '<input name="completeOrder" type="submit" value="Objednat">';
        echo '</form>';

        echo '</div>';
        echo '</div>';
    }

}
static function printOrderConfirm(){//vypis pokud je zbozi objednano
    echo "<script>
        alert('Zboží objednáno');
        window.location.href='index.php?pages=myOrders';
        </script>";
}


}
