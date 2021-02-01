<?php
echo '<div id="allGoodsHomePage">';
    $categories = array("shirts","protectors","shoes","balls");
    for ($i = 0; $i < 4; $i++) {
        $href = "index.php?pages=goods&goods=".$categories[$i]."&gender=m";
        echo'<a href="'.$href.'">
            <div id="homePageGoods">
                <img id="imgHomePage" src="./imgs/imgs_homePage/' .$categories[$i].'.jpg" alt="">
            </div>
        </a>';
    }
echo '</div>';
?>

