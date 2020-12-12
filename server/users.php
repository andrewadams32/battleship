<?php

include "connection.php";

if($_SERVER['REQUEST_METHOD'] === 'GET'){
  if(isset($_GET['a'])) {

  }
} elseif($_SERVER['REQUEST_METHOD'] === 'POST') {
  if ($_SERVER["CONTENT_TYPE"] === "application/json")
    $_POST = json_decode(file_get_contents("php://input"), true) ?: [];
  if(isset($_POST['register'])) {
    registerUser($conn, $_POST['username'], $_POST['password']);
  } elseif(isset($_POST['login'])) {
    loginUser($conn, $_POST['username'], $_POST['password']);
  } elseif(isset($_POST['find-match'])) {
    findOrWaitForGame($conn, $_POST['username']);
  } elseif(isset($_POST['stop-waiting'])) {
    if(setUserWaiting($conn, $_POST['username'], false)) {
      $res = array (
        "ok" => true
      );
      echo json_encode($res);
    } else {
      $res = array (
        "ok" => false,
        "msg" => "unable to stop waiting"
      );
      echo json_encode($res);
    }
  }
}

function loginUser($conn, $username, $password) {
  $sql = "
    SELECT * FROM Users 
    WHERE username='$username'
    LIMIT 1
  ";

  $result = $conn->query($sql);
  $res;

  if ($result->num_rows > 0) {
    $user = $result->fetch_assoc();
    if (password_verify($password, $user['password'])) {
      $res = array (
        "ok" => true,
        "user" => $user['username']
      );
    } else {
      $res = array (
        "ok" => false,
        "msg" => "Incorrect credentials (pwd)"
      );
    }

  } else{
    $res = array (
      "ok" => false,
      "msg" => "Incorrect credentials"
    );
  }

  echo json_encode($res);
}

function registerUser($conn, $username, $password) {
  $hash = password_hash($password, PASSWORD_DEFAULT);
  $sql = "
    INSERT INTO Users (username, password) values ('$username', '$hash')
  ";
  $res;
  if ($conn->query($sql) === TRUE) {
    $res = array (
      "ok" => true, 
    );
  } else {
    if($conn->errno == 1062) {
      $res = array (
        "ok" => false,
        "msg" => "duplicate"
      );
    } else {
      $res = array (
        "ok" => false,
        "msg" => "server error"
      );
    }
  }
  echo json_encode($res);
}

function setUserWaiting($conn, $username, $waiting) {
  $waiting_bool = $waiting ? 1 : 0;
  
  $sql = "
    UPDATE Users
    SET waiting='$waiting_bool'
    WHERE username='$username'
  ";
  if($conn->query($sql) === true) {
    return true;
  } else {
    echo "err" . $conn->error;
    return false;
  }
}

function findGame($conn, $username) {
  $sql = "
    SELECT * FROM Users
    WHERE waiting='1' AND username <> '$username'
    LIMIT 1
  ";

  $result = $conn->query($sql);
  $res;
  if ($result->num_rows > 0) {
    $user = $result->fetch_assoc();
    $res = array (
      "match_found" => true,
      "opponent" => $user['username']
    );
    return $res;
  } else {
    return array (
      "match_found" => false,
      "msg" => $conn->error
    );
  }
}

function findOrWaitForGame($conn, $username) {

  $res = findGame($conn, $username);
  // TODO: create game in Games table with user and opponent as players
  if($res['match_found'] === true) {
    echo json_encode($res);
  } else {
    echo $res['msg'];
    if(setUserWaiting($conn, $username, true) === true) {
      $res = array (
        "match_found" => false,
        "waiting" => true
      );
      echo json_encode($res);
    } else {
      $res = array (
        "match_found" => false,
        "waiting" => false,
        "msg" => "server error"
      );
      echo json_encode($res);
    }
  }
}
?>