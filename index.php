<?php
session_start();

if (!isset($_SESSION['loggedUser'])) {
    header('Location: login.php');
}

include 'src/user.class.php';
include 'src/tweet.class.php';
include 'src/database.class.php';
?>
<head>
    <title>murmur - strona główna</title>
    <link rel="stylesheet" type="text/css" href="style/style.css">
</head>
<body>

    <?php
unset($_SESSION['logError']);
echo '<div class="col">';
echo '<div class="menu">';
echo 'zalogowany: ' . $_SESSION['loggedUser'];
echo '<br>';
echo '<div class="photo">';
echo '<img src="img/'.$_SESSION['loggedUserId'].'.jpg"></img>';
echo '</div><br><a href="logout.php">wyloguj</a><br>';
echo '<br><a href="user.php?id='.$_SESSION['loggedUserId'].'">strona uzytkownika</a><br>';
echo '<br><a href="profile.php?id='.$_SESSION['loggedUserId'].'">zaktualizuj profil</a><br>';
echo '<br><a href="message_box.php">wiadomosci</a><br><br></div><br>';
echo '</div>';
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
$conn = Database::Connection();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    echo $_POST['newTweet'];
    $newTweet = new Tweet();
    $newTweet->setUserId($_SESSION['loggedUserId']);
    $newTweet->setText($_POST['newTweet']);
    $newTweet->saveToDB($conn);
    header('Location: index.php');
}
    echo '<div class="col">';
$allTweets = Tweet::loadAllTweets($conn);

foreach ($allTweets as $row) {
    $text = $row->getText();
    $posted = $row->getCreationDate();
    $id = $row->getId();
    $user_id = $row->getUserId();
    $author = $row->username;

    echo '<div class="tweet">';
    echo '<div style="font-size:12px"><b><a href="user.php?id=' . $user_id . '">' . $author . '</a>, ' . $posted . '</b></div><br>';
    echo $text . '<br><br>';
    echo '<div><div style="font-size:12px; display:inline"><b><a href="tweet.php?id=' . $id . '">otworz post</a></b></div>';
        if (($_SESSION['loggedUserId']) === $user_id) {
        echo '<div style="font-size:12px; display:inline; float:right"><b><a href="remover.php?id=' . $id . '">Usun</a></b></div><br>';
    }
    echo '</div></div><br>';

}
    echo '</div>';
    Database::endConnection($conn);
?>
</body>