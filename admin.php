<?php 
  include 'connect.php'; 
  include 'header.php';

  $limit = 1;


  $stmt = $conn -> prepare("UPDATE questions SET active = 0");
  $stmt -> execute();

  if(isset($_GET['question_no'])){
    $qn = $_GET['question_no'];
    if($qn > 0){
      $stmt = $conn -> prepare("UPDATE questions SET active = 0");
      $stmt -> execute();
    }
  }else{
    $qn = 1;
  }

  $stmt = $conn -> prepare("SELECT q.*, qi.question_stmt, qi.a_option, qi.b_option, qi.c_option, qi.d_option FROM `questions` q LEFT JOIN question_info qi ON qi.question_id = q.question_id WHERE lang = 'en' AND q.question_id = ?;");
  $stmt -> execute (array($qn));
  $questions = $stmt -> fetchAll(PDO::FETCH_ASSOC);

?>

<div class="container">
  <div class="row">
    <div class="col">
      <h1 class="text-center title mt-3">Annual Celebration 2023 - 2024</h1>
    </div>
  </div>
  <div class="row">
    <div class="col-6">
      <?php
        foreach($questions as $q){ ?>
          <div class="">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="p-4 flex-grow-1" id="question_stmt">
                          <?php 
                            if($q['question_id'] == 44){
                              echo '<div class="d-flex">';
                                echo '<img src="imgs/fahad.png" style="margin:auto;">';
                              echo '</div>';
                            }else{
                              echo 'Q'. $q['question_id'] . '. ' . $q['question_stmt'];
                            }
                          ?>
                        </h5>
                    </div>
                    <div class="modal-body">
                        <div class="quiz d-flex" id="quiz" data-toggle="buttons">
                          <div class="btn btn-lg btn-muted d-block my-2 animate__animated w-100"><span>A. </span><?php echo $q['a_option']?></div>
                          <div class="btn btn-lg btn-muted d-block my-2 animate__animated w-100"><span>B. </span><?php echo $q['b_option']?></div>
                          <div class="btn btn-lg btn-muted d-block my-2 animate__animated w-100"><span>C. </span><?php echo $q['c_option']?></div>
                          <div class="btn btn-lg btn-muted d-block my-2 animate__animated w-100"><span>D. </span><?php echo $q['d_option']?></div>
                        </div>
                    </div>
                </div>
            </div>
          </div> 
          <div class="row buttons">
            <div class="col-6">
              <button class="btn btn-primary" id="activate" data-question_id="<?php echo $q['question_id']?>">Activate</button>
            </div>
            <div class="col-6">
              <button class="btn btn-primary" style="float:right" id="getWinner" data-question_id="<?php echo $q['question_id']?>">Get Winner</button>
            </div>
          </div> <?php
        }
      ?>
      <ul class="text-center" id="answers">
        <?php
          $stmt = $conn -> prepare("SELECT * FROM questions");
          $stmt -> execute();
          $total_records = $stmt -> rowCount();
          $total_pages = ceil($total_records / $limit);
          $k = (($qn+2>$total_pages)?$total_pages-2:(($qn-2<1)?3:$qn));	
          $pagLink = "";
          if($qn>=2){
            echo "<li><a href='admin?question_no=1'> << </a></li>";
            echo "<li><a href='admin?question_no=".($qn-1)."'> < </a></li>";
          }
          for ($i=-2; $i<=2; $i++) {
            if($k+$i==$qn)
              $pagLink .= "<li class='active'><a href='admin?question_no=".($k+$i)."'>".($k+$i)."</a></li>";
            else
              $pagLink .= "<li><a href='admin?question_no=".($k+$i)."'>".($k+$i)."</a></li>";
            };
            echo $pagLink;
            if($qn<$total_pages){
              echo "<li><a href='admin?question_no=".($qn+1)."'> > </a></li>";
              echo "<li><a href='admin?question_no=".$total_pages."'> >> </a></li>";
            }	
        ?>
      </ul>
    </div>
    <div class="col-6">
    <script type="text/javascript" src="googleChart.js"></script>
    <script type="text/javascript">
        google.charts.load('current', {'packages':['corechart']});
        google.charts.setOnLoadCallback(drawChart);

        let getDataBtn = document.getElementById("getData");
        let output = '';

        function drawChart() {

          var data = google.visualization.arrayToDataTable([
            ['Task', 'Hours per Day'],
            <?php
                include 'connect.php';
                if($_SERVER['REQUEST_METHOD'] == 'GET'){
                  $question_id = $_GET['question_no'];
                  $stmt = $conn -> prepare("SELECT `question_no`, `answer`, COUNT(`user_id`) AS 'count' FROM `answers` WHERE `question_no` = ? GROUP BY `question_no`, `answer`;;");
                  $stmt -> execute(array($question_id));
                  $results = $stmt -> fetchAll(PDO::FETCH_ASSOC);
                  foreach($results as $r){
                    echo  "['" . strtoupper($r['answer']) . "', " . $r['count'] . "],";
                  } 
                }
            ?>
          ]);

          var options = {
            is3D: true,
            legend: {position: 'top', alignment: 'center' , textStyle: {color: 'blue', fontSize: 16}},
            pieSliceText: 'percentage',
          };

          var chart = new google.visualization.PieChart(document.getElementById('piechart'));

          chart.draw(data, options);
        }
      </script>

    <div id="piechart" style="width: 100%; height: 100%;"></div>
      </div>
    </div>

<?php // include 'footer.php';?>