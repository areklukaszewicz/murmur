<?php

class Tweet {

    private $id;
    private $user_id;
    private $text;
    private $creation_date;

    public function __construct() {
        $this->id = -1;
        $this->user_id = '';
        $this->text = '';
        $this->creation_date = date('Y-m-d H:i:s');
    }

    public function getId() {
        return $this->id;
    }

    public function getUserId() {
        return $this->user_id;
    }

    public function setUserId($userId) {
        $this->user_id = $userId;
        return $this->user_id;
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

    public function saveToDB(mysqli $connection) {
        if ($this->id == -1) {
            $sql = "INSERT INTO Tweets(user_id, text, creation_date) VALUES ('$this->user_id', '$this->text', '$this->creation_date')";
            $result = $connection->query($sql);
            if ($result == true) {
                $this->id = $connection->insert_id;
                return true;
            }
        }
        return false;
    }

    static public function loadTweetById(mysqli $connection, $id) {
        $sql = "SELECT * FROM Users JOIN Tweets ON Tweets.user_id=Users.id WHERE Tweets.id=$id";

        $result = $connection->query($sql);
        if ($result == true && $result->num_rows == 1) {
            $row = $result->fetch_assoc();

            $loadedTweet = new Tweet();
            $loadedTweet->id = $row['id'];
            $loadedTweet->user_id = $row['user_id'];
            $loadedTweet->username = $row['username'];
            $loadedTweet->text = $row['text'];
            $loadedTweet->creation_date = $row['creation_date'];
            return $loadedTweet;
        }
        return null;
    }

    static public function loadTweetByUserId(mysqli $connection, $user_id) {
        $sql = "SELECT * FROM Users JOIN Tweets ON Tweets.user_id = Users.id WHERE user_id=$user_id ORDER BY creation_date DESC";
        $ret = [];
        $result = $connection->query($sql);
        if ($result == true && $result->num_rows > 0) {
            foreach ($result as $row) {
                $loadedTweet = new Tweet();
                $loadedTweet->id = $row['id'];
                $loadedTweet->user_id = $row['user_id'];
                $loadedTweet->text = $row['text'];
                $loadedTweet->creation_date = $row['creation_date'];
                $loadedTweet->username = $row['username'];
                $ret[] = $loadedTweet;
            }
        }
        return $ret;
    }

    static public function loadAllTweets(mysqli $connection) {
        $sql = "SELECT * FROM Users JOIN Tweets ON Tweets.user_id = Users.id ORDER BY creation_date DESC";
        $ret = [];

        $result = $connection->query($sql);
        if ($result == true && $result->num_rows != 0) {
            foreach ($result as $row) {
                $loadedTweet = new Tweet();
                $loadedTweet->id = $row['id'];
                $loadedTweet->user_id = $row['user_id'];
                $loadedTweet->username = $row['username'];
                $loadedTweet->text = $row['text'];
                $loadedTweet->creation_date = $row['creation_date'];
                $ret[] = $loadedTweet;
            }
        }
        return $ret;
    }

    public function deleteTweet(mysqli $connection) {
        if ($this->id != -1) {
            $sql = "DELETE FROM Tweets WHERE id=$this->id";
            $result = $connection->query($sql);
            if ($result == true) {
                $this->id = -1;
                return true;
            }
            return false;
        }
        return true;
    }

//    static public function deleteTweet(mysqli $connection, $id) {
//
//        $sql = "DELETE FROM Tweets WHERE id=$id";
//        $result = $connection->query($sql);
//        return true;
//    }

}
