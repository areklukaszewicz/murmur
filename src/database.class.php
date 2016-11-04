<?php

class Database {
    
  static private $db_host = 'localhost';
  static private $db_user = 'root';
  static private $db_pass = 'coderslab';
  static private $db_name = 'twitter';

  static public function Connection(){

    $conn = new mysqli(self::$db_host, self::$db_user, self::$db_pass, self::$db_name);

    if($conn->connect_error){
      echo "Nieudane połączenie z bazą danych: " . $conn->connect_error;
      die("Koniec");
    } else {

      return $conn;

    }

  }

  static public function endConnection(mysqli $conn){

    $conn->close();
    $conn = null;

    return true;

  }
}