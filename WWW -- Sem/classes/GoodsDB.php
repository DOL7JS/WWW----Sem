<?php


class GoodsDB
{
    public static function getSizesOfAllGoods($category, $gender){
        $conn = connection::getConnection();
        $result = $conn->prepare("SELECT DISTINCT size FROM db_dev.goods 
                                                            JOIN db_dev.attribute a on db_dev.goods.id_goods = a.goods_id_goods
                                                            WHERE gender = :gender AND category_id_category = :category_id");
        $result->bindParam(":gender",$gender);
        $result->bindParam(":category_id",$category);
        $result->execute();
        return $result->fetchAll();
    }
    public static function getSizesOfGoodsById($id){
        $conn = connection::getConnection();
        $result = $conn->prepare("SELECT DISTINCT size FROM db_dev.goods 
                                            JOIN db_dev.attribute a on db_dev.goods.id_goods = a.goods_id_goods 
                                            WHERE id_goods = :id_goods AND a.deleted=0");
        $result->bindParam(":id_goods",$id);
        $result->execute();
        return $result->fetchAll();
    }
    public static function getColorsOfGoodsById($id){
        $conn = connection::getConnection();
        $result = $conn->prepare("SELECT DISTINCT color FROM db_dev.goods 
                                            JOIN db_dev.attribute a on db_dev.goods.id_goods = a.goods_id_goods 
                                            WHERE id_goods = :id_goods");
        $result->bindParam(":id_goods",$id);
        $result->execute();
        return $result->fetchAll();
    }
    public static function selectAllSizesOfGoodsInSale(){
        $conn = connection::getConnection();
        $result = $conn->prepare("SELECT DISTINCT size FROM db_dev.attribute 
                                            WHERE sale!=0");
        $result->execute();
        $r = $result->fetchAll();
        $arr = array();
        foreach ($r as $item){
            $arr[] = $item["size"];
        }
        return $arr;

    }

    public static function getGoodsByCategoryAndGender($category,$gender,$color,$size,$price){
        $conn = connection::getConnection();
        $sql = 'SELECT DISTINCT id_goods,name,price,sale FROM db_dev.goods
                                                              JOIN db_dev.attribute a on db_dev.goods.id_goods = a.goods_id_goods
                                                              WHERE available = 1 AND gender = :gender AND
                                                            category_id_category= :category_id AND goods.deleted= 0';
        if(!empty($color)){//pridani filtru k SQL prikazu
            $sql = $sql." AND COLOR = '".$color."'";
        }
        if(!empty($size)){//pridani filtru k SQL prikazu
            $sql = $sql." AND SIZE = '".$size."'";
        }
        if(!empty($price)&&$price=="descending"){//pridani filtru k SQL prikazu
            $sql = $sql ." ORDER BY price DESC";
        }else{
            $sql = $sql ." ORDER BY price";
        }
        $result = $conn->prepare($sql);
        $result->bindParam(":gender",$gender);
        $result->bindParam(":category_id",$category);
        $result->execute();
        return $result->fetchAll();
    }

    public static function selectCategoryByName($nameOfCategory){
        $conn = connection::getConnection();
        $result = $conn->prepare("SELECT * FROM db_dev.CATEGORY WHERE name = :category_name");
        $result->bindParam(":category_name",$nameOfCategory);
        $result->execute();
        return $result->fetch();
    }
    public static function selectCategoryByNameInCzech($nameOfCategory){
        $conn = connection::getConnection();
        $result = $conn->prepare("SELECT * FROM db_dev.CATEGORY WHERE czech_name = :category_name");
        $result->bindParam(":category_name",$nameOfCategory);
        $result->execute();
        return $result->fetch();
    }

    public static function getGoodsWithAvailableAttributeById($id){
        $conn = connection::getConnection();
        $result = $conn->prepare("SELECT * FROM db_dev.goods 
                                                JOIN db_dev.attribute a on goods.id_goods = a.goods_id_goods WHERE id_goods= :idGoods AND a.deleted=0");//zjisteni informaci o zbozi na zaklade id
        $result->bindParam(":idGoods",$id);
        $result->execute();
        return $result->fetchAll();
    }
    public static function getGoodsWithAttributeById($id){
        $conn = connection::getConnection();
        $result = $conn->prepare("SELECT * FROM db_dev.goods 
                                                JOIN db_dev.attribute a on goods.id_goods = a.goods_id_goods WHERE id_goods= :idGoods");//zjisteni informaci o zbozi na zaklade id
        $result->bindParam(":idGoods",$id);
        $result->execute();
        return $result->fetchAll();
    }

    public static function selectIdAttribute($gender, $color, $size, $sale, $goods_id_goods)
    {
        $conn = connection::getConnection();
        $result = $conn->prepare("SELECT id_attribute FROM db_dev.attribute WHERE color = :color AND gender = :gender AND
        size = :size AND sale = :sale AND goods_id_goods = :goods_id_goods");
        $result->bindParam(":color",$color);
        $result->bindParam(":gender",$gender);
        $result->bindParam(":size",$size);
        $result->bindParam(":sale",$sale);
        $result->bindParam(":goods_id_goods",$goods_id_goods);
        $result->execute();
        $id = $result->fetch();
        return $id["id_attribute"];
    }
    public static function selectGenderOfGoodsById($id){
        $conn = connection::getConnection();
        $result = $conn->prepare("SELECT gender FROM db_dev.goods JOIN attribute a on goods.id_goods = a.goods_id_goods
            WHERE id_goods = :id_goods");
        $result->bindParam("id_goods",$id);
        $result->execute();
        return $result->fetch()["gender"];
    }
    public static function selectGoodsInOrder($id_order){
        $conn = connection::getConnection();
        $result = $conn->prepare("SELECT * FROM db_dev.orders
                                                                JOIN db_dev.ordered_goods og on orders.id_order = og.order_id_order
                                                                JOIN db_dev.goods g on og.goods_id_goods = g.id_goods
                                                                WHERE id_order= :id_order");
        $result->bindParam("id_order",$id_order);
        $result->execute();
        return $result->fetchAll();
    }

    public static function selectCategoriesCzechNames()
    {
        $conn = connection::getConnection();
        $result = $conn->prepare("SELECT * FROM db_dev.category");
        $result->execute();
        $r = $result->fetchAll();
        $arr = array();
        foreach ($r as $item){
            $arr[] = $item["czech_name"];
        }
        return $arr;
    }
    public static function selectCategories()
    {
        $conn = connection::getConnection();
        $result = $conn->prepare("SELECT * FROM db_dev.category WHERE deleted=0");
        $result->execute();
        return $result->fetchAll();
    }

    public static function selectGoodsInSale()
    {
        $conn = connection::getConnection();
        $result = $conn->prepare("SELECT DISTINCT id_goods,name,price,sale FROM db_dev.goods 
                                JOIN attribute a on goods.id_goods = a.goods_id_goods WHERE sale!=0 AND available=1");
        $result->execute();
        return $result->fetchAll();
    }

    public static function deleteOrderedGoodsByOrder($idOrder)
    {
        $conn = connection::getConnection();
        $result = $conn->prepare("DELETE FROM db_dev.ordered_goods WHERE order_id_order= :idUser");
        $result->bindParam(":idUser",$idOrder);
        $result->execute();

    }

    public static function selectAllGoods()
    {
        $conn = connection::getConnection();
        $result = $conn->prepare("SELECT * FROM db_dev.goods");
        $result->execute();
        return $result->fetchAll();

    }

    public static function selectAllGoodsWithAttributeOrderByPriceOrderByCategoryOrderByAvailable($category,$available,$price)
    {
        $conn = connection::getConnection();
        $sql = "SELECT DISTINCT id_goods,name,price,available FROM db_dev.goods
                                              JOIN attribute a on goods.id_goods = a.goods_id_goods";
        if($category!=null){
            $sql .= " WHERE category_id_category = :idCategory ";
        }
        if($available!=null&&$category!=null){
            $sql .= " AND available = :available ";
        }else if($available!=null){
            $sql .= " WHERE available = :available ";
        }
        if($available==null&&$category==null){
            $sql.=" WHERE goods.deleted = 0";
        }else {
            $sql.=" AND goods.deleted = 0";
        }
        $sql.=" ORDER BY price";
        if($price=="descending"){
            $sql .= " DESC";
        }
        $result = $conn->prepare($sql);
        if($category!=null) {
            $result->bindParam(":idCategory",$category);
        }
        if($available!=null){
            $available = $available=="available"?1:0;
            $result->bindParam(":available",$available);
        }
        $result->execute();
        return $result->fetchAll();
    }

    public static function deleteSale($goodsId)
    {
        $conn = connection::getConnection();
        $result =$conn->prepare("UPDATE db_dev.attribute SET sale=0 WHERE goods_id_goods= :goods_id;");
        $result->bindParam(":goods_id",$goodsId);
        $result->execute();
    }

    public static function selectGoodsByName($nameGoods)
    {
        $conn = connection::getConnection();
        $result = $conn->prepare("SELECT id_goods FROM db_dev.goods WHERE name = :nameGoods AND deleted = 0");
        $result->bindParam(":nameGoods",$nameGoods);
        $result->execute();
        return $result->fetch();
    }
    public static function selectCategoryByGoodsName($nameGoods)
    {
        $conn = connection::getConnection();
        $result = $conn->prepare("SELECT * FROM db_dev.goods 
                                JOIN category c on c.id_category = category_id_category 
                                WHERE goods.name = :nameGoods");
        $result->bindParam(":nameGoods",$nameGoods);
        $result->execute();
        return $result->fetch();
    }

    public static function updateAttributeSale($idGoods,$sale){
        $conn = connection::getConnection();
        $result = $conn->prepare("UPDATE db_dev.attribute SET sale= :sale WHERE goods_id_goods=:idGoods");
        $result->bindParam(":sale",$sale);
        $result->bindParam(":idGoods",$idGoods);
        $result->execute();
    }

    public static function insertGoods($name, $price,$category)
    {
        $conn = connection::getConnection();
        $result= $conn->prepare("INSERT INTO db_dev.goods(name, price, available, deleted,category_id_category) VALUES(:name,:price,1,0,:category)");
        $result->bindParam(":name",$name);
        $result->bindParam(":price",$price);
        $result->bindParam(":category",$category);
        $result->execute();
    }

    public static function insertAttribute($gender,$color,$size,$id_goods,$image)
    {
        $conn = connection::getConnection();
        $result= $conn->prepare("INSERT INTO db_dev.attribute (gender, color, size, goods_id_goods, image) VALUES(:gender,:color,:size,:id_goods,:image)");
        $result->bindParam(":gender",$gender);
        $result->bindParam(":color",$color);
        $result->bindParam(":size",$size);
        $result->bindParam(":id_goods",$id_goods);
        $result->bindParam(":image",$image);
        $result->execute();
    }

    public static function addAttribute($id_goods, $size, $color, $gender, $sale, $deleted, $image)
    {
        $conn = connection::getConnection();
        $result= $conn->prepare("INSERT INTO db_dev.attribute (gender, color, size, sale, goods_id_goods, deleted, image) 
                                            VALUES (:gender,:color,:size,:sale,:idGoods,:deleted,:image)");
        $result->bindParam(":gender",$gender);
        $result->bindParam(":color",$color);
        $result->bindParam(":size",$size);
        $result->bindParam(":sale",$sale);
        $result->bindParam(":idGoods",$id_goods);
        $result->bindParam(":deleted",$deleted);
        $result->bindParam(":image",$image);
        $result->execute();
    }

    public static function selectGoodsById($id_goods)
    {
        $conn = connection::getConnection();
        $result = $conn->prepare("SELECT * FROM db_dev.goods WHERE id_goods = :id_goods");
        $result->bindParam(":id_goods",$id_goods);
        $result->execute();
        return $result->fetch();
    }

    public static function setGoodsUnavailable($idOfGoods)
    {
        $conn = connection::getConnection();
        $result = $conn->prepare("UPDATE db_dev.GOODS SET available=0 WHERE id_goods = :idGoods");
        $result->bindParam(":idGoods",$idOfGoods);
        $result->execute();
    }

    public static function setGoodsAvailable($idOfGoods)
    {
        $conn = connection::getConnection();
        $result = $conn->prepare("UPDATE db_dev.GOODS SET available=1 WHERE id_goods = :idGoods");
        $result->bindParam(":idGoods",$idOfGoods);
        $result->execute();
    }

    public static function deleteGoods($idOfGoods)
    {
        $conn = connection::getConnection();
        $result = $conn->prepare("UPDATE db_dev.GOODS SET deleted=1 WHERE id_goods = :idGoods");
        $result->bindParam(":idGoods",$idOfGoods);
        $result->execute();
    }

    public static function addCategory($czechNameOfCategory, $englishNameOfCategory, $file,$deleted)
    {
        $conn = connection::getConnection();
        $result= $conn->prepare("INSERT INTO db_dev.category (name, image, czech_name,deleted) VALUES (:name,:file,:czech_name,:deleted)");
        $result->bindParam(":name",$englishNameOfCategory);
        $result->bindParam(":czech_name",$czechNameOfCategory);
        $result->bindParam(":file",$file);
        $result->bindParam(":deleted",$deleted);
        $result->execute();
    }

    public static function updateCategoryStatus($idCategory,$deleted)
    {
        $conn = connection::getConnection();
        $result = $conn->prepare("UPDATE db_dev.category SET deleted=:deleted WHERE id_category = :idCategory");
        $result->bindParam(":deleted",$deleted);
        $result->bindParam(":idCategory",$idCategory);
        $result->execute();
    }

    public static function checkUniqueNameCategory($czechNameOfCategory, $englishNameOfCategory)
    {
        $conn = connection::getConnection();
        $result = $conn->prepare("SELECT * FROM db_dev.category WHERE name = :name AND czech_name = :czech_name");
        $result->bindParam(":name",$englishNameOfCategory);
        $result->bindParam(":czech_name",$czechNameOfCategory);
        $result->execute();
        return $result->fetch();
    }

    public static function deleteAttribute($idGoods, $size, $color)
    {
        $conn = connection::getConnection();
        $result = $conn->prepare("UPDATE db_dev.attribute SET deleted=1 WHERE size=:size AND color =:color AND goods_id_goods = :idGoods");
        $result->bindParam(":size",$size);
        $result->bindParam(":color",$color);
        $result->bindParam(":idGoods",$idGoods);
        $result->execute();
    }

    public static function selectColorsByGoodsIdBySize($goodsID, $attributeSize)
    {
        $conn = connection::getConnection();
        $result = $conn->prepare("SELECT DISTINCT color FROM db_dev.attribute WHERE goods_id_goods = :idGoods AND size = :size AND deleted=0");
        $result->bindParam(":idGoods",$goodsID);
        $result->bindParam(":size",$attributeSize);
        $result->execute();
        return $result->fetchAll();
    }

    public static function updateGoods($idGoods, $name, $price)
    {
        $conn = connection::getConnection();
        $result = $conn->prepare("UPDATE db_dev.goods SET name=:name,price=:price WHERE id_goods=:id_goods");
        $result->bindParam(":name",$name);
        $result->bindParam(":price",$price);
        $result->bindParam(":id_goods",$idGoods);
        $result->execute();
    }

    public static function updateCategoryStatusAndImage($id_category, $deleted, $image)
    {
        $conn = connection::getConnection();
        $result = $conn->prepare("UPDATE db_dev.category SET deleted=:deleted,image=:image WHERE id_category = :idCategory");
        $result->bindParam(":deleted",$deleted);
        $result->bindParam(":idCategory",$id_category);
        $result->bindParam(":image",$image);
        $result->execute();
    }

    public static function checkUniqueCategory($czechNameOfCategory, $englishNameOfCategory,$deleted)
    {
        $conn = connection::getConnection();
        $result = $conn->prepare("SELECT * FROM db_dev.category WHERE name = :name AND czech_name = :czech_name AND deleted=:deleted");
        $result->bindParam(":name",$englishNameOfCategory);
        $result->bindParam(":czech_name",$czechNameOfCategory);
        $result->bindParam(":deleted",$deleted);
        $result->execute();
        return $result->fetch();
    }

    public static function selectAttributeByIdGoodsBySizeByColor($id_goods, $size, $color)
    {
        $conn = connection::getConnection();
        $result = $conn->prepare("SELECT * FROM db_dev.attribute WHERE goods_id_goods = :id_goods AND size = :size AND color=:color");
        $result->bindParam(":id_goods",$id_goods);
        $result->bindParam(":size",$size);
        $result->bindParam(":color",$color);
        $result->execute();
        return $result->fetch();
    }

    public static function selectAttributeById($idAttribute)
    {
        $conn = connection::getConnection();
        $result = $conn->prepare("SELECT * FROM db_dev.attribute WHERE id_attribute = :idAttribute");
        $result->bindParam(":idAttribute",$idAttribute);
        $result->execute();
        return $result->fetch();
    }


}