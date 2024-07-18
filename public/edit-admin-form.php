<?php
include_once('includes/functions.php');
$function = new functions;
include_once('includes/custom-functions.php');
$fn = new custom_functions;
?>
<?php

if (isset($_GET['id'])) {
    $ID = $db->escapeString($_GET['id']);
} else {
    // $ID = "";
    return false;
    exit(0);
}
if (isset($_POST['btnEdit'])) {

            $name = $db->escapeString(($_POST['name']));
            $role = $db->escapeString(($_POST['role']));
            $password = $db->escapeString(($_POST['password']));
            $email = $db->escapeString(($_POST['email']));
            $refer_code = $db->escapeString(($_POST['refer_code']));
            $status = $db->escapeString(($_POST['status']));
            $error = array();

     if (!empty($name) && !empty($password)&& !empty($role) && !empty($email)&& !empty($refer_code)) 
		{

        $sql_query = "UPDATE admin SET name='$name', password='$password', role='$role', email='$email', refer_code='$refer_code',status='$status' WHERE id =  $ID";
        $db->sql($sql_query);
        $update_result = $db->getResult();
        if (!empty($update_result)) {
            $update_result = 0;
        } else {
            $update_result = 1;
        }

        // check update result
        if ($update_result == 1) {
            $error['update_admin'] = " <section class='content-header'><span class='label label-success'>Admin updated Successfully</span></section>";
        } else {
            $error['update_admin'] = " <span class='label label-danger'>Failed update</span>";
        }
    }
}


// create array variable to store previous data
$data = array();

$sql_query = "SELECT * FROM admin WHERE id =" . $ID;
$db->sql($sql_query);
$res = $db->getResult();

if (isset($_POST['btnCancel'])) { ?>
    <script>
        window.location.href = "admins.php";
    </script>
<?php } ?>
<section class="content-header">
    <h1>
        Edit Admin<small><a href='admins.php'><i class='fa fa-angle-double-left'></i>&nbsp;&nbsp;&nbsp;Back to Admin</a></small></h1>
    <small><?php echo isset($error['update_admin']) ? $error['update_admin'] : ''; ?></small>
    <ol class="breadcrumb">
        <li><a href="home.php"><i class="fa fa-home"></i> Home</a></li>
    </ol>
</section>
<section class="content">
    <!-- Main row -->

    <div class="row">
        <div class="col-md-10">

            <!-- general form elements -->
            <div class="box box-primary">
                <div class="box-header with-border">
                </div>
                <div class="box-header">
                    <?php echo isset($error['cancelable']) ? '<span class="label label-danger">Till status is required.</span>' : ''; ?>
                </div>

                <!-- /.box-header -->
                <!-- form start -->
                <form id="edit_user_form" method="post" enctype="multipart/form-data">
                    <div class="box-body">
                        <div class="row">
                            <div class="form-group">
                                <div class='col-md-6'>
                                    <label for="exampleInputEmail1">Role</label> <i class="text-danger asterik">*</i>
                                    <select id="role" name="role" class="form-control">
                                        <option value="">Select</option>
                                        <option value="Admin"<?=$res[0]['role'] == 'Admin' ? ' selected="selected"' : '';?>>Admin</option>
                                        <option value="Super Admin"<?=$res[0]['role'] == 'Super Admin' ? ' selected="selected"' : '';?> >Super Admin</option>
                                    </select>
                                </div>
                                <div class='col-md-6'>
                                    <label for="exampleInputEmail1">Name</label> <i class="text-danger asterik">*</i>
                                    <input type="text" class="form-control" name="name" value="<?php echo $res[0]['name']; ?>">
                                </div>
                            </div>
                            
                        </div>
                        <br>
                        <div class="row">
                            <div class="form-group">
                               <div class="col-md-6">
                                    <label for="exampleInputEmail1">Email</label><i class="text-danger asterik">*</i>
                                    <input type="email" class="form-control" name="email" value="<?php echo $res[0]['email']; ?>">
                                </div>
                                <div class="col-md-6">
                                    <label for="exampleInputEmail1">Password</label><i class="text-danger asterik">*</i>
                                    <input type="text" class="form-control" name="password" value="<?php echo $res[0]['password']; ?>">
                                </div>
                            </div>
                        </div>
                        <br>
                        <div class="row">
                            <div class="form-group">
                                <div class="col-md-6">
                                    <label for="exampleInputEmail1">Refer Code</label><i class="text-danger asterik">*</i>
                                    <input type="text" class="form-control" name="refer_code" value="<?php echo $res[0]['refer_code']; ?>">
                                </div>
                                <div class="col-md-6">
                                    <label class="control-label">Status</label> <i class="text-danger asterik">*</i><br>
                                    <div id="status" class="btn-group">
                                        <label class="btn btn-danger" data-toggle-class="btn-default" data-toggle-passive-class="btn-default">
                                            <input type="radio" name="status" value="0" <?= ($res[0]['status'] == 0) ? 'checked' : ''; ?>> Deactive
                                        </label>
                                        <label class="btn btn-success" data-toggle-class="btn-primary" data-toggle-passive-class="btn-default">
                                            <input type="radio" name="status" value="1" <?= ($res[0]['status'] == 1) ? 'checked' : ''; ?>> Active
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div><!-- /.box-body -->

                    <div class="box-footer">
                        <button type="submit" class="btn btn-primary" name="btnEdit">Update</button>

                    </div>
                </form>
            </div><!-- /.box -->
        </div>
    </div>
</section>

<div class="separator"> </div>
<?php $db->disconnect(); ?>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.17.0/jquery.validate.min.js"></script>