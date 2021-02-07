
<h1>Přihlášení</h1>
<?php
$conn = connection::getConnection();
if(!empty($_POST["email"])&&!empty($_POST["heslo"])) {
    UserControl::login();
}
?>
    <form action="index.php?pages=logIn" method="post">
        <div class="accountBox">
            <div><label class="labelAccount">Email: </label><input type="email" name="email" placeholder="Email . . ."></div>
            <div><label class="labelAccount">Heslo: </label><input type="password" name="heslo"  placeholder="Heslo . . ."></div>
            <div><label class="labelAccount"></label><input type="submit" value="Přihlásit" name=""></div>
        </div>
    </form>
