<?php

function createDatabase($conn, $dbname) {
  // Create database
  $sql = "CREATE DATABASE " . $dbname;
  if ($conn->query($sql) === TRUE) {
    echo "Database created successfully";
  } else {
    echo "Error creating database: " . $conn->error;
  }

  $conn->close();
}

?>