<?php
echo '<div class="allGoodsHomePage">';
    $categories = array("shirts","protectors","shoes","balls");
    for ($i = 0; $i < 4; $i++) {
            echo '<div class="homePageGoods">
                <img class="imgHomePage" src="./imgs/imgs_homePage/' .$categories[$i].'.jpg" alt="">
            </div>';
    }
echo '</div>';
?>

