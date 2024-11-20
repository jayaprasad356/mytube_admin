<?php


include('./includes/variables.php');
include_once('includes/custom-functions.php');
$fn = new custom_functions;

if (isset($_POST['btnLogin'])) {

    // get email and password
    $email = $db->escapeString($_POST['email']);
    $password = $db->escapeString($_POST['password']);

    // set time for session timeout
    $currentTime = time() + 25200;
    $expired = 3600;

    // create array variable to handle error
    $error = array();

    // check whether $email is empty or not
    if (empty($email)) {
        $error['email'] = "*Email should be filled.";
    }

    // check whether $password is empty or not
    if (empty($password)) {
        $error['password'] = "*Password should be filled.";
    }

    // if email and password is not empty, check in database
    if (!empty($email) && !empty($password)) {
        $sql_query = "SELECT * FROM admin WHERE email = '$email' AND password = '$password' AND status = 1";
        $db->sql($sql_query);
        $res = $db->getResult();
        $num = $db->numRows($res);
        if($num == 1){
            $_SESSION['id'] = $res[0]['id'];
            $_SESSION['role'] = $res[0]['role'];
            $_SESSION['username'] = $res[0]['name'];
            $_SESSION['email'] = $res[0]['email'];
            $_SESSION['refer_code'] = $res[0]['refer_code'];
            $_SESSION['timeout'] = $currentTime + $expired;
            header("location: reports.php");
            
        }
        else{
            $error['failed'] = "<span class='label label-danger'>Invalid Email or Password!</span>";
        }
    }
}
?>
<?php echo isset($error['update_user']) ? $error['update_user'] : ''; ?>
<div class="col-md-4 col-md-offset-4 " style="margin-top:150px;">
    <!-- general form elements -->
    <div class='row'>
        <div class="col-md-12 text-center">
            <img src="dist/img/icon.jpeg" height="110">
            <h3>Smart Id Maker -Dashboard</h3>
        </div>
        <div class="box box-info col-md-12">
            <div class="box-header with-border">
                <h3 class="box-title">Admin Login</h3>
                <center>
                    <div class="msg"><?php echo isset($error['failed']) ? $error['failed'] : ''; ?></div>
                </center>
            </div><!-- /.box-header -->
            <!-- form start -->
            <form method="post" enctype="multipart/form-data">
                <div class="box-body">
                    <div class="form-group">
                        <label for="exampleInputEmail1">Email :</label>
                        <input type="email" name="email" class="form-control" value="" required>
                    </div>
                    <div class="form-group">
                        <label for="exampleInputEmail1">Password :</label>
                        <input type="password" class="form-control" name="password" value="" required>
                    </div>
                    <div class="box-footer">
                        <button type="submit" name="btnLogin" class="btn btn-info pull-left">Login</button>
                    </div>
                </div>
            </form>
        </div><!-- /.box -->
    </div>
</div>
<?php include('footer.php'); ?>