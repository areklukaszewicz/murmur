<?php

session_start();

require 'src/database.class.php';
require 'src/tweet.class.php';

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $conn = Database::Connection();
    $delTweet = Tweet::loadTweetById($conn, $_GET['id']);
    $author = $delTweet->getUserId();
    
    if (isset($_GET['id']) && ($author === $_SESSION['loggedUserId'])) {

        $delTweet->deleteTweet($conn);
        Database::endConnection($conn);

        if (isset($_GET['user'])) {
            header('Location: user.php?id=' . $_GET['user'] . '');
        } else {
            header('Location: index.php');
        }
        
    } else {
        echo 'bledne dane';
    }
    Database::endConnection($conn);
} else {
    echo 'błąd';
}