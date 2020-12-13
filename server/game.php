<?php

include "connection.php";

if($_SERVER['REQUEST_METHOD'] === 'GET'){} 
elseif($_SERVER['REQUEST_METHOD'] === 'POST') {
  if ($_SERVER["CONTENT_TYPE"] === "application/json")
    $_POST = json_decode(file_get_contents("php://input"), true) ?: [];
  if(isset($_POST['find-or-create'])) {
    findOrCreateGame($conn, $_POST['username']);
  } elseif(isset($_POST['add-player'])) {
    addSecondPlayer($conn, $_POST['username']);
  } elseif(isset($_POST['cancel-game'])) {
    cancelGame($conn, $_POST['username']);
  } elseif(isset($_POST['player-2-joined'])) {
    player2Joined($conn, $_POST['username'], $_POST['game_id']);
  } 
}


//GAME SETUP
function findGame($conn, $username) {
  $sql = "
    SELECT * FROM Games
    WHERE hasStarted='0' AND player1 <> $username AND player2 IS NULL
    LIMIT 1
  ";

  $result = $conn->query($sql);
  if ($result !== false) {
    $game = $result->fetch_assoc();
    $res = array (
      "match_found" => true,
      "opponent" => $game['player1'],
      "game_id" => $game['id']
    );
    return $res;
  } else {
    return array (
      "match_found" => false,
      "msg" => $conn->error
    );
  }
}

function makeGame($conn, $username) {
  $sql = "
    INSERT INTO Games (player1)
    VALUES ('$username')
  ";
  return $conn->query($sql);
}

function addSecondPlayer($conn, $player1, $username) {
  $sql = "
    UPDATE Games
    SET player2 = '$username'
    WHERE player1='$player1';
  ";

  return $conn->query($sql);
}

function cancelGame($conn, $username) {
  $sql = "
    DELETE FROM Games 
    WHERE player1='$username' AND player2 IS NULL
  ";
  if($conn->query($sql)) {
    echo json_encode(array (
      "ok" => true
    ));
  } else echo json_encode(array (
    "ok" => false,
    "msg" => $conn->error
  ));
}

function player2Joined($conn, $username, $game_id) {
  $sql = "
    SELECT * FROM Games
    WHERE hasStarted='0' AND id='$game_id' AND player1='$username' AND player2 IS NOT NULL
    LIMIT 1
  ";

  $result = $conn->query($sql);
  $res;
  if ($result->num_rows > 0) {
    $game = $result->fetch_assoc();
    echo json_encode(array (
      "player_joined" => true,
      "opponent" => $game['player2']
    ));
  } else {
    echo json_encode(array (
      "player_joined" => false,
      "msg" => "no game found"
    ));
  }
}

function findOrCreateGame($conn, $username) {
  $res = findGame($conn, $username);
  if($res['match_found']) {
    addSecondPlayer($conn, $res['opponent'], $username);
    echo json_encode($res);
  } else {
    if(makeGame($conn, $username)) {
      echo json_encode(array (
        "match_found" => false,
        "game_created" => true,
        "game_id" => $conn->insert_id
      ));
    } else echo json_encode(array (
      "match_found" => false,
      "game_created" => false,
      "msg" => "error creating game"
    ));
  }
}


//GAMEPLAY
function getGame($conn, $game_id) {
  $sql = "
    SELECT * FROM Games
    WHERE hasStarted='1' AND id='$game_id'
  ";

  $result = $conn->query($sql);
  if ($result !== false) {
    $game = $result->fetch_assoc();
    echo json_encode(array (
      "game" => $game,
    ));
  } else {
    echo json_encode(array (
      "ok" => false,
      "msg" => $conn->error
    ));
  }
}

//GAMEPLAY
function updateBoard($conn, $board, $game_id) {

  //TODO : handle win/loss logic here

  //
  
  $sql = "
    UPDATE Games
    SET board = '$board'
    WHERE id = '$game_id';
  ";

  $result = $conn->query($sql);
  if ($result !== false) {
    echo json_encode(array (
      "ok" => true,
    ));
  } else {
    echo json_encode(array (
      "ok" => false,
      "msg" => $conn->error
    ));
  }
}

?>