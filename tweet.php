<?php
session_start();

if (!isset($_SESSION['loggedUser'])) {
    header('Location: login.php');
}

include 'src/database.class.php';
include 'src/tweet.class.php';
include 'src/comment.class.php';

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
echo '<br><a href="profile.php?id='.$_SESSION['loggedUserId'].'">zaktualizuj profil</a><br>';
echo '<br><a href="message_box.php">wiadomosci</a><br>';
echo '<br><a href="index.php">Powrot do strony glownej</a><br></div><br></div>';

$conn = Database::Connection();
$singleTweet = Tweet::loadTweetById($conn, $_GET['id']);

$text = $singleTweet->getText();
$posted = $singleTweet->getCreationDate();
$post_id = $singleTweet->getId();
$user_id = $singleTweet->getUserId();
$post_author = $singleTweet->username;
echo '<div class="col">';
echo '<div class="tweet">';
echo '<div style="font-size:12px"><b><a href="user.php?id=' . $user_id . '">' . $post_author . '</a>, ' . $posted . '</b></div><br>';
echo $text.'<br><br>';
if (($_SESSION['loggedUserId']) === $user_id) {
        echo '<div style="font-size:12px; display:inline; float:right"><b><a href="remover.php?id=' . $post_id . '">Usun</a></b></div><br>';
    }
echo '</div>';
?>
<br>
<div class="form">
    <form method="post" action="#">
        <textarea style="width:220px; height: 70px" name="newComment"></textarea><br>
        <button type="submit" name="submit" value="publish">skomentuj</button>
    </form>
</div>
</div>

<?php
$allComments = Comment::loadCommentsByTweetId($conn, $post_id);

echo '<div class="col">';
echo 'Komentarze do wpisu:<br><br>';
foreach ($allComments as $row) {
    $text = $row->getText();
    $posted = $row->getCreationDate();
    $id = $row->getId();
    $user_id = $row->getUserId();
    $author = $row->username;
    echo '<div class="comment">';
    echo '<div style="font-size:12px"><b><a href="user.php?id=' . $user_id . '">' . $author . '</a>, ' . $posted . '</b></div><br>';
    echo $text . '</div><br>';

}
echo '</div>';
Database::endConnection($conn);




if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $newComment = new Comment();

    $newComment->setUserId($_SESSION['loggedUserId']);
    $newComment->setText($_POST['newComment']);
    $newComment->setTweetId($post_id);
    
    $conn = Database::Connection();
    $newComment->saveToDB($conn);
    Database::endConnection($conn);
    header('Location: tweet.php?id=' . $post_id);
}
?>





