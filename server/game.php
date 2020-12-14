<?php

include "connection.php";

if($_SERVER['REQUEST_METHOD'] === 'GET'){
  if(isset($_GET['leaderboard'])) {
    getLeaderBoardStats($conn, $_GET['sort']);
  } elseif(isset($_GET['game'])) {
    getGame($conn, $_GET['game_id']);
  }
} 
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
  } elseif(isset($_POST['update-board'])) {
    updateBoard($conn, $_POST['board'], $_POST['player1'], $_POST['game_id']);
  } elseif(isset($_POST['send-attack'])) {
    if(isset($_POST['winner'])) {
      ob_start();
      sendAttack($conn, $_POST['player1'], $_POST['board'], $_POST['game_id']);
      ob_end_clean();
      endGame($conn, $_POST['winner'], $_POST['loser'], $_POST['game_id']);
    } else {
      sendAttack($conn, $_POST['player1'], $_POST['board'], $_POST['game_id']);
    }
  }
}

//LEADERBOARD
function getLeaderBoardStats($conn, $sort) {
  $sql;
  if($sort === "wins")
    $sql = "
      SELECT winner, COUNT(*) as count 
      FROM Games
      GROUP BY winner 
      ORDER BY count DESC
    ";
  elseif($sort === "games")
    $sql = "
      SELECT player1, player2, COUNT(*) as count 
      FROM Games
      GROUP BY player1
      ORDER BY count DESC
    ";
  $res = $conn->query($sql);
  $stats = array();
  if( $res->num_rows > 0) {
    while($row = $res->fetch_assoc())
      array_push($stats, array (
        "player" => $row["winner"],
        "winCount" => $row["count"]
      ));
  }
  print_r(json_encode($stats));
}

//GAME SETUP
function findGame($conn, $username) {
  $sql = "
    SELECT * FROM Games
    WHERE hasStarted='0' AND player1 <> '$username' AND player2 IS NULL
    LIMIT 1
  ";

  $result = $conn->query($sql);
  if ($result->num_rows > 0) {
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
    INSERT INTO Games (player1, turn)
    VALUES ('$username', '$username')
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

    $sql = "
      UPDATE Games
      SET hasStarted = '0'
      WHERE id='$game_id' AND hasStarted='0' AND player1='$username';
    ";

    if($conn->query($sql)) {
      echo json_encode(array (
        "player_joined" => true,
        "opponent" => $game['player2']
      ));
    } else {
      echo json_encode(array (
        "player_joined" => true,
        "opponent" => $game['player2'],
        "error" => $conn->error
      ));
    }
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
      "msg" => $conn->error
    ));
  }
}

//GAMEPLAY

function getGame($conn, $game_id) {
  $sql = "
    SELECT * FROM Games
    WHERE id='$game_id'
  ";

  $result = $conn->query($sql);
  if ($result !== false) {
    $game = $result->fetch_assoc();
    $game["player1Board"] = unserialize($game["player1Board"]);
    $game["player2Board"] = unserialize($game["player2Board"]);
    echo json_encode(array (
      "game" => $game,
      "ok" => true
    ));
  } else {
    echo json_encode(array (
      "ok" => false,
      "msg" => $conn->error
    ));
  }
}

function sendAttack($conn, $player1, $otherBoard, $game_id) {
  
  $sql = "
    SELECT * FROM Games
    WHERE id='$game_id'
  ";

  $result = $conn->query($sql);
  if ($result !== false) {
    $game = $result->fetch_assoc();
    $nextTurn = $game['turn'] === $game['player1'] ? $game['player2'] : $game['player1'];
    $serialized_board = serialize($otherBoard);

    if($player1) {
      $sql = "
        UPDATE Games
        SET player2Board = '$serialized_board', turn = '$nextTurn', hasStarted='1'
        WHERE id = '$game_id';
      ";
    } else {
      $sql = "
        UPDATE Games
        SET player1Board = '$serialized_board', turn = '$nextTurn'
        WHERE id = '$game_id';
      ";
    }
  
    $result = $conn->query($sql);
    if ($result !== false) {
      echo json_encode(array (
        "ok" => true
      ));
    } else {
      echo json_encode(array (
        "ok" => false,
        "msg" => $conn->error
      ));
    }
  }
}

function updateBoard($conn, $board, $player1, $game_id) {
  $serialized_board = serialize($board);

  if($player1) {
    $sql = "
      UPDATE Games
      SET player1Board = '$serialized_board'
      WHERE id = '$game_id';
    ";
  } else {
    $sql = "
      UPDATE Games
      SET player2Board = '$serialized_board'
      WHERE id = '$game_id';
    ";
  }

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

function endGame($conn, $winner, $loser, $game_id) {
  $sql = "
    UPDATE Games
    SET winner='$winner', loser='$loser', turn=''
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