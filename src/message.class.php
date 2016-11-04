<?php

class Message {

    private $id;
    private $sender_id;
    private $receiver_id;
    private $text;
    private $creation_date;
    private $read_message;

    public function __construct() {
        $this->id = -1;
        $this->sender_id = '';
        $this->receiver_id = '';
        $this->text = '';
        $this->creation_date = date('Y-m-d H:i:s');
        $this->read_message = 0;
    }

    public function getId() {
        return $this->id;
    }

    public function getSenderId() {
        return $this->sender_id;
    }

    public function setSenderId($senderId) {
        $this->sender_id = $senderId;
        return $this->sender_id;
    }

    public function getReceiverId() {
        return $this->receiver_id;
    }

    public function setReceiverId($receiverId) {
        $this->receiver_id = $receiverId;
        return $this->receiver_id;
    }

    public function getText() {
        return $this->text;
    }

    public function setText($text) {
        $this->text = $text;
        return $this->text;
    }

    public function getCreationDate() {
        return $this->creation_date;
    }

    public function getReadMessage() {
        return $this->read_message;
    }

    public function setReadMessage(mysqli $connection) {
        $this->read_message = 1;
        $sql = "UPDATE Messages SET read_message=1 WHERE id=$this->id";
        $connection->query($sql);
        return $this->read_message;
    }

    public function saveToDB(mysqli $connection) {
        if ($this->id == -1) {
            $sql = "INSERT INTO Messages(sender_id, receiver_id, text, creation_date, read_message) VALUES ($this->sender_id, $this->receiver_id, '$this->text', '$this->creation_date', $this->read_message)";
            $result = $connection->query($sql);
            if ($result == true) {
                $this->id = $connection->insert_id;
                return true;
            }
        }
        return false;
    }

    public function messageRead(mysqli $connection, $id) {
        $sql = "UPDATE Messages SET read_message=1 WHERE id=$id";
        $result = $connection->query($sql);
        if ($result == true) {
            return true;
        }
        return false;
    }

    public function checkReceiverId(mysqli $connection, $receiver) {
        $sql = "SELECT * FROM Users WHERE Users.username=" . "'" . $receiver . "'";
        $result = $connection->query($sql);
        $row = $result->fetch_assoc();

        return $row['id'];
    }

    static public function loadSentMessage(mysqli $connection, $id) {
        $sql = "SELECT * FROM Users JOIN Messages ON Users.id = Messages.receiver_id WHERE Messages.id=$id";
        $result = $connection->query($sql);
        if ($result == true && $result->num_rows == 1) {
            $row = $result->fetch_assoc();
            $loadedMessage = new Message();
            $loadedMessage->id = $row['id'];
            $loadedMessage->sender_id = $row['sender_id'];
            $loadedMessage->receiver_id = $row['receiver_id'];
            $loadedMessage->receiver = $row['username'];
            $loadedMessage->text = $row['text'];
            $loadedMessage->creation_date = $row['creation_date'];
            $loadedMessage->read_message = $row['read_message'];
            return $loadedMessage;
        }
        return null;
    }

    static public function loadReceivedMessage(mysqli $connection, $id) {
        $sql = "SELECT * FROM Users JOIN Messages ON Users.id = Messages.sender_id WHERE Messages.id=$id";
        $result = $connection->query($sql);
        if ($result == true && $result->num_rows == 1) {
            $row = $result->fetch_assoc();
            $loadedMessage = new Message();
            $loadedMessage->id = $row['id'];
            $loadedMessage->sender_id = $row['sender_id'];
            $loadedMessage->receiver_id = $row['receiver_id'];
            $loadedMessage->sender = $row['username'];
            $loadedMessage->text = $row['text'];
            $loadedMessage->creation_date = $row['creation_date'];
            $loadedMessage->read_message = $row['read_message'];
            return $loadedMessage;
        }
        return null;
    }

    static public function loadSentMessages(mysqli $connection, $sender_id) {
        $sql = "SELECT * FROM Users JOIN Messages ON Users.id = Messages.receiver_id WHERE Messages.sender_id=$sender_id ORDER BY Messages.creation_date DESC";
        $ret = [];
        $result = $connection->query($sql);
        if ($result == true && $result->num_rows > 0) {
            foreach ($result as $row) {
                $loadedMessage = new Message();
                $loadedMessage->id = $row['id'];
                $loadedMessage->sender_id = $row['sender_id'];
                $loadedMessage->receiver_id = $row['receiver_id'];
                $loadedMessage->receiver = $row['username'];
                $loadedMessage->text = $row['text'];
                $loadedMessage->creation_date = $row['creation_date'];
                $loadedMessage->read_message = $row['read_message'];
                $ret[] = $loadedMessage;
            }
        }
        return $ret;
    }

    static public function loadReceivedMessages(mysqli $connection, $receiver_id) {
        $sql = "SELECT * FROM Users JOIN Messages ON Users.id = Messages.sender_id WHERE Messages.receiver_id=$receiver_id ORDER BY Messages.creation_date DESC";
        $ret = [];
        $result = $connection->query($sql);
        if ($result == true && $result->num_rows > 0) {
            foreach ($result as $row) {
                $loadedMessage = new Message();
                $loadedMessage->id = $row['id'];
                $loadedMessage->sender_id = $row['sender_id'];
                $loadedMessage->receiver_id = $row['receiver_id'];
                $loadedMessage->sender = $row['username'];
                $loadedMessage->text = $row['text'];
                $loadedMessage->creation_date = $row['creation_date'];
                $loadedMessage->read_message = $row['read_message'];
                $ret[] = $loadedMessage;
            }
        }
        return $ret;
    }

}
