<?php

session_start();

require 'src/database.class.php';
require 'src/user.class.php';
$conn = Database::Connection();

$email = $_POST['login'];
$passw = $_POST['pass'];

if ((isset($email)) && (isset($passw))) {
    $sql = 'SELECT * FROM Users WHERE email="' . $email . '"';
    $selected = $conn->query($sql);

    $verified = $selected->fetch_assoc();
    if (password_verify($passw, $verified['hashedPassword'])) {


        $_SESSION['loggedUser'] = $verified['username'];
        $_SESSION['loggedUserId'] = $verified['id'];
        Database::endConnection($conn);
        header('location: index.php');
    } else {
        $_SESSION['logError'] = '<span style="color:red">Nieprawidlowy login lub haslo</span>';
        header('location: login.php');
    }
} else {
    echo 'Wystąpił błąd. Logowanie jest w tej chwili niemożliwe';
    Database::endConnection($conn);
}
