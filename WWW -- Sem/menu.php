
<label for="show-menu" class="show-menu">Menu</label>
<input type="checkbox" id="show-menu" role="button">

<ul id="menu">
    <!--<li><a href="index.php?page=homePage"><div id="logo"><img  height="50px" width="50px" src="imgs/logo.png"></div></a></li>-->
    <li><a href="index.php?pages=homePage">Domů</a></li>
    <li><a href="index.php?pages=categories&gender=m">Muži</a></li>
    <li><a href="index.php?pages=categories&gender=f">Ženy</a></li>
    <li><a href="index.php?pages=categories&gender=k">Děti</a></li>
    <li><a href="index.php?pages=sales">Akce</a></li>
    <?php
    if(!empty($_SESSION["loggedIn"])&&$_SESSION["loggedIn"]==true){

        if($_SESSION["role"]=="Admin"){
            echo '
                          <li style="float:right" ><a href="index.php?pages=logOut">Odhlásit se</a></li>  
                          <li style="float:right"> <a href="index.php?pages=account">Můj účet</a></li><li style="float:right"> <a href="index.php?pages=usersManagement">Správa uživatelů</a></li>
                          <li style="float:right" ><a href="index.php?pages=goodsManagement">Správa zboží</a></li>
                          <li style="float:right" ><a href="index.php?pages=saleManagement">Správa akcí</a></li>
                          <li style="float:right" ><a href="index.php?pages=ordersManagement">Správa objednávek</a></li>';
        }else if($_SESSION["role"]=="Zaměstnanec"){
            echo '<li style="float:right" ><a href="index.php?pages=logOut">Odhlásit se</a></li>  
                            <li style="float:right"> <a href="index.php?pages=account">Můj účet</a></li>
                            <li style="float:right" ><a href="index.php?pages=goodsManagement">Správa zboží</a></li>
                            <li style="float:right" ><a href="index.php?pages=saleManagement">Správa akcí</a></li>
                            <li style="float:right" ><a href="index.php?pages=ordersManagement">Správa objednávek</a></li>';
        }else{
            if(!empty($_SESSION["cart"])){
                echo '    <li style="float:right;border: black solid 1px"><a class="active" href="index.php?pages=cart">Košík -- '.count($_SESSION["cart"]).'</a></li>';
            }else{
                echo '    <li style="float:right;border: black solid 1px"><a class="active" href="index.php?pages=cart">Košík</a></li>';
            }

            echo '<li style="float:right" ><a href="index.php?pages=logOut">Odhlásit se</a></li>  
                          <li style="float:right"> <a href="index.php?pages=account">Můj účet</a></li>
                          <li style="float:right" ><a href="index.php?pages=myOrders">Moje objednávky</a></li>';
        }
    }else{
        if(!empty($_SESSION["cart"])){
            echo '    <li style="float:right;border: black solid 1px"><a class="active" href="index.php?pages=cart">Košík -- '.count($_SESSION["cart"]).'</a></li>';
        }else{
            echo '    <li style="float:right;border: black solid 1px"><a class="active" href="index.php?pages=cart">Košík</a></li>';
        }
        echo '<li style="float:right"> <a href="index.php?pages=logIn">Přihlášení</a></li>
                  <li style="float:right" ><a href="index.php?pages=signUp">Registrace</a></li>';
    }
    ?>
</ul>
<script>
    document.getElementById("changeGreen").onclick = function(){
        document.getElementById("test").style.color = 'green';
        <?php
        $_POST['menu'] = "homePage";
        ?>
    }
</script>


