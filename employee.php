<?php

  session_start();  
  
  $lang = $_SESSION['lang'];
  $dir = $lang == 'ar' ? 'rtl' : 'ltr';
  include 'connect.php';
  include 'header.php';  
  $user_name_stmt = $conn ->prepare("SELECT * FROM users WHERE empid = ?");
  $user_name_stmt -> execute(array($_SESSION['emp']));
  $u = $user_name_stmt -> fetch();
  $user_name =  $u['user_name'];
  $user_name_ar =  $u['user_name_ar'];
  ?>

<?php
  if(!isset($_SESSION['emp'])){
    header('Location: index');
    exit(); 
  }
  
  function did_i_answer($session, $question_id){
    global $conn;
    $stmt = $conn -> prepare("SELECT q.* FROM `questions` q WHERE active = 1 AND question_id > 0;");
    $stmt -> execute();
    $active_quesitons = $stmt -> rowCount();

    if($active_quesitons > 0){
    $stmt2 = $conn -> prepare("SELECT * FROM `answers` WHERE user_id = ? AND `question_no` = ?;");
    $stmt2 -> execute(array($session,$question_id));
    $my_answer = $stmt2 -> rowCount();
    return $my_answer;
    }
  }


  $stmt = $conn -> prepare("SELECT q.*, qi.question_stmt, qi.a_option, qi.b_option, qi.c_option, qi.d_option FROM `questions` q LEFT JOIN question_info qi ON qi.question_id = q.question_id WHERE lang = ? AND q.question_id > 0 AND q.active = 1;");
  $stmt -> execute(array($lang));
  $question = $stmt -> fetch();
  $active_quesitons = $stmt -> rowCount();

  if($active_quesitons > 0){
    $stmt2 = $conn -> prepare("SELECT * FROM `answers` WHERE user_id = ? AND `question_no` = ?;");
    $stmt2 -> execute(array($_SESSION['emp'],$question['question_id']));
    $my_answer = $stmt2 -> rowCount();
  }else if($active_quesitons == 0){ 
      if($lang == 'en') {?>
        <script>
          Swal.fire({
              position: 'center-center',
              icon: 'info',
              title: 'No active questions yet.' + "\n" + 'Please wait for a new question to be activated',
              showConfirmButton: true,
              })
        </script> <?php
      }else if($lang == 'ar'){?>
        <script>
          console.log("ar");
          Swal.fire({
              position: 'center-center',
              icon: 'info',
              title: 'لا يوجد اسئلة مفعلة' + "\n" + 'برجاء الانتظار حتى يتم تفعيل السؤال التالي',
              showConfirmButton: true,
              })
        </script> <?php

      }
  }

  $active_employee = $conn -> prepare("SELECT * FROM users WHERE empid = ? AND active = 1");
  $active_employee -> execute(array($_SESSION['emp']));
  if($name = $active_employee->fetch()){
    $active = $active_employee -> rowCount();
  }else{
    $active = 0;
  };

  if($active == 0){ ?>
    <script>
        Swal.fire({
          position: 'center-center',
          icon: 'info',
          title: 'You already won a gift' + "\n" + 'Good Luck next year!',
          showConfirmButton: true,
          })
    </script> <?php
    
  }

  if($_SERVER['REQUEST_METHOD'] == 'POST'){
    $active_quesitons_now = $conn -> prepare("SELECT * FROM questions WHERE active = 1");
    $active_quesitons_now -> execute();
    $count_of_active_questions_now = $active_quesitons_now -> rowCount();
    if($count_of_active_questions_now > 0){
      $stmt = $conn -> prepare("INSERT INTO `answers`(`id`, `user_id`, `question_no`, `answer`, `time`) 
                  VALUES (NULL,?,?,?,?)");
      $stmt -> execute(array($_SESSION['emp'],$question['question_id'], $_POST['answer'], microtime(true)));
    }else{
      header('location: employee');
      exit();
    }

    // Get the correct answer 

    $stmt_correct_answer = $conn -> prepare("SELECT * FROM questions WHERE question_id = ?");
    $stmt_correct_answer -> execute(array($question['question_id']));

    $answer = ($stmt_correct_answer -> fetch())['answer'];

    if($answer == $_POST['answer']){ 
      if($lang == 'en'){ ?>
        <script>
          Swal.fire({
            icon: 'success',
            title: 'Congratulations',
            text: 'You have a chance to win',
            showConfirmButton: true,
            timer: 1500
          });
        </script> <?php 
      } else{ ?>
        <script>
          Swal.fire({
            icon: 'success',
            title: 'تهانينا',
            text: 'لديك فرصة في الفوز',
            showConfirmButton: true,
            timer: 1500
          });
        </script> <?php 
      } 
      ?>
      <script>
      setTimeout(() => {
        location.href = "employee";
      }, 3000);
      </script> <?php
    }else{
      if($lang == 'en'){ ?>
        <script>
          Swal.fire({
            icon: 'error',
            title: 'Oops...',
            text: 'Wrong Answer. Try again next time',
            showConfirmButton: false,
          });
        </script> <?php
      }else {
        ?>
        <script>
          Swal.fire({
            icon: 'error',
            title: 'للاسف...',
            text: 'اجابتك خاطئة برجاء المحاولة مرة اخرى',
            showConfirmButton: false,
          });
        </script> <?php
      } ?>
        <script>
        setTimeout(() => {
        location.href = "employee";
      }, 3000);
        </script> <?php
    }
  }



if($active_quesitons == 1 && did_i_answer($_SESSION['emp'], $question['question_id']) == 0 && $active > 0){  ?>
      <div class="container" dir='<?php echo $dir;?>'>
          <div class="modal-dialog">
              <div class="modal-content">
                  <div class="modal-header">
                      <h3 class='question'>
                        <?php 
                            if($lang == 'en'){
                              echo 'Q.'. $question['question_id'] . ' ' . $question['question_stmt'];
                            }else if($lang == 'ar'){
                              echo 'س'. $question['question_id'] . ' ' . $question['question_stmt'];
                            }
                        ?>
                      </h3>
                  </div>
                  <div class="modal-body">
                      <div class="col-xs-3 5"></div>
                          <form action="<?php echo $_SERVER['PHP_SELF']?>" method="POST">
                              <div class="quiz" id="quiz" data-toggle="buttons">
                              <label class="element-animation1 btn btn-lg btn-primary btn-block"><span class="btn-label"><i class="glyphicon glyphicon-chevron-right"></i></span> <input type="radio" name="answer" value="a"><?php echo ' A. ' . $question['a_option']?></label>
                              <label class="element-animation2 btn btn-lg btn-primary btn-block"><span class="btn-label"><i class="glyphicon glyphicon-chevron-right"></i></span> <input type="radio" name="answer" value="b"><?php echo ' B. ' . $question['b_option']?></label>
                              <label class="element-animation3 btn btn-lg btn-primary btn-block"><span class="btn-label"><i class="glyphicon glyphicon-chevron-right"></i></span> <input type="radio" name="answer" value="c"><?php echo ' C. ' . $question['c_option']?></label>
                              <label class="element-animation4 btn btn-lg btn-primary btn-block"><span class="btn-label"><i class="glyphicon glyphicon-chevron-right"></i></span> <input type="radio" name="answer" value="d"><?php echo ' D. ' . $question['d_option']?></label>
                              <input type="submit" class="btn btn-primary" value="<?php if($lang="en"){echo "Answer";}else{echo "أجب";}?>">
                          </form>
                      </div>
                  </div>
              </div>
          </div>
      </div> <?php
  }else{ ?>
      <div class="container" dir="<?php echo $dir;?>">
        <h1 class="title text-center mt-3 pt-3">
            <?php
              if($lang == 'en'){echo 'Welcome';} else {echo 'مرحبا';}
            ?>
          </h1>
        <h2 class="subtitle text-center">
            <?php 
              if($lang == 'en'){
                echo $user_name;
              }else if ($lang == 'ar'){
                echo $user_name_ar;
              }
            ?>
        </h2>
        <ul>
            <li class="subtitle mt-5 fs-5">
            <?php 
              if($lang == 'en'){
                echo 'To get the next question, press the blue button';
              }else if ($lang == 'ar'){
                echo '  للسؤال التالي برجاء الضغط على الزر الازرق';
              }
            ?>
            </li>
            <li class="subtitle fs-5">
            <?php 
              if($lang == 'en'){
                echo 'When you win a gift, you\'ll leave the game';
              }else if ($lang == 'ar'){
                echo 'عند حصولك على هدية ستغادر اللعبة';
              }
            ?>
            </li>
        </ul>
        <p class="subtitle fs-5" style="text-align:right;">
          <?php
            if($lang == 'en'){echo 'Wishing you Good Luck!';} else {echo 'نتمنى لكم حظاً سعيدً';}
          ?> 
        </p>
          <div class="d-flex justify-content-center">
            <div class="mb-5">
              <a href="employee" class="btn btn-primary">
                <?php
                  if($lang == 'en'){echo 'Get the next Question';} else {echo 'السؤال التالي';}
                ?> 
              </a>
              <a href="logout.php" class="btn btn-secondary  mx-3">
              <?php
                  if($lang == 'en'){echo 'Log Out';} else {echo 'خروج';}
                ?> 
              </a>
            </div>
            </div>

          
      </div>
  <?php }
?>
<?php include 'footer.php';?>