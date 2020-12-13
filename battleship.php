<?php

function renderTable() {
  
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
  <style>
    table tbody tr td {
      width: 100%;
      border: 1px solid black;
      height: 100px;
    }

    thead {
      width: 100%;
      color: red;
      border: 2px solid green;
      height: 300px;
      width: 100%;
      size: small;
    }

    .background {
      box-sizing: border-box;
      width: 100%;
      height: 100%;
      padding: 3px;
      background-image: url(ocean.gif);
      border: 1px solid black;
      background-size: 100% 200%;
    }
  </style>
  <link rel="stylesheet" href="main.css" />
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Play Battleship</title>
</head>

<body class="background" onload="insertObject();">
  <script>
    var board = new Array(100).fill(0);
    function insertObject() {

      var myTable = document.getElementById('tableData');
      tableData.style.cssText = 'height:60px;width:90%;border: 1px solid black;';
      for (i = 0; i < 10; i++) {
        var row = document.createElement('tr');
        row.style.cssText = 'border: 1px solid black'
        //var rowObj = data[i];
        for (var u = 0; u < 10; u++) {
          var dataEl = document.createElement('td');
          dataEl.id = (i + (u * 10));
          console.log(dataEl.id);
          dataEl.innerHTML = '<input type = "checkbox">';
          dataEl.style.cssText = 'height:60px;width:10.2%;border: 1px solid black;';
          row.appendChild(dataEl);
        }
        myTable.appendChild(row);
      }



      /*for (var i = 0; i < board.length; i++) {
        board[i] = new Array(10);
        for(var k = 0; k < 10; k++){ 
        board[i][k] =5;
        }
      }
      for (var o = 0; o < board.length; o++) {
      
        for(var g = 0; g < 10; g++){
      board[o][g] = 5;  
       console.log(board[0][0])
        }
      }*/
      var r = document.getElementById(12);
      var w = r.innerHTML;


      w = '<input type = "button" checked>';
      //console.log(r);
      //console.log(w);
    }

    function setarray() {
      var a = getElementById("Carrier");

    }
    function coords() {
      var ships = new Array(5);
      var shipsdir = new Array(5);
      var e = document.getElementById("Carrier").value;
      var d = document.getElementById("Battleship").value;
      var c = document.getElementById("Cruiser").value;
      var b = document.getElementById("Submarine").value;
      var a = document.getElementById("Destroyer").value;
      var f = document.getElementById("Direction1").value;
      var g = document.getElementById("Direction2").value;
      var h = document.getElementById("Direction3").value;
      var i = document.getElementById("Direction4").value;
      var j = document.getElementById("Direction5").value;

      shipsdir[4] = f;
      shipsdir[3] = g;
      shipsdir[2] = h;
      shipsdir[1] = i;
      shipsdir[0] = j;

      ships[0] = a;
      ships[1] = b;
      ships[2] = c;
      ships[3] = d;
      ships[4] = e;
      var boatlen = 1;
      for (var i = 0; i < ships.length; i++) {
        var str = ships[i];
        var dir = shipsdir[i];
        var dir2 = 0;
        var letter = 0;
        var str2 = str.charAt(1);
        var num = 0;
        var loc = 0;
        var tmp = 0;
        var counter = 0;

        if (dir == "Right") { dir2 = 10; }
        if (dir == "Left") { dir2 = -10; }
        if (dir == "Up") { dir2 = -1; }
        if (dir == "Down") { dir2 = 1; }

        if (str.charAt(0) == 'A') { letter = 0 }
        if (str.charAt(0) == 'B') { letter = 1 }
        if (str.charAt(0) == 'C') { letter = 2 }
        if (str.charAt(0) == 'D') { letter = 3 }
        if (str.charAt(0) == 'E') { letter = 4 }
        if (str.charAt(0) == 'F') { letter = 5 }
        if (str.charAt(0) == 'G') { letter = 6 }
        if (str.charAt(0) == 'H') { letter = 7 }
        if (str.charAt(0) == 'I') { letter = 8 }
        if (str.charAt(0) == 'J') { letter = 9 }
        num = (parseInt(str2)) - 1;
        loc = (num * 10) + letter;
        var box = document.getElementById(loc);
        console.log(box);
        //var color = document.getElementById(box);


        for (var t = 0; t < boatlen; t++) {
          board[loc] = 1;
          box.style.backgroundColor = "Gray"
          //box.style.backgroundColor="Gray"
          tmp2 = (loc + dir2);
          tmp = document.getElementById(tmp2);
          tmp.style.backgroundColor = "Gray";
          if (t > 0) {
            tmp2 = tmp2 + dir2; tmp = document.getElementById(tmp2);
            tmp.style.backgroundColor = "Gray";
          }
          if (t > 2) {
            tmp2 = tmp2 + dir2; tmp = document.getElementById(tmp2);
            tmp.style.backgroundColor = "Gray";
          }
          if (t > 3) {
            tmp2 = tmp2 + dir2; tmp = document.getElementById(tmp2);
            tmp.style.backgroundColor = "Gray";
          }
          if (t > 4) {
            tmp2 = tmp2dir2; tmp = document.getElementById(tmp2);
            tmp.style.backgroundColor = "Gray";
          }
        }
        boatlen = boatlen + 1;
        //box.style.backgroundColor="Gray"
      }
    }

  </script>
  Game Here
  <form id="form">

  </form>
  <table id="tableData"></table>
  <form id="form2" style="background-color: white;width:93%">

    Carrier
    <input type="text" maxlength="2" style="width: 40px" id="Carrier"></input>
    <label for="Direction1">Direction</label>
    <select name="Direction1" id="Direction1">
      <option value="Right">Right</option>
      <option value="Left">Left</option>
      <option value="Up">Up</option>
      <option value="Down">Down</option>
    </select>
    Battleship

    <input type="text" maxlength="2" style="width: 40px" id="Battleship"></input>
    <label for="Direction2">Direction</label>
    <select name="Direction2" id="Direction2">
      <option value="Right">Right</option>
      <option value="Left">Left</option>
      <option value="Up">Up</option>
      <option value="Down">Down</option>
    </select>
    Cruiser
    <input type="text" maxlength="2" style="width: 40px" id="Cruiser"></input>
    <label for="Direction3">Direction</label>
    <select name="Direction3" id="Direction3">
      <option value="Right">Right</option>
      <option value="Left">Left</option>
      <option value="Up">Up</option>
      <option value="Down">Down</option>
    </select>
    Submarine
    <input type="text" maxlength="2" style="width: 40px" id="Submarine"></input>
    <label for="Direction4">Direction</label>
    <select name="Direction4" id="Direction4">
      <option value="Right">Right</option>
      <option value="Left">Left</option>
      <option value="Up">Up</option>
      <option value="Down">Down</option>
    </select>
    Destroyer
    <input type="text" maxlength="2" style="width: 40px" id="Destroyer"></input>
    <label for="Direction5">Direction</label>
    <select name="Direction5" id="Direction5">
      <option value="Right">Right</option>
      <option value="Left">Left</option>
      <option value="Up">Up</option>
      <option value="Down">Down</option>
    </select>
    <button type="button" onclick=coords();>Set</button>
  </form>

  <iframe width="560" height="315"
    src="https://www.youtube.com/embed/videoseries?list=PLncQ2BTzvewx-U77-NTDldyXClijZaYiZ&autoplay=1" frameborder="0"
    allow="autoplay; encrypted-media" allowfullscreen></iframe>
</body>

</html>