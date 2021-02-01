<h1>Registrace</h1>
<?php
if(!empty($_POST["email"])) {
   Users::signUp();
}
?>
    <form action="" method="post" >
        <div class="accountBox">
            <div><label class="w200">Email: </label><input type="email" name="email" placeholder="Email . . ."></div>
            <div><label class="w200">Heslo: </label><input type="password" name="password" placeholder="Heslo . . ."></div>
            <div><label class="w200">Potvrzení hesla: </label><input type="password" name="passwordConfirm" placeholder="Potvrzení hesla . . ."></div>
            <div><label class="w200"></label><input type="submit" value="Registrovat"></div>
        </div>
    </form>
