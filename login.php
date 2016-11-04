<?php
session_start();
?>

<!DOCTYPE html>
<head>
    <title>murmur - strona logowania</title>
    <meta charset="utf-8">
    <script src='https://www.google.com/recaptcha/api.js'></script> 
    <link rel="stylesheet" type="text/css" href="style/style.css">
</head>

<body>
    <div class="start">
    <h3>Murmur</h3>
    <p>Aplikacja społecznościowa <br>wzorowana na twitterze</p>
    </div>
    <br><hr>
    <div class="start">
        <form action="log.php" method="post">
            <h4>Zaloguj się</h4>
            <label>e-mail</label><br>
            <input type="text" name="login"><br>
            <label>haslo</label><br>
            <input type="password" name="pass"><br>
            <input type="submit" value="zaloguj">
        </form>
    </div>
    <?php
    if (isset($_SESSION['logError'])) {
        echo $_SESSION['logError'];
    }

?>
    <br><hr><br>
    <div class="start">
        <br><p>Jeżeli nie masz jeszcze konta,<br><a href="register.php"><b>zarejestruj się</b></a></p><br>
    </div>
</body>    