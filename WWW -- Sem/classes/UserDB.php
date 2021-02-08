<?php


class UserDB
{
    public static function selectAllUsers(){
        $conn = connection::getConnection();
        $result = $conn->prepare("SELECT * FROM db_dev.user");
        $result->execute();
        return $result->fetchAll();
    }
    private static function selectIdAddressesOfUser($idUser){
        $conn = connection::getConnection();
        $result = $conn->prepare("SELECT DISTINCT * FROM db_dev.user_delivery_info 
        JOIN delivery_info di on di.id_delivery_info = user_delivery_info.delivery_info_id_delivery_info 
        WHERE user_id_user = :userId AND saved_address=1");
        $result->bindParam("userId",$idUser);
        $result->execute();
        return $result->fetchAll();

    }
    public static function selectIdAddress($first_name,$last_name,$phone_number,$city,$street,$home_number,$zip_code,$idUser){
        $conn = connection::getConnection();
        $result = $conn->prepare("SELECT * FROM db_dev.delivery_info 
    JOIN user_delivery_info udi on delivery_info.id_delivery_info = udi.delivery_info_id_delivery_info 
    JOIN user u on u.id_user = udi.user_id_user 
    WHERE first_name = :first_name AND last_name = :last_name AND
        phone_number = :phone_number AND city = :city AND street = :street AND
        home_number = :home_number AND zip_code = :zip_code AND user_id_user = :id_user");
        $result->bindParam(":first_name",$first_name);
        $result->bindParam(":last_name",$last_name);
        $result->bindParam(":phone_number",$phone_number);
        $result->bindParam(":city",$city);
        $result->bindParam(":street",$street);
        $result->bindParam(":home_number",$home_number);
        $result->bindParam(":zip_code",$zip_code);
        $result->bindParam(":id_user",$idUser);
        $result->execute();
        return $result->fetch();
    }
    public static function selectAddressesOfUser($idUser){
        $conn = connection::getConnection();
        $idsOfAddresses = self::selectIdAddressesOfUser($idUser);
        $ar = array();
        foreach($idsOfAddresses as $id){
            $result = $conn->prepare("SELECT * FROM db_dev.delivery_info WHERE id_delivery_info = :idDeliveryInfo AND saved_address = 1;");
            $result->bindParam(":idDeliveryInfo",$id["delivery_info_id_delivery_info"]);
            $result->execute();
            $ar[] = $result->fetch();
        }
        return $ar;
    }
    public static function insertAddressToDeliveryInfo($first_name,$last_name,$phone_number,$city,$street,$home_number,$zip_code,$saveAddress){
        $conn = connection::getConnection();
            $result = $conn->prepare("INSERT INTO db_dev.delivery_info (first_name, last_name, phone_number, city, street, home_number, zip_code,saved_address) 
                                    VALUES (:first_name,:last_name,:phone_number,:city,:street,:home_number,:zip_code,:saveAddres)");
            $result->bindParam(":first_name",$first_name);
            $result->bindParam(":last_name",$last_name);
            $result->bindParam(":phone_number",$phone_number);
            $result->bindParam(":city",$city);
            $result->bindParam(":street",$street);
            $result->bindParam(":home_number",$home_number);
            $result->bindParam(":zip_code",$zip_code);
            $result->bindParam(":saveAddres",$saveAddress);

            $result->execute();

    }
    public static function selectIdOfLastAddedDeliveryInfo(){
        $conn = connection::getConnection();
        $result = $conn->prepare("SELECT MAX(id_delivery_info) as id_delivery_info FROM db_dev.delivery_info");
        $result->execute();
        return $result->fetch()["id_delivery_info"];

    }
    public static function insertIdAddressToUserDeliveryInfo($deliveryInfoId,$idUser){
        $conn = connection::getConnection();
        $result = $conn->prepare("INSERT INTO db_dev.user_delivery_info (delivery_info_id_delivery_info, user_id_user) VALUES (:delivery_info_id,:user_id)");
        $result->bindParam(":delivery_info_id",$deliveryInfoId);
        $result->bindParam(":user_id",$idUser);
        $result->execute();
    }

    public static function checkUserUniqueAddress($first_name,$last_name,$phone_number,$city,$street,$home_number,$zip_code,$idUser)
    {
        $conn = connection::getConnection();
        $result = $conn->prepare("SELECT * FROM db_dev.delivery_info 
    JOIN user_delivery_info udi on delivery_info.id_delivery_info = udi.delivery_info_id_delivery_info 
    JOIN user u on u.id_user = udi.user_id_user 
    WHERE first_name = :first_name AND last_name = :last_name AND
        phone_number = :phone_number AND city = :city AND street = :street AND
          home_number = :home_number AND zip_code = :zip_code AND user_id_user = :idUser");
        $result->bindParam(":first_name",$first_name);
        $result->bindParam(":last_name",$last_name);
        $result->bindParam(":phone_number",$phone_number);
        $result->bindParam(":city",$city);
        $result->bindParam(":street",$street);
        $result->bindParam(":home_number",$home_number);
        $result->bindParam(":zip_code",$zip_code);
        $result->bindParam(":idUser",$idUser);
        $result->execute();
        return $result->rowCount()==0;
    }

    public static function updateAddressStatus($first_name, $last_name, $phone_number, $city, $street, $home_number, $zip_code, $idUser,$saveAddress)
    {
        $conn = connection::getConnection();
        $result = $conn->prepare("UPDATE db_dev.delivery_info 
    JOIN user_delivery_info udi on delivery_info.id_delivery_info = udi.delivery_info_id_delivery_info
    JOIN user SET saved_address=:saveAddress
    WHERE first_name= :first_name AND last_name= :last_name AND phone_number= :phone_number AND
          city= :city AND street= :street AND home_number= :home_number AND 
          zip_code= :zip_code AND user_id_user= :idUser");
        $result->bindParam(":first_name",$first_name);
        $result->bindParam(":last_name",$last_name);
        $result->bindParam(":phone_number",$phone_number);
        $result->bindParam(":city",$city);
        $result->bindParam(":street",$street);
        $result->bindParam(":home_number",$home_number);
        $result->bindParam(":zip_code",$zip_code);
        $result->bindParam(":idUser",$idUser);
        $result->bindParam(":saveAddress",$saveAddress);
        $result->execute();
    }

    public static function selectAddressById($addressOption)
    {
        $conn = connection::getConnection();
        $result = $conn->prepare("SELECT * FROM db_dev.delivery_info WHERE id_delivery_info = :id_delivery_info");
        $result->bindParam(":id_delivery_info",$addressOption);
        $result->execute();
        return $result->fetch();
    }

    public static function selectAddressOfOrder($id_order){
        $conn = connection::getConnection();
        $result = $conn->prepare("SELECT * FROM db_dev.delivery_info 
                        JOIN orders o on delivery_info.id_delivery_info = o.delivery_info_id_delivery_info
                        WHERE id_order = :id_order");
        $result->bindParam(":id_order",$id_order);
        $result->execute();
        return $result->fetch();
    }

    public static function checkEmail($email)
    {
        $conn = connection::getConnection();
        $result = $conn->prepare("SELECT * FROM db_dev.user WHERE email= :email");
        $result->bindParam(":email",$email);
        $result->execute();
        return $result->fetch();
    }

    public static function addUser($email, $password, $role)
    {
        $conn = connection::getConnection();
        $result = $conn->prepare("INSERT INTO db_dev.user (email, password, role) VALUES (:email,:password,:role)");
        $result->bindParam(":email",$email);
        $result->bindParam(":password",$password);
        $result->bindParam(":role",$role);
        $result->execute();
    }

    public static function deleteUser($idUser)
    {
        $conn = connection::getConnection();
        $result = $conn->prepare("DELETE FROM db_dev.user WHERE id_user= :idUser");
        $result->bindParam(":idUser",$idUser);
        $result->execute();
    }

    public static function selectUserById($idUser)
    {
        $conn = connection::getConnection();
        $result = $conn->prepare("SELECT * FROM db_dev.user WHERE id_user = :idUser");
        $result->bindParam(":idUser",$idUser);
        $result->execute();
        return $result->fetch();

    }
    public static function updateUserEmailPasswordRole($idUser,$email,$password,$role){
        $conn = connection::getConnection();
        $result = $conn->prepare("UPDATE db_dev.user SET email = :email ,password = :password,role = :role  WHERE id_user = :idUser");
        $result->bindParam(":email",$email);
        $result->bindParam(":password",$password);
        $result->bindParam(":role",$role);
        $result->bindParam(":idUser",$idUser);
        $result->execute();
    }
    public static function updateUserEmailRole($idUser,$email,$role){
        $conn = connection::getConnection();
        $result = $conn->prepare("UPDATE db_dev.user SET email = :email ,role = :role  WHERE id_user = :idUser");
        $result->bindParam(":email",$email);
        $result->bindParam(":role",$role);
        $result->bindParam(":idUser",$idUser);
        $result->execute();
    }

    public static function deleteUserDeliveryInfo($idUser)
    {
        $conn = connection::getConnection();
        $result = $conn->prepare("DELETE FROM db_dev.user_delivery_info WHERE user_id_user= :idUser");
        $result->bindParam(":idUser",$idUser);
        $result->execute();
    }

    public static function deleteDeliveryInfo($addreses)
    {
        $conn = connection::getConnection();
        foreach ($addreses as $id){
            $result = $conn->prepare("DELETE FROM db_dev.delivery_info WHERE id_delivery_info= :idDeliveryInfo");
            $result->bindParam(":idDeliveryInfo",$id);
            $result->execute();
        }
    }


}