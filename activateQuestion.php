<?php

  session_start();


  if($_SERVER['REQUEST_METHOD'] == "POST"){
    
    include 'connect.php';
    $question_id = $_POST['question_id'];
    $stmt = $conn -> prepare("UPDATE questions SET active = 0");
    $stmt -> execute();

    $stmt2 = $conn -> prepare("UPDATE questions SET active = 1, `activation_time`= ? WHERE question_id = ?");
    $stmt2 -> execute(array(microtime(true),$question_id));
    
    echo true;
  }

?>
