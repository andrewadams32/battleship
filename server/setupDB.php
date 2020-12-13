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
  $conn->select_db($dbname);

  if(!doesTableExist($conn, "Users")) {
    $sql = "CREATE TABLE Users (
      id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
      username VARCHAR(30) NOT NULL,
      password VARCHAR(255) NOT NULL,
      waiting BOOLEAN DEFAULT FALSE,
      CONSTRAINT username_unique UNIQUE(username)
    )";
    
    if ($conn->query($sql) === TRUE) {
      echo "Table MyGuests created successfully";
    } else {
      echo "Error creating table: " . $conn->error;
    }
  } else echo "users already exists";

  if(!doesTableExist($conn, "Games")) {
    $sql = "CREATE TABLE Games (
      id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
      player1 VARCHAR(30) NOT NULL,
      player2 VARCHAR(30),
      winner VARCHAR(30),
      loser VARCHAR(30),
      hasStarted BOOLEAN DEFAULT FALSE,
      board TINYTEXT,
      turn VARCHAR(30)
    )";
    
    if ($conn->query($sql) === TRUE) {
      echo "Table MyGuests created successfully";
    } else {
      echo "Error creating table: " . $conn->error;
    }
  } else echo "games already exists"

?>