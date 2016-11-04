<?php

class User {

    private $id;
    private $username;
    private $hashedPassword;
    private $email;

    public function __construct() {
        $this->id = -1;
        $this->username = '';
        $this->hashedPassword = '';
        $this->email = '';
    }

    public function getId() {
        return $this->id;
    }

    public function getUsername() {
        return $this->username;
    }

    public function setUsername($newUsername) {
        $this->username = $newUsername;
        return $this->username;
    }

    public function getPassword() {
        return $this->hashedPassword;
    }

    public function setPassword($newPassword) {
        $newHashedPassword = password_hash($newPassword, PASSWORD_BCRYPT);
        $this->hashedPassword = $newHashedPassword;
        return $this->hashedPassword;
    }

    public function getEmail() {
        return $this->email;
    }

    public function setEmail($newEmail) {
        $this->email = $newEmail;
        return $this->email;
    }

    public function saveToDB(mysqli $connection) {
        if ($this->id == -1) {
            $sql = "INSERT INTO Users(username, email, hashedPassword) VALUES ('$this->username', '$this->email', '$this->hashedPassword')";
            $result = $connection->query($sql);
            if ($result == true) {
                $this->id = $connection->insert_id;
                return true;
            }
        } else {
            $sql = "UPDATE Users SET username='$this->username', email='$this->email', hashedPassword='$this->hashedPassword' WHERE id=$this->id";
            $result = $connection->query($sql);
            if ($result == true) {
                return true;
            }
        }

        return false;
    }

    static public function loadUserById(mysqli $connection, $id) {
        $sql = "SELECT * FROM Users WHERE id=$id";
        $result = $connection->query($sql);
        if ($result == true && $result->num_rows == 1) {
            $row = $result->fetch_assoc();

            $loadedUser = new User();
            $loadedUser->id = $row['id'];
            $loadedUser->username = $row['username'];
            $loadedUser->hashedPassword = $row['hashedPassword'];
            $loadedUser->email = $row['email'];
            return $loadedUser;
        }
        return null;
    }

    static public function loadAllUsers(mysqli $connection) {
        $sql = "SELECT * FROM Users";
        $ret = [];

        $result = $connection->query($sql);
        if ($result == true && $result->num_rows != 0) {
            foreach ($result as $row) {
                $loadedUser = new User();
                $loadedUser->id = $row['id'];
                $loadedUser->username = $row['username'];
                $loadedUser->hashedPassword = $row['hashedPassword'];
                $loadedUser->email = $row['email'];
                $ret[] = $loadedUser;
            }
        }
        return $ret;
    }

    public function deleteUser(mysqli $connection) {
        if ($this->id != -1) {
            $sql = "DELETE FROM Users WHERE id=$this->id";
            $result = $connection->query($sql);
            if ($result == true) {
                $this->id = -1;
                return true;
            }
            return false;
        }
        return true;
    }

}
