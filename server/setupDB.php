<?php
  include 'connection.php';

  $dbname = "battleship";

  function doesDBExist($conn, $dbname) {
    return $conn->select_db($dbname);
  }

  function doesTableExist($conn, $dbname) {
    $sql = "Select 1 FROM $dbname LIMIT 1";
    return $conn->query($sql);
  }

  function createDatabase($conn, $dbname) {
    // Create database
    if(!doesDBExist($conn, $dbname)) {
      $sql = "CREATE DATABASE " . $dbname;
      if ($conn->query($sql) === TRUE) {
        echo "Database created successfully";
      } else {
        echo "Error creating database: " . $conn->error;
      }
    }
  }
  
  createDatabase($conn, $dbname);

  if(!doesTableExist($conn, "Users")) {
    $sql = "CREATE TABLE Users (
      id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
      username VARCHAR(30) NOT NULL,
      password VARCHAR(255) NOT NULL,
      CONSTRAINT username_unique UNIQUE(username)   

    )";
    
    if ($conn->query($sql) === TRUE) {
      echo "Table MyGuests created successfully";
    } else {
      if ($conn->err() == 1062) {
        print 'no way!';
      }
      echo "Error creating table: " . $conn->error;
    }
  } else echo "users already exists"

?>