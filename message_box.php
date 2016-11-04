<?php

session_start();

if (!isset($_SESSION['loggedUser'])) {
    header('Location: login.php');
}

include 'src/database.class.php';
include 'src/message.class.php';

unset($_SESSION['logError']);
?>

<head>
    <link rel="stylesheet" type="text/css" href="style/style.css">
</head>

<?php
echo'<div class="col">';
echo '<div class="menu">';
echo 'zalogowany: ' . $_SESSION['loggedUser'];
echo '<br><br><a href="logout.php">wyloguj</a><br>';
echo '<br><a href="index.php">Powrot do strony glownej</a></div><br>';

$conn = Database::Connection();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $newMessage = new Message();

    $newMessage->setSenderId($_SESSION['loggedUserId']);
    $newMessage->setReceiverId($newMessage->checkReceiverId($conn, $_POST['receiver']));
    $newMessage->setText($_POST['newMessage']);

    $newMessage->saveToDB($conn);
    echo 'wiadomosc wyslana';
}

?>

<br>
<div class="form">
    <div>Wyslij nowa wiadomosc
    <form method="post" action="#">
        <label>do:</label><br>
        <input style="width: 220px" type="text" name="receiver"><br>
        <label>tresc wiadomosci:</label><br>
        <textarea style="width: 220px; height: 175px" name="newMessage"></textarea><br>
        <button type="submit" name="submit">wyslij</button>
    </form>
    </div>
</div>
</div>


<?php

$allReceivedMessages = Message::loadReceivedMessages($conn, $_SESSION['loggedUserId']);

echo '<div class="col">';
echo 'odebrane wiadomosci<br><br>';
foreach ($allReceivedMessages as $row) {
    $text = $row->getText();
    $received = $row->getCreationDate();
    $id = $row->getId();
    $read_message = $row->getReadMessage();
    $sender_id = $row->getSenderId();
    $receiver_id = $row->getReceiverId();
    $sender = $row->sender;
    if ($read_message == 0) {
        echo '<div class="message unread">';
    } else {
        echo '<div class="message">';
    }
    echo '<div style="font-size:12px"><b>Nadawca: <a href="user.php?id='.$sender_id.'">' . $sender . '</a><br>' . $received . '</b></div><br>';
    echo '<div> <a href="message.php?id='.$id.'&mes=r">'.substr($text, 0, 30).'</a></div></div><br>';
    if (strlen($text)>30) {
        echo '...';
    }
}
    echo '<br></div>';

$allSentMessages = Message::loadSentMessages($conn, $_SESSION['loggedUserId']);

echo '<div class="col">';
echo 'wyslane wiadomosci<br><br>';
foreach ($allSentMessages as $row) {
    $text = $row->getText();
    $sent = $row->getCreationDate();
    $id = $row->getId();
    $sender_id = $row->getSenderId();
    $receiver_id = $row->getReceiverId();
    $receiver = $row->receiver;
    if ($read_message == 0) {
        echo '<div class="message unread">';
    } else {
        echo '<div class="message">';
    }
    echo '<div style="font-size:12px"><b>Wyslano do: <a href="user.php?id='.$receiver_id.'">' . $receiver . '</a><br>' . $sent . '</b></div><br>';
    echo '<div> <a href="message.php?id='.$id.'&mes=s">'.substr($text, 0, 30);
    if (strlen($text)>30) {
        echo '...';
    }
    echo '</a></div></div><br>';
    
}
    echo '<br></div>';
    Database::endConnection($conn);

