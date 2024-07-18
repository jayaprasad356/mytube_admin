<?php
include_once('includes/functions.php');
$function = new functions;
include_once('includes/custom-functions.php');
$fn = new custom_functions;

$sql = "SELECT id, name FROM categories ORDER BY id ASC";
$db->sql($sql);
$res = $db->getResult();

?>
<?php
if (isset($_POST['btnAdd'])) {

        $name = $db->escapeString(($_POST['name']));
        $role = $db->escapeString($_POST['role']);
        $password = $db->escapeString(($_POST['password']));
        $refer_code = $db->escapeString(($_POST['refer_code']));
        $email = $db->escapeString(($_POST['email']));
        $error = array();
       
        if (empty($name)) {
            $error['name'] = " <span class='label label-danger'>Required!</span>";
        }
        if (empty($role)) {
            $error['role'] = " <span class='label label-danger'>Required!</span>";
        }
        if (empty($password)) {
            $error['password'] = " <span class='label label-danger'>Required!</span>";
        }
        if (empty($refer_code)) {
            $error['refer_code'] = " <span class='label label-danger'>Required!</span>";
        }
        if (empty($email)) {
            $error['email'] = " <span class='label label-danger'>Required!</span>";
        }
       
       
       if (!empty($name) && !empty($role) && !empty($email) && !empty($password) && !empty($refer_code)) 
       {
        $sql_query = "SELECT * FROM admin WHERE email = '$email'";
        $db->sql($sql_query);
        $res = $db->getResult();
        $num = $db->numRows($res);
        if ($num >= 1) {
            $error['add_admin'] = " <span class='label label-danger'>Admin Already Added</span>";
            
        }else{
            $sql_query = "INSERT INTO admin (name,role,email,password,refer_code,status)VALUES('$name','$role','$email','$password','$refer_code',1)";
            $db->sql($sql_query);
            $result = $db->getResult();
            if (!empty($result)) {
                $result = 0;
            } else {
                $result = 1;
            }

            if ($result == 1) {
                echo $num;
                
                $error['add_admin'] = "<section class='content-header'>
                                                <span class='label label-success'>Admin Added Successfully</span> </section>";
            } else {
                $error['add_admin'] = " <span class='label label-danger'>Failed</span>";
            }
        }
            
        }
           

    }
?>
<section class="content-header">
    <h1>Add New admin <small><a href='admins.php'> <i class='fa fa-angle-double-left'></i>&nbsp;&nbsp;&nbsp;Back to Admins</a></small></h1>

    <?php echo isset($error['add_admin']) ? $error['add_admin'] : ''; ?>
    <ol class="breadcrumb">
        <li><a href="home.php"><i class="fa fa-home"></i> Home</a></li>
    </ol>
    <hr />
</section>
<section class="content">
    <div class="row">
        <div class="col-md-10">
           
            <!-- general form elements -->
            <div class="box box-primary">
                <div class="box-header with-border">

                </div>
                <!-- /.box-header -->
                <!-- form start -->
                <form name="add_admin_form" method="post" enctype="multipart/form-data">
                    <div class="box-body">
                    <div class="row">
                            <div class="form-group">
                                <div class='col-md-6'>
                                    <label for="role">Role</label> <i class="text-danger asterik">*</i><?php echo isset($error['role']) ? $error['role'] : ''; ?>
                                    <select  name="role" id="role" class="form-control" required>
                                            <option value="">Select role</option>
                                            <option value="Admin">Admin</option>
                                            <option value="Super Admin">Super Admin</option>
=                                    </select>
                                </div>
                                <div class='col-md-6'>
                                    <label for="exampleInputEmail1">Name</label> <i class="text-danger asterik">*</i><?php echo isset($error['name']) ? $error['name'] : ''; ?>
                                    <input type="text" class="form-control" name="name" required>
                                </div>
                            </div>
                            
                        </div>
                        <br>
                        <div class="row">
                            <div class="form-group">
                                <div class='col-md-6'>
                                    <label for="exampleInputEmail1">Email</label> <i class="text-danger asterik">*</i><?php echo isset($error['email']) ? $error['email'] : ''; ?>
                                    <input type="email" class="form-control" name="email" required>
                                </div>
                                <div class="col-md-6">
                                    <label for="exampleInputEmail1">Password</label><i class="text-danger asterik">*</i><?php echo isset($error['password']) ? $error['password'] : ''; ?>
                                    <input type="text" class="form-control" name="password" required>
                                </div>
                            </div>
                        </div>
                        <br>
                        <div class="row">
                            <div class="form-group">
                            <div class="col-md-6">
                                    <label for="exampleInputEmail1">Referral Code</label><i class="text-danger asterik">*</i><?php echo isset($error['refer_code']) ? $error['refer_code'] : ''; ?>
                                    <input type="text" class="form-control" name="refer_code" required >
                                </div>
                            </div>
                        </div>
                    </div>
                  
                    <!-- /.box-body -->

                    <div class="box-footer">
                        <button type="submit" class="btn btn-primary" name="btnAdd">Submit</button>
                        <input type="reset" onClick="refreshPage()" class="btn-warning btn" value="Clear" />
                    </div>

                </form>

            </div><!-- /.box -->
        </div>
    </div>
</section>

<div class="separator"> </div>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.17.0/jquery.validate.min.js"></script>
<script>
    $('#add_admin_form').validate({

        ignore: [],
        debug: false,
        rules: {
            name: "required",
            role: "required",
            password: "required",
        }
    });
    $('#btnClear').on('click', function() {
        for (instance in CKEDITOR.instances) {
            CKEDITOR.instances[instance].setData('');
        }
    });
</script>

<!--code for page clear-->
<script>
    function refreshPage(){
    window.location.reload();
} 
</script>

<?php $db->disconnect(); ?>