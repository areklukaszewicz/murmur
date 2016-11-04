<?php

session_start();

include 'src/database.class.php';
include 'src/user.class.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    unset($regError);
    $username = $_POST['username'];
    if (strlen($username) < 2) {
        $_SESSION['username_error']='Nazwa użytkownika musi liczyć co najmniej 2 znaki';
        $regError = true;
    }
    $email= $_POST['email'];
    $emailFiltered = filter_var($email, FILTER_SANITIZE_EMAIL);
    if ((filter_var($emailFiltered, FILTER_VALIDATE_EMAIL)== false) || ($email != $emailFiltered)){
        $_SESSION['email_error']='Niewłaściwy adres e-mail';
        $regError = true;
    }
    
    $conn = Database::Connection();
    $usernameSql = "SELECT id FROM Users WHERE username='$username'";
    $usernameCheck = $conn->query($usernameSql);
    if (($usernameCheck->num_rows)>0) {
        $_SESSION['username_error']='użytkownik o podanej nazwie już istnieje';
        $regError = true;        
    }
    
    $emailSql = "SELECT id FROM Users WHERE email='$email'";
    $emailCheck = $conn->query($emailSql);
    if (($emailCheck->num_rows)>0) {
        $_SESSION['email_error']='użytkownik o podanym adresie e-mail już istnieje';
        $regError = true;        
    }
    Database::endConnection($conn);
    
    $password = $_POST['password'];
    $passwordRepeat = $_POST['passwordRepeat'];
    if (strlen($password) < 8) {
        $_SESSION['password_error']='Hasło musi liczyć co najmniej 8 znaków';
        $regError = true;
    }
    if ($password != $passwordRepeat) {
        $_SESSION['passwordRepeat_error']='Hasła nie zgadzają się';
        $regError = true;
    }
//    $captchaCode = '6Lct3wcUAAAAAPs1J1tUBps6hT1jVTSEEKBPnmxL';
//    $captchaCheck = file_get_contents('https://www.google.com/recaptcha/api/siteverify?secret='.$captchaCode.'&response='.$_POST['g-recaptcha-response']);
//    
//    $answer= json_decode($captchaCheck);
//    
//    if(!($answer->success)) {
//        $_SESSION['captcha_error'] = 'Potwierdź, że nie jesteś botem';
//        $regError = true;
//    }
}


if (($_SERVER['REQUEST_METHOD'] === 'POST') && (!isset($regError))) {
    $regUser = new User();
    $regUser->setUsername($username);
    $regUser->setPassword($password);
    $regUser->setEmail($email);
    
    $conn = Database::Connection();
    $regUser->saveToDB($conn);
    
        $_SESSION['loggedUser'] = $regUser->getUsername();
        $_SESSION['loggedUserId'] = $regUser->getId();
        
        var_dump($_SESSION['loggedUser']);
        var_dump($_SESSION['loggedUserId']);
        Database::endConnection($conn);
        header('location: index.php');
}

?>
<head>
    <title>murmur - rejestracja użytkownika</title>
    <meta charset="utf-8">
    <script src='https://www.google.com/recaptcha/api.js'></script>
    <link rel="stylesheet" type="text/css" href="style/style.css">
</head>
<body>
    <div>
        <form method="post" action="#">
            <h4>Nowy użytkownik</h4>
            <div class="start" style="width: 250px">
            <label>nazwa użytkownika</label><br>
            <input type="text" name="username">
            <div style="color: red">
            <?php
            if (isset($_SESSION['username_error'])) {
                echo $_SESSION['username_error'];
                unset ($_SESSION['username_error']);
            }
            ?>
            </div>
            <br>
            <label>e-mail</label><br>
            <input type="text" name="email">
            <div style="color: red">
            <?php
            if (isset($_SESSION['email_error'])) {
                echo $_SESSION['email_error'];
                unset ($_SESSION['email_error']);
            }
            ?>
            </div>
            <br>
            <label>hasło</label><br>
            <input type="password" name="password">
            <div style="color: red">
            <?php
            if (isset($_SESSION['password_error'])) {
                echo $_SESSION['password_error'];
                unset ($_SESSION['password_error']);
            }
            ?>
            </div>            
            <br>
            <label>powtórz hasło</label><br>
            <input type="password" name="passwordRepeat">
            <div style="color: red">
            <?php
            if (isset($_SESSION['passwordRepeat_error'])) {
                echo $_SESSION['passwordRepeat_error'];
                unset ($_SESSION['passwordRepeat_error']);
            }
            ?>
            </div>            
<!--            <br></div><br>
            <div class="g-recaptcha" data-sitekey="6Lct3wcUAAAAACDSSCJ2As1mHkY_svnGyIlJj0Mw"></div>
            <div style="color: red">
            <?php
            if (isset($_SESSION['captcha_error'])) {
                echo $_SESSION['captcha_error'];
                unset ($_SESSION['captcha_error']);
            }
            ?>
            </div>-->
            <br>
            <input type="submit" value="zarejestruj się">
        </form>
    </div>
</body>