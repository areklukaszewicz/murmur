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

echo '<div class="col">';
echo '<div class="menu">';
echo 'zalogowany: ' . $_SESSION['loggedUser'];
echo '<br><br><a href="logout.php">wyloguj</a><br>';
echo '<br><a href="index.php">Powrot do strony glownej</a><br>';
echo '<br><a href="message_box.php">Przejdz do wszystkich wiadomosci</a><br>';
echo '</div><br></div>';

$conn = Database::Connection();

?>

<div class="col">

    <?php
    switch ($_GET['mes']) {
        case 's':
            $singleMessage = Message::loadSentMessage($conn, $_GET['id']);

            $text = $singleMessage->getText();
            $sent = $singleMessage->getCreationDate();
            $id = $singleMessage->getId();
            $sender_id = $singleMessage->getSenderId();
            $receiver_id = $singleMessage->getReceiverId();
            $receiver = $singleMessage->receiver;
            echo 'Nadana wiadomosc<br><br>';
            echo '<div class="message">';
            echo '<div style="font-size:12px"><b>Wyslano do: <a href="user.php?id=' . $receiver_id . '">' . $receiver . '</a><br>' . $sent . '</b></div><br>';
            echo $text . '</div></div><br>';
            break;
        case 'r':
            $singleMessage = Message::loadReceivedMessage($conn, $_GET['id']);

            $singleMessage->setReadMessage($conn);
            $text = $singleMessage->getText();
            $received = $singleMessage->getCreationDate();
            $id = $singleMessage->getId();
            $sender_id = $singleMessage->getSenderId();
            $receiver_id = $singleMessage->getReceiverId();
            $sender = $singleMessage->sender;
            echo 'Odebrana wiadomosc<br><br>';
            echo '<div class="message">';
            echo '<div style="font-size:12px"><b>Nadawca: <a href="user.php?id=' . $sender_id . '">' . $sender . '</a><br>' . $received . '</b></div><br>';
            echo $text . '</div><br>';
            echo '<br>Odpowiedz na wiadomosc
            <div class="message">
                <form method="post" action="#">
                    <textarea style="width: 220px; height: 175px" name="newMessage"></textarea><br>
                    <button type="submit" name="submit">wyslij</button>
                </form>
            </div><br>';
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $newMessage = new Message();

                $newMessage->setSenderId($_SESSION['loggedUserId']);
                $newMessage->setReceiverId($sender_id);
                $newMessage->setText($_POST['newMessage']);

                $newMessage->saveToDB($conn);
                echo 'odpowiedz wyslana!';
            }
            echo '</div>';
            break;
        default:
            echo 'wystapil blad. nie mozna wczytac wiadomosci';
    }
Database::endConnection($conn);

