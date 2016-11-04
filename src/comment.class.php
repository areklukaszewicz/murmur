<?php

class Comment {

    private $id;
    private $user_id;
    private $tweet_id;
    private $text;
    private $creation_date;

    public function __construct() {
        $this->id = -1;
        $this->user_id = '';
        $this->tweet_id = '';
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
    
    public function getTweetId() {
        return $this->tweet_id;
    }

    public function setTweetId($tweetId) {
        $this->tweet_id = $tweetId;
        return $this->tweet_id;
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
            $sql = "INSERT INTO Comments(user_id, tweet_id, text, creation_date) VALUES ('$this->user_id', '$this->tweet_id', '$this->text', '$this->creation_date')";
            $result = $connection->query($sql);
            if ($result == true) {
                $this->id = $connection->insert_id;
                return true;
            }
        }
        return false;
    }

//    static public function loadCommentById(mysqli $connection, $id) {
//        $sql = "SELECT * FROM Users JOIN Tweets ON Tweets.user_id=Users.id WHERE Tweets.id=$id";
//
//        $result = $connection->query($sql);
//        if ($result == true && $result->num_rows == 1) {
//            $row = $result->fetch_assoc();
//
//            $loadedTweet = new Tweets();
//            $loadedTweet->id = $row['id'];
//            $loadedTweet->user_id = $row['user_id'];
//            $loadedTweet->username = $row['username'];
//            $loadedTweet->text = $row['text'];
//            $loadedTweet->creation_date = $row['creation_date'];
//            return $loadedTweet;
//        }
//        return null;
//    }

    static public function loadCommentsByTweetId(mysqli $connection, $tweet_id) {
        $sql = "SELECT * FROM Tweets JOIN Comments ON Tweets.id = Comments.tweet_id JOIN Users ON Comments.user_id = Users.id WHERE Tweets.id=$tweet_id ORDER BY Comments.creation_date ASC";
        $ret = [];
        $result = $connection->query($sql);
        if ($result == true && $result->num_rows > 0) {
            foreach ($result as $row) {
                $loadedComment = new Comment();
                $loadedComment->id = $row['id'];
                $loadedComment->user_id = $row['user_id'];
                $loadedComment->tweet_id = $row['tweet_id'];
                $loadedComment->text = $row['text'];
                $loadedComment->creation_date = $row['creation_date'];
                $loadedComment->username = $row['username'];
                $ret[] = $loadedComment;
            }
        }
        return $ret;
    }
}