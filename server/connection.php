<?php

$servername = "localhost";
$username = "battleshipAdmin";
$password = "4VPnroTOC6wOU3mn";
$dbname = "battleship";

$conn = new mysqli($servername, $username, $password);
$conn->select_db($dbname);
?>