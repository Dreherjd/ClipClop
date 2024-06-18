<?php

    require_once 'lib/common.php';

    session_start();

    //if user already logged in, just send them home
    if(isLoggedIn()){
        redirectAndExit('index.php');
    }

    //handle form posting
    $username = '';
    if($_POST){
        //start up a session and the db
        
        $pdo = getPDO();

        //send them through if PW is correct
        $username = $_POST['username'];
        $ok = tryLogin($pdo, $username, $_POST['password']);
        if($ok){
            login($username);
            redirectAndExit('index.php');
        }
    }
?>
<?php include("includes/header.php"); ?>
<div class="container">
    <h4>Login</h4>
    <form method="post">
        <p>
            Username:
            <input type="text" class="form-control" name="username" />
        </p>
        <p>
            Password:
            <input type="password" class="form-control" name="password" />
        </p>
        <input type="submit" class="btn btn-primary" name="submit" value="Login" />
    </form>
</div>
<?php include("includes/footer.php"); ?>