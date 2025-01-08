<?php
include_once('includes/functions.php');
$function = new functions;
include_once('includes/custom-functions.php');
$fn = new custom_functions;

?>
<?php
if (isset($_POST['btnAdd'])) {

        $name = $db->escapeString(($_POST['name']));
        $mobile = $db->escapeString(($_POST['mobile']));
        $password = $db->escapeString(($_POST['password']));
        $dob = $db->escapeString(($_POST['dob']));
        $email = $db->escapeString(($_POST['email']));
        $city = $db->escapeString(($_POST['city']));
        $referred_by = (isset($_POST['referred_by']) && !empty($_POST['referred_by'])) ? $db->escapeString($_POST['referred_by']) : "";
        $error = array();
       
        if (empty($name)) {
            $error['name'] = " <span class='label label-danger'>Required!</span>";
        }
        if (empty($mobile)) {
            $error['mobile'] = " <span class='label label-danger'>Required!</span>";
        }
        if (empty($password)) {
            $error['password'] = " <span class='label label-danger'>Required!</span>";
        }
        if (empty($dob)) {
            $error['dob'] = " <span class='label label-danger'>Required!</span>";
        }
        if (empty($email)) {
            $error['email'] = " <span class='label label-danger'>Required!</span>";
        }
        if (empty($city)) {
            $error['city'] = " <span class='label label-danger'>Required!</span>";
        }
       
       
       if (!empty($name) && !empty($email) && !empty($mobile) && !empty($password) && !empty($city)  && !empty($dob)) 
       {
        do {
            $random_number = mt_rand(10000,99999);
            $sql = "SELECT * FROM users WHERE refer_code = $random_number";
            $db->sql($sql);
            $res = $db->getResult();
            if(!$res) {
                break;
            }
        } while(1);
        if(empty($referred_by)){
            $refer_code = MAIN_REFER . $random_number;
    
        }
        else{
            $refer_code = substr($referred_by, 0, -5) . $random_number;
    
        }
        $sql = "SELECT * FROM users WHERE mobile='$mobile'";
        $db->sql($sql);
        $res = $db->getResult();
        $num = $db->numRows($res);
        if ($num >= 1) {
            $error['add_user'] = " <span class='label label-danger'>Mobile Number Already Exists</span>";
        }
        else{
            $joined_date = date('Y-m-d H:i:s');
            $sql_query = "INSERT INTO users (name,mobile,email,password,dob,city,referred_by,refer_code,joined_date)VALUES('$name','$mobile','$email','$password','$dob','$city','$referred_by','$refer_code','$joined_date')";
            $db->sql($sql_query);
            $result = $db->getResult();
            if (!empty($result)) {
                $result = 0;
            } else {
                $result = 1;
            }
    
            if ($result == 1) {
                
                $error['add_user'] = "<section class='content-header'>
                                                <span class='label label-success'>User Added Successfully</span> </section>";
            } else {
                $error['add_user'] = " <span class='label label-danger'>Failed</span>";
            }
            }
        }
 }
?>
<section class="content-header">
    <h1>Add New User <small><a href='users.php'> <i class='fa fa-angle-double-left'></i>&nbsp;&nbsp;&nbsp;Back to Users</a></small></h1>

    <?php echo isset($error['add_user']) ? $error['add_user'] : ''; ?>
    <ol class="breadcrumb">
        <li><a href="reports.php"><i class="fa fa-home"></i> Home</a></li>
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
                <form name="add_user_form" method="post" enctype="multipart/form-data">
                    <div class="box-body">
                    <div class="row">
                            <div class="form-group">
                                <div class='col-md-6'>
                                    <label for="exampleInputEmail1">Name</label> <i class="text-danger asterik">*</i>
                                    <input type="text" class="form-control" name="name" required>
                                </div>

                            </div>
                            
                        </div>
                        <br>
                        <div class="row">
                            <div class="form-group">
                                <div class="col-md-6">
                                    <label for="exampleInputEmail1">Phone Number</label><i class="text-danger asterik">*</i>
                                    <input type="number" class="form-control" name="mobile" required>
                                </div>
                                <div class="col-md-6">
                                    <label for="exampleInputEmail1">Password</label><i class="text-danger asterik">*</i>
                                    <input type="text" class="form-control" name="password" required>
                                </div>
                            </div>
                        </div>
                        <br>
                        <div class="row">
                            <div class="form-group">
                                <div class="col-md-6">
                                    <label for="exampleInputEmail1">Date of Birth</label><i class="text-danger asterik">*</i>
                                    <input type="date" class="form-control" name="dob" required>
                                </div>
                                <div class="col-md-6">
                                    <label for="exampleInputEmail1">E-mail</label><i class="text-danger asterik">*</i>
                                    <input type="text" class="form-control" name="email" required>
                                </div>
                            </div>
                        </div>
                        <br>
                        <div class="row">
                            <div class="form-group">
                                <div class="col-md-6">
                                    <label for="exampleInputEmail1">City</label><i class="text-danger asterik">*</i>
                                    <input type="text" class="form-control" name="city" required>
                                </div>
                                <div class="col-md-6">
                                    <label for="exampleInputEmail1">Referred By</label><i class="text-danger asterik">*</i>
                                    <input type="text" class="form-control" name="referred_by" >
                                </div>
                            </div>
                        </div>
                        <br>

                        

         
                    </div>
                  
                    <!-- /.box-body -->

                    <div class="box-footer">
                        <button type="submit" class="btn btn-primary" name="btnAdd">Add</button>
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
    $('#add_product').validate({

        ignore: [],
        debug: false,
        rules: {
            product_name: "required",
            brand: "required",
            category_image: "required",
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