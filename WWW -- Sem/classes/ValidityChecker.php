<?php


class ValidityChecker
{
    //metody kontroluji zda jsou vyplnene potrebne fieldy
    //pokud alespon jeden je nevyplnen, na konci vyskoci okno o teto informaci
    public static function checkValidityGoods(){
        $errorMsg = '';
        $sizes = array("XS", "S", "M", "L", "XL","XXL","3XL");
        if(!in_array($_POST["size"],$sizes)&&!is_numeric($_POST["size"])){
            $errorMsg .= 'Zadal jste neplatnou velikost\n';
        }
        if(empty($_POST["name"])){
            $errorMsg .= 'Vyplnte název\n';
        }
        if(empty($_POST["price"])){
            $errorMsg .= 'Vyplnte cenu\n';
        }
        if($_POST["price"]<0){
            $errorMsg .= 'Zadal jste neplatne hodnoty pro cenu\n';
        }
        if(empty($_POST["size"])){
            $errorMsg .= 'Vyplnte velikost\n';
        }
        if($_POST["size"]<0){
            $errorMsg .= 'Zadal jste neplatne hodnoty pro velikost\n';
        }
        if(empty( $fileName = $_FILES['Filename']['name'])){
            $errorMsg .= 'Vyberte soubor\n';
        }
        if(!empty($errorMsg)){
            UserControl::printInformation($errorMsg);
            return false;
        }
        return true;
    }
    public static function checkAddAttribute(){
        $errorMsg = '';
        if(empty($_POST["attributeSizeAdd"])){
            $errorMsg .= 'Vyplnte velikost\n';
        }
        if(empty($_FILES["Filename"]["name"])){
            $errorMsg .= 'Vyberte soubor\n';
        }
        if(!empty($errorMsg)){
            UserControl::printInformation($errorMsg);
            return false;
        }
        return true;
    }
    public static function checkValiditySale(){
        $errorMsg = '';
        if(empty($_POST["sale"])){
            $errorMsg .= 'Vyplnte velikost slevy\n';
        }
        if($_POST["sale"]<0||$_POST["sale"]>100){
            $errorMsg .= 'Zadal jste neplatne hodnoty pro slevu\n';
        }
        if(!empty($errorMsg)){
            UserControl::printInformation($errorMsg);
            return false;
        }
        return true;
    }
    public static function checkValidityPayment(){
        $errorMsg = '';
        if(empty($_POST["deliveryMethod"])){
            $errorMsg .= 'Vyplnte způsob doručení\n';
        }
        if(empty($_POST["paymentMethod"])){
            $errorMsg .= 'Vyplnte způsob platby\n';
        }
        if(empty($_POST["first_name"])){
            $errorMsg .= 'Vyplnte jméno\n';
        }
        if(empty($_POST["last_name"])){
            $errorMsg .= 'Vyplnte příjmení\n';
        }
        if(empty($_POST["phone_number"])){
            $errorMsg .= 'Vyplnte telefonní číslo\n';
        }
        if(strlen(str_replace(" ","",$_POST["phone_number"]))>9){
            $errorMsg .= 'Moc znaku v telefonním cisle\n';
        }
        if(empty($_POST["city"])){
            $errorMsg .= 'Vyplnte město\n';
        }
        if(empty($_POST["street"])){
            $errorMsg .= 'Vyplnte ulici\n';
        }
        if(empty($_POST["home_number"])){
            $errorMsg .= 'Vyplnte číslo domu\n';
        }
        if(empty($_POST["zip_code"])){
            $errorMsg .= 'Vyplnte PSČ\n';
        }else if(!is_numeric(str_replace(" ", "", $_POST["zip_code"]))){
            $errorMsg .= 'PSČ není číslo\n';
        }
        if(strlen(str_replace(" ","",$_POST["zip_code"]))>5){
            $errorMsg .= 'Moc znaku v PSČ\n';
        }
        if(!empty($errorMsg)){
            UserControl::printInformation($errorMsg);
            return false;
        }
        return true;
    }
    public static function checkValidityUsers(){
        $errorMsg = '';
        if(empty($_POST["email"])){
            $errorMsg .= 'Vyplnte email\n';
        }
        if(empty($_POST["password"])){
            $errorMsg .= 'Vyplnte heslo\n';
        }
        if(!empty($errorMsg)){
            UserControl::printInformation($errorMsg);
            return false;
        }
        return true;
    }
    public static function  checkValidityAddAddresses(){
        $errorMsg = '';
        if(empty($_POST["first_name"])){
            $errorMsg .= 'Vyplnte jméno\n';
        }
        if(empty($_POST["last_name"])){
            $errorMsg .= 'Vyplnte příjmení\n';
        }
        if(empty($_POST["phone_number"])){
            $errorMsg .= 'Vyplnte telefonní číslo\n';
        }
        if(empty($_POST["city"])){
            $errorMsg .= 'Vyplnte město\n';
        }
        if(empty($_POST["street"])){
            $errorMsg .= 'Vyplnte ulici\n';
        }
        if(empty($_POST["home_number"])){
            $errorMsg .= 'Vyplnte číslo domu\n';
        }
        if(empty($_POST["zip_code"])){
            $errorMsg .= 'Vyplnte PSČ\n';
        }else if(!is_numeric(str_replace(" ", "", $_POST["zip_code"]))){
            $errorMsg .= 'PSČ není číslo\n';
        }
        if(strlen(str_replace(" ","",$_POST["phone_number"]))>9){
            $errorMsg .= 'Moc znaku v telefonním cisle\n';
        }
        if(strlen(str_replace(" ","",$_POST["zip_code"]))>5){
            $errorMsg .= 'Moc znaku v PSČ\n';
        }

        if(!empty($errorMsg)){
            UserControl::printInformation($errorMsg);
            return false;
        }
        return true;
    }
    public static function checkValidityPaymentGateway(){
        $errorMsg = '';
        if(empty($_POST["numberOfCard"])){
            $errorMsg .= 'Vyplnte číslo karty\n';
        }
        if(empty($_POST["validityMonth"])){
            $errorMsg .= 'Vyplnte platnost karty(měsíc)\n';
        }
        if(empty($_POST["validityYear"])){
            $errorMsg .= 'Vyplnte platnost karty(rok)\n';
        }
        if(empty($_POST["CVC"])){
            $errorMsg .= 'Vyplnte CVC\n';
        }
        if(strlen(str_replace(" ","",$_POST["numberOfCard"]))!=16){
            $errorMsg .= 'Spatny pocet znaku v cisle karty\n';
        }
        if(strlen(str_replace(" ","",$_POST["validityMonth"]))!=2){
            $errorMsg .= 'Spatny pocet znaku v mesici\n';
        }
        if(strlen(str_replace(" ","",$_POST["validityYear"]))!=2){
            $errorMsg .= 'Spatny pocet znaku v roce\n';
        }
        if(strlen(str_replace(" ","",$_POST["CVC"]))!=3){
            $errorMsg .= 'Spatny pocet znaku v CVC\n';
        }
        if(!empty($errorMsg)){
            UserControl::printInformation($errorMsg);
            return false;
        }
        return true;
    }

    public static function checkUpdateGoods()
    {
        $errorMsg = '';
        if(empty($_POST["name"])){
            $errorMsg .= 'Vyplnte název zboží\n';
        }
        if(empty($_POST["price"])){
            $errorMsg .= 'Vyplnte cenu\n';
        }
        if(!empty($errorMsg)){
            UserControl::printInformation($errorMsg);
            return false;
        }
        return true;
    }

    public static function checkAddCategory()
    {
        $errorMsg = '';
        if(empty($_POST["categoryNameCzech"])){
            $errorMsg .= 'Vyplnte český název kategorie\n';
        }
        if(empty($_POST["categoryNameEnglish"])){
            $errorMsg .= 'Vyplnte anglický název kategorie\n';
        }
        if(empty($_FILES['Filename']['name'])){
            $errorMsg .= 'Vyberte soubor\n';
        }
        if(!empty($errorMsg)){
            UserControl::printInformation($errorMsg);
            return false;
        }
        return true;
    }

}