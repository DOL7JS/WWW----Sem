<?php
if(!empty($_GET["gender"])){
    switch ($_GET["gender"]){
        case "m": Section::printSection("m");break;
        case "f": Section::printSection("f");break;
        case "k": Section::printSection("k");break;
    }
}


