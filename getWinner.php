<?php

if($_SERVER['REQUEST_METHOD'] == "POST"){
  include 'connect.php';
  $question_id = $_POST['question_id'];

  $stmt = $conn -> prepare("SELECT * FROM (
                              SELECT 
                                    a.user_id, 
                                    u.user_name,
                                    a.question_no, 
                                    a.answer, 
                                    a.time,
                                    q.activation_time,
                                    abs(q.activation_time - a.time) AS duration,
                                    q.answer AS model_answer,
                                    q.img, 
                                    q.gift,
                                    IF(a.answer = q.answer, 1,0) AS mark 
                                FROM `answers` a
                                JOIN questions q ON q.question_id = a.question_no
                                JOIN users u ON u.empid = a.user_id) z
                            WHERE mark = 1 AND question_no = ?
                            ORDER BY time
                            LIMIT 1;"); 
  $stmt -> execute(array($question_id));
  $row = $stmt -> fetch(PDO::FETCH_ASSOC);
  if(isset($row['user_name'])){
    if($question_id > 0){
      $stmt = $conn ->prepare("UPDATE users SET active = 0 WHERE empid = ?");
      $stmt -> execute(array($row['user_id']));

      $stmt2 = $conn -> prepare("UPDATE users SET gift = ? WHERE empid = ?");
      $stmt2 -> execute(array($row['gift'], $row['user_id']));
    }
    echo "<img src='". $row['img'] ."' style='margin-bottom:20px; max-width:200px; max-height:200px;'><br>";
    echo $row['user_name'];
    echo '<br>';
    echo '<b>' . number_format($row['duration'],4) . '<b>' . ' Seconds';
  }else{
    echo 'No one has the right answer';
  }
}

?>