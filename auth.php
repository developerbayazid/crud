<?php
require_once "inc/functions.php"; 
session_start([
    'cookie_lifetime' => 300 //5 Minutes
    ]);
$signupFormShow = false;
if($_GET['signup'] == true){
    $signupFormShow = true;
}
$signupSuccess = '';
$error = false;   
if(isset($_POST['username']) && isset($_POST['password'])){
    $username = filter_input(INPUT_POST, 'username', FILTER_SANITIZE_STRING);
    $password = filter_input(INPUT_POST, 'password', FILTER_SANITIZE_STRING);
    if($username && $password){
        $jsonData = file_get_contents(DB_USER);
        $users = json_decode($jsonData, true);
        foreach($users as $_user){
            if($_user['r_username'] == $username && $_user['r_password'] == $password){
                $_SESSION['user_id'] = $_user['r_id'];
                $_SESSION['logedin'] = true;
                header('location: index.php');
            }
        }
    }else{
        $error = true;
        $_SESSION['logedin'] = false;
        header('location: auth.php?login=error');
    }
}


if($_GET['logout'] == 'success'){
    $_SESSION['logedin'] = false;
    session_destroy();
    header('location: auth.php');
}
if($_GET['signup'] == 'success'){
    $r_id = filter_input(INPUT_POST, 'r_id', FILTER_SANITIZE_STRING);
    $r_username = filter_input(INPUT_POST, 'r_username', FILTER_SANITIZE_STRING);
    $user = filter_input(INPUT_POST, 'user', FILTER_SANITIZE_STRING);
    $r_password = filter_input(INPUT_POST, 'r_password', FILTER_SANITIZE_STRING);

    if(isset($r_id) && isset($r_username) && isset($user) && isset($r_password) && $r_id != '' && $r_username != '' && $user != '' && $r_password != ''){
        $userAdd = addUser($r_username, $user, $r_password);
        if($userAdd){
            $signupSuccess = 1;
        }else{
            $signupSuccess = 2;
        }
    }else{
        $signupSuccess = 3;
    }
    
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | Signup</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Roboto:300,300italic,700,700italic">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/normalize/8.0.1/normalize.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/milligram/1.4.1/milligram.css">
</head>
<style>
    body{
        margin-top: 100px;
    }
</style>
<body>
    <div class="container">
        <div class="row">
            <div class="column column-50 column-offset-20">
            
            <?php if($_SESSION['logedin'] == false): ?>
                <h2>Simple Auth Example</h2>
                <p>Hello Stranger, Login Below</p>
                <?php 
                if(isset($_SESSION['private_page'])){
                    echo $_SESSION['private_page'];
                    unset($_SESSION['private_page']);
                }
                ?>
                <?php if($_GET['login'] == 'error'): ?>
                <blockquote>Your Username and Password doesnt match</blockquote>
                <?php endif; ?>
                <?php if(!$signupFormShow): ?>
                <form action="auth.php" method="POST">
                    <label for="username">Username</label>
                    <input type="text" name="username" id="username">
                    <label for="password">Password</label>
                    <input type="password" name="password" id="password">
                    <button type="submit" class="button-primary" name="submit">Login</button><br>
                    <small>Click here to <a href="auth.php?signup=true">signup</a></small>
                </form>
                <?php endif; ?>
            <?php endif; ?>
            <?php if($signupSuccess == 1): ?>
            <blockquote>Signup is Complete</blockquote>
            <?php elseif($signupSuccess == 2): ?>
            <blockquote>Username already used</blockquote>
            <?php elseif($signupSuccess == 3): ?>
            <blockquote>You must need to fill-up all the fields</blockquote>
            <?php endif; ?>
            <?php if($signupFormShow): ?>
            <form action="auth.php?signup=success" method="POST">
                <input type="hidden" name="r_id" value="1">
                <label for="r_username">Username</label>
                <input type="text" name="r_username" id="r_username">
                <select name="user" id="user">
                    <option disabled selected>Select a User Roll</option>
                    <option value="admin">Admin</option>
                    <option value="editor">Editor</option>
                </select>
                <label for="r_password">Password</label>
                <input type="password" name="r_password" id="r_password">
                <button type="submit" class="button-primary">Signup</button><br>
                <small>Click here to <a href="auth.php">Login</a></small>
            </form>
            <?php endif; ?>

            </div>
        </div>
    </div>
</body>
</html>