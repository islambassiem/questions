<?php
    session_start();


    // $_SESSION = array();

    $title = 'Log in';
    include 'header.php';
    include 'connect.php';
    
    if(isset($_SESSION['emp'])){
        header('location: employee');
        exit();
    }
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $user = $_POST['user'];
        $pass = sha1($_POST['password']);
        $lang = $_POST['lang'];

        $stmt = $conn -> prepare("SELECT * FROM users WHERE empid = ? AND `password` = ?");
        $stmt -> execute(array($user, $pass));
        $row = $stmt -> fetch();   
        $count = $stmt -> rowCount();

        if ($count > 0 ){
            session_start();
            $_SESSION['emp']  = $row['empid'];
            $_SESSION['lang'] = $lang;
            $stmt = $conn ->prepare("UPDATE users SET present = ? , ip = ? WHERE empid = ?");
            $stmt -> execute(array(date('Y-m-d h:i:s'),$_SERVER['REMOTE_ADDR'],$row['empid']));
            header('location: employee');
            exit();
        }
    }
?>

<div class="container-fluid">
    <div class="row">
            <div class="col-md-7">
                <div class="video">
                    <div class="pane">
                            <p>Inaya Medical Colleges</p>
                            <video id="vid" type="video/mp4" muted loop autoplay playsinline>
                                <source src="imgs/video.mp4">
                            </video>
                            <p>Reach for the Sky!</p>
                    </div>
                </div>
            </div>
            <div class="col-md-5">
                <div class="login d-flex align-items-center">
                    <div class="login-form flex-fill">
                        <div class="h1 text-center my-3 text-primary">Log In</div>
                            <form action="<?php echo basename($_SERVER['PHP_SELF'],'.php');?>" method="POST" class="mb-3">
                                <input  type="text"
                                        autocomplete="off"
                                        placeholder="Type your Identification Key"
                                        name="user"
                                        class="form-control mb-3">
                                <input  type="password"
                                        placeholder="Type your password"
                                        name="password"
                                        class="form-control mb-3">
                                <select name="lang" id="lang" class="form-control py-2">
                                    <option value="en">Engligh</option>
                                    <option value="ar">عربي</option>
                                </select>
                                <input  type="submit"
                                        value="Log In"
                                        class="form-control my-3 btn btn-info">
                                <!-- <div class="d-flex justify-content-between fs-6">
                                    <a type="button" class="" data-bs-toggle="modal" data-bs-target="#exampleModal">
                                    Forgot your password ? 
                                    </a>
                                    <p><a href="ar/index">عربي</a></p>
                                </div> -->
                                <!-- <input  type="button"
                                        value="Log In With Google   "
                                        class="form-control mb-3 btn btn-info"
                                        onclick="window.location='<?php echo $gClient->createAuthUrl();?>'"> -->
                                <!-- <img
                                    style="width:100%; cursor:pointer; margin-bottom:30px;"
                                    src="imgs/google.jpeg" 
                                    alt=""
                                    onclick="window.location='<?php echo $gClient->createAuthUrl();?>'"> -->
                            </form>
                            <?php
                                if($_SERVER['REQUEST_METHOD'] == 'POST' || $pass=''){
                                    //echo '<div class="alert alert-danger text-center">Login Information is not correct</div>';?>
                                    <script>
                                        Swal.fire({
                                        icon: 'error',
                                        title: 'Oops...',
                                        text: 'The Information you submitted is not correct!'
                                        })
                                    </script>
                                <?php }
                            ?>
                        </div>
                </div>
            </div>
    </div>
</div>

<div class="container-fluid fixed-bottom text-light footer" style="background-color: #1f376a;">
    <div class="row">
        <div class="footer text-center">
            <div class="py-2 my-2">Copyright &copy; <?php echo date('Y')?> IMC - Human Resources Department</div>
        </div>
    </div>
</div>    

