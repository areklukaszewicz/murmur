<?php
session_start();

if (!isset($_SESSION['loggedUser'])) {
    header('Location: login.php');
}

include 'src/database.class.php';
include 'src/tweet.class.php';
include 'src/user.class.php';
include 'src/message.class.php';

unset($_SESSION['logError']);
?>
<head>
    <link rel="stylesheet" type="text/css" href="style/style.css">
</head>

<?php
echo '<div class="col">';
echo '<div class="menu">';
echo 'zalogowany: ' . $_SESSION['loggedUser'];
echo '<div class="photo">';
echo '<img src="img/'.$_SESSION['loggedUserId'].'.jpg"></img>';
echo '</div><br><a href="logout.php">wyloguj</a><br>';
if (($_SESSION['loggedUserId']) !== $_GET['id']) {
    echo '<br><a href="user.php?id=' . $_SESSION['loggedUserId'] . '">strona uzytkownika</a><br>';
}
echo '<br><a href="profile.php?id=' . $_SESSION['loggedUserId'] . '">zaktualizuj profil</a><br>';
echo '<br><a href="message_box.php">wiadomosci</a><br>';
echo '<br><a href="index.php">Powrot do strony glownej</a><br></div><br></div>';

if (($_SESSION['loggedUserId']) == $_GET['id']) {
    ?>

    <div class="col">
        <div class="form">
            <form method="post" action="#">
                <label>Zamiesc nowy wpis</label><br>
                <textarea style="width: 220px; height: 70px" name="newTweet"></textarea><br>
                <button type="submit" name="submit" value="publish">opublikuj</button>
            </form>
        </div>
    </div>

    <?php
}

    $conn = Database::Connection();
    $userTweets = Tweet::loadTweetByUserId($conn, $_GET['id']);
    $viewedUser = User::loadUserById($conn, $_GET['id']);


    echo '<div class="col">';
    echo 'Posty uzytkownika: ' . $viewedUser->getUsername() . '<br><br>';
    foreach ($userTweets as $row) {
        $text = $row->getText();
        $posted = $row->getCreationDate();
        $id = $row->getUserId();
        $tweet_id = $row->getId();
        $author = $row->username;
        echo '<div class="tweet">';
        echo '<div style="font-size:12px"><b><a href="user.php?id=' . $id . '">' . $author . '</a>, ' . $posted . '</b></div><br>';
        echo $text . '<br><br>';
        echo '<div><div style="font-size:12px; display:inline"><b><a href="tweet.php?id=' . $id . '">otworz post</a></b></div>';
        if (($_SESSION['loggedUserId']) === $id) {
            echo '<div style="font-size:12px; display:inline; float:right"><b><a href="remover.php?id=' . $tweet_id . '&user=' . $id . '">Usun</a></b></div><br>';
        }
        echo '</div></div><br>';
    }
    echo '</div>';
    
    if (($_SESSION['loggedUserId']) !== $_GET['id']) {
        ?>
        <div class="col">
        Wyslij wiadomosc do uzytkownika:
            <div class="message">
                <form method="post" action="#">
                    <textarea style="width: 220px; height: 175px" name="newMessage"></textarea><br>
                    <button type="submit" name="submit">wyslij</button>
                </form>
            </div><br>
        <?php
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $newMessage = new Message();

            $newMessage->setSenderId($_SESSION['loggedUserId']);
            $newMessage->setReceiverId($_GET['id']);
            $newMessage->setText($_POST['newMessage']);

            $newMessage->saveToDB($conn);
            echo 'wiadomosc wyslana!';
        }
        echo '</div>';
    }

    Database::endConnection($conn);

