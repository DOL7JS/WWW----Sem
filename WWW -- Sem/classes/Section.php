<?php


class Section
{
    static function translate($name)//preklad ceskych kategorii do anglickych
    {
        $list = explode("/", $name);
        $name = explode(".", end($list));
        switch ($name[0]) {
            case "Trenýrky":
                return "shorts";
            case "Trika":
                return "shirts";
            case "Tašky":
                return "bags";
            case "Míče":
                return "balls";
            case "Kopačky":
                return "shoes";
            case "Chrániče":
                return "protectors";
            case "Brankář":
                return "goalkeeper";
        }
    }
    static function getCategory($category){
        switch ($category) {
            case "shorts":
                return "Trenýrky";
            case "shirts":
                return "Trika";
            case "bags":
                return "Tašky";
            case "balls":
                return "Míče";
            case "shoes":
                return "Kopačky";
            case "protectors":
                return "Chrániče";
            case "goalkeeper":
                return "Brankář";
        }
    }

    static function printSection($gender)//vypis kategorii
    {
        $dir = "./imgs/imgs_section/";
        $files2 = scandir($dir, 1);
        for ($i = 0; $i < count($files2) - 2; $i++) {
            $filenames[$i] = $dir . $files2[$i];
        }
        switch ($gender){
            case "m": echo '<h1 class="textCenter">Muži</h1>';break;
            case "f": echo '<h1 class="textCenter">Ženy</h1>';break;
            case "k": echo '<h1 class="textCenter">Děti</h1>';break;
        }

        echo '<div class="allGoods">';
        for ($i = 0; $i < count($filenames); $i++) {
            $a = Section::translate($filenames[$i]);
            echo "<a href='index.php?pages=goods&goods=$a&gender=$gender'>";
            echo '
            <div id="goodsSection">
            <img id="imgSection" src=' . $filenames[$i] . '>';
            $list = explode("/", $filenames[$i]);
            $name = explode(".", end($list));
            echo '<p id="nameSection">';
            echo $name[0];
            echo '</p>
            </div>
            </a>';
        }
        echo '</div>';
    }

}