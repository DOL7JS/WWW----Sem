<?php


class Colors
{

    public static function getColors(){
        return array(array("black","Černá"),array("white","Bílá"),array("blue","Modrá"),
            array("green","Zelená"),array("yellow","Žlutá"),array("orange","Oranžová"),array("red","Červená"));
    }
    static function getBarva($color){//preklad anglicke barvy na ceskou
        switch ($color) {
            case "black":
                return "Černá";
            case "white":
                return "Bílá";
            case "blue":
                return "Modrá";
            case "green":
                return "Zelená";
            case "yellow":
                return "Žlutá";
            case "orange":
                return "Oranžová";
            case "red":
                return "Červená";
        }
    }
    static function getColor($color){//preklad anglicke barvy na ceskou
        switch ($color) {
            case "Černá":
                return "black";
            case "Bílá":
                return "white";
            case "Modrá":
                return "blue";
            case "Zelená":
                return "green";
            case "Žlutá":
                return "yellow";
            case "Oranžová":
                return "orange";
            case "Červená":
                return "red";
        }
    }
}