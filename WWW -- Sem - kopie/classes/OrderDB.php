<?php


class OrderDB
{
    public static function selectDeliveryMethodById($id){
        $conn = connection::getConnection();
        $result = $conn->prepare("SELECT price FROM db_dev.delivery_method WHERE id_order_meth = :deliveryId ");
        $result->bindParam(":deliveryId",$id);
        $result->execute();
        return $result->fetch();
    }

    public static function selectPaymentMethodById($id)
    {
        $conn = connection::getConnection();
        $result = $conn->prepare("SELECT price FROM db_dev.payment_method WHERE id_payment_meth = :paymentId ");
        $result->bindParam(":paymentId",$id);
        $result->execute();
        return $result->fetch();
    }

    public static function addOrder($idCustomer, $paymentMethod, $deliveryMethod,$delivery_info)
    {
        $conn = connection::getConnection();
        $result = $conn->prepare("INSERT INTO  db_dev.orders (order_status, user_id_user, delivery_method_id_order_meth, payment_method_id_payment_meth,delivery_info_id_delivery_info)
                            VALUES (0,:idCustomer,:deliveryMethod,:paymentMethod,:delivery_info)");
        $result->bindParam("idCustomer",$idCustomer);
        $result->bindParam("deliveryMethod",$deliveryMethod);
        $result->bindParam("paymentMethod",$paymentMethod);
        $result->bindParam("delivery_info",$delivery_info);
        $result->execute();
    }

    public static function addGoodsToOrderedGoods($order_id_order, $goods_id_goods, $attribute_id_attribute, $price, $quantity)
    {
        $conn = connection::getConnection();
        $result = $conn->prepare("INSERT INTO db_dev.ordered_goods (quantity, order_id_order, goods_id_goods, price, attribute_id_attribute)
            VALUES (:quantity,:order_id_order,:goods_id_goods,:price,:attribute_id_attribute)");
        $result->bindParam(":quantity",$quantity);
        $result->bindParam(":order_id_order",$order_id_order);
        $result->bindParam(":goods_id_goods",$goods_id_goods);
        $result->bindParam(":price",$price);
        $result->bindParam(":attribute_id_attribute",$attribute_id_attribute);
        $result->execute();
    }
    public static function selectLastAddedOrder(){
        $conn = connection::getConnection();
        $result = $conn->prepare("SELECT * FROM db_dev.orders ORDER BY id_order DESC LIMIT 1");
        $result->execute();
        return $result->fetch();
    }

    public static function selectOrdersOfUser($id_user){
        $conn = connection::getConnection();
        $result = $conn->prepare("SELECT * FROM db_dev.orders WHERE user_id_user = :idUser ORDER BY id_order DESC");
        $result->bindParam(":idUser",$id_user);
        $result->execute();
        return $result->fetchAll();
    }
    public static function selectOrderedGoodsByOrderId($id_order){
        $conn = connection::getConnection();
        $result = $conn->prepare("SELECT * FROM db_dev.ordered_goods WHERE order_id_order = :id_order");
        $result->bindParam(":id_order",$id_order);
        $result->execute();
        return $result->fetchAll();
    }

    public static function deleteOrdersByUser($idUser)
    {
        $conn = connection::getConnection();
        $result = $conn->prepare("DELETE FROM db_dev.orders WHERE user_id_user= :idUser");
        $result->bindParam(":idUser",$idUser);
        $result->execute();
    }

    public static function selectAllOrders()
    {
        $conn = connection::getConnection();
        $result = $conn->prepare("SELECT * FROM db_dev.orders ORDER BY date_of_order DESC");
        $result->execute();
        return $result->fetchAll();
    }

    public static function updateOrderStatusToFalse()
    {
        $conn = connection::getConnection();
        $result = $conn->prepare("UPDATE db_dev.orders SET order_status=0 WHERE order_status=1");
        $result->execute();
    }

    public static function updateOrderStatusToTrue($arrayOfOrders)
    {
        $conn = connection::getConnection();
        foreach ($arrayOfOrders as $order){//TODO predelat do jednoho dotazu, nesel parametr IN
            $result = $conn->prepare("UPDATE db_dev.orders SET order_status=1  WHERE id_order =:idOrder");//pote se nastavi vybrane objednavky na 1(odeslano)
            $result->bindParam(":idOrder",$order);
            $result->execute();
        }
    }
}