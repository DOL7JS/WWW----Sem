<?php


class Section
{

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
        switch ($gender){
            case "m": echo '<h1 class="textCenter">Muži</h1>';break;
            case "f": echo '<h1 class="textCenter">Ženy</h1>';break;
            case "k": echo '<h1 class="textCenter">Děti</h1>';break;
        }

        echo '<div class="allGoods">';
        $categories = GoodsDB::selectCategories();
        foreach($categories as $category){
            echo '<a href="index.php?pages=goods&goods='.$category["name"].'&gender='.$gender.'">';
            echo '
            <div class="goodsSection">
            <img class="imgSection" src=' . $category["image"] . '>';
            echo '<p class="nameSection">';
            echo $category["czech_name"];
            echo '</p>
            </div>
            </a>';
        }

        echo '</div>';
    }

}