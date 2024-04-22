<?php
  (set_time_limit(3600));

  include 'connect.php';
  require 'phpSpreadsheet/vendor/autoload.php';
  require 'mailer/vendor/autoload.php';


  use PhpOffice\PhpSpreadsheet\Spreadsheet;
  use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

  use PHPMailer\PHPMailer\PHPMailer;
  use PHPMailer\PHPMailer\Exception;


  function send($subject, $body, $to){
    $mail = new PHPMailer(true);

    try {
        //Server settings
        $mail->SMTPDebug = false;                      //Enable verbose debug output
        $mail->isSMTP();                                            //Send using SMTP
        $mail->Host       = 'smtp.gmail.com';                     //Set the SMTP server to send through
        $mail->SMTPAuth   = true;                                   //Enable SMTP authentication
        $mail->Username   = 'hr@inaya.edu.sa';                     //SMTP username
        $mail->Password   = 'imc123HR#2017';                               //SMTP password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;            //Enable implicit TLS encryption
        $mail->Port       = 465;                                    //TCP port to connect to; use 587 if you have set `SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS`

        //Recipients
        $mail->setFrom('noreply@csmonline.net', 'HR Portal');
        // $mail->addAddress('joe@example.net', 'Joe User');     //Add a recipient
        $mail->addAddress($to);               //Name is optional
        // $mail->addReplyTo('info@example.com', 'Information');
        // $mail->addCC('cc@example.com');
        // $mail->addBCC('bcc@example.com');

        //Content
        $mail->isHTML(true);                                  //Set email format to HTML
        $mail->Subject = $subject;
        $mail->Body    = $body;
        $mail->send();
    } catch (Exception $e) {
        "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
    }
  }
  

  if($_SERVER['REQUEST_METHOD'] == 'POST'){
    $new_table = $conn -> prepare("
      CREATE TABLE IF NOT EXISTS bouns (
        id INT NOT NULL AUTO_INCREMENT,
        empid VARCHAR(10),
        email VARCHAR(40),
        body VARCHAR(255),
        date DATETIME,
        email_sent DATETIME,
        PRIMARY KEY (id))
    ");
    $new_table -> execute();
    if($_FILES['advisor']['size'] > 0){
      $file = $_FILES['advisor']['tmp_name'];
      $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($file);
      $data = $spreadsheet->getActiveSheet()->toArray();
      $r = 0;
      foreach($data as $row){
        if($r > 0){
          $insert = $conn -> prepare("INSERT INTO `bouns`(`id`, `empid`, `email`, `body`, `date`, `email_sent`) VALUES (NULL,?,?,?,?, NULL)");
          $insert -> execute(array($row[0], $row[1],$row[2], date('Y-m-d h:i:s')));
          if(send($_POST['subject'], $row[2],$row[1])){
            $update_table = $conn -> prepare("UPDATE `bouns` SET email_sent = ? WHERE email = ?");
            $update_table -> execute(array(date('Y-m-d h:i:s'), $row[1]));
          }
         }
        $r = 1;
      }
    }
  }
?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
    <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <title>Student Advisors List</title>
    <style>
      table{
        width:30%;
      }
      table tbody tr td:nth-child(odd){
          font-weight: 500;
          font-size: 18px;
      }
    </style>
</head>
<body>
  <div class="container">
    <div class="row">
      <div class="col-md-12 mt-4">
        <div class="card">
          <div class="card-header">
            <h4 class="text-center">Staff Bouns for 2022 - 2023 </h4>
          </div>
          <div class="card-body">
            <form action="<?php echo pathinfo($_SERVER['PHP_SELF'],PATHINFO_FILENAME)?>" enctype="multipart/form-data" method = 'post'>
              <input type="text" class="form-control my-2" placeholder="Subject of the email" name="subject">
              <input type="file" name="advisor" class="form-control">
              <input type="submit" value="upload file and send emails" class="btn btn-primary mt-3"> 
            </form>
          </div>
          <?php
          if($_SERVER['REQUEST_METHOD'] == 'POST' 
            && $_FILES['advisor']['size'] > 0
            && in_array(strtolower(pathinfo($_FILES['advisor']['name'],PATHINFO_EXTENSION)), ['xls', 'xlsx', 'csv'])){?>
            <?php
          }elseif($_SERVER['REQUEST_METHOD'] == 'POST' && $_FILES['advisor']['size'] == 0){ ?>
            <script>
                Swal.fire({
                  icon: 'error',
                  title: 'Oops...',
                  text: 'There is no file chosen. Kindly choose a vaild file',
                })
              </script>
            <?php
          }elseif($_SERVER['REQUEST_METHOD'] == 'POST' && !in_array(strtolower(pathinfo($_FILES['sheet']['name'],PATHINFO_EXTENSION)), ['xls', 'xlsx', 'csv', ''])){?>
            <script>
                Swal.fire({
                  icon: 'error',
                  title: 'Oops...',
                  text: 'Kindly upload only an excel sheet or a csv file',
                })
              </script><?php
            exit();  
          }
          ?>
        </div>
      </div>
    </div>
	</div>
</body>
</html>