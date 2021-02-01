<?php
echo '<h1>Můj účet</h1>';

echo '<div class="accountBox">';
    echo 'Email: '.$_SESSION["email"];
    echo '<br>';
    echo 'Role: '.$_SESSION["role"];
    if($_SESSION["role"]=="Zákazník"){//pokud je role zákazník, může se prokliknout na své dodací adresy
        echo '<form action="index.php?pages=addresses" method="post" >';
            echo '<div><input value="Adresy" type="submit" name="show_delivery_info"></div>';
        echo '</form>';
    }
echo '</div>';

?>
