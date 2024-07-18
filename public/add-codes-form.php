<?php
include_once('includes/functions.php');
$function = new functions;
include_once('includes/custom-functions.php');
$fn = new custom_functions;

$sql = "SELECT id, name FROM categories ORDER BY id ASC";
$db->sql($sql);
$res = $db->getResult();
date_default_timezone_set('Asia/Kolkata');
?>
<?php
 $ID = $db->escapeString($_GET['id']);
if (isset($_POST['btnAdd'])) {
        $codes = $db->escapeString(($_POST['codes']));
        $error = array();
       
        if (empty($codes)) {
            $error['codes'] = " <span class='label label-danger'>Required!</span>";
        }
       
            if (!empty($codes)) 
            {
                $datetime = date('Y-m-d H:i:s');
                $type = 'code_bonus';
                $per_code_cost = $fn->get_code_per_cost($ID);
                $amount = $codes * $per_code_cost;
                $sql = "INSERT INTO transactions (`user_id`,`codes`,`amount`,`datetime`,`type`)VALUES('$ID','$codes','$amount','$datetime','$type')";
                $db->sql($sql);
                $res = $db->getResult();
            
                $sql = "UPDATE `users` SET  `today_codes` = today_codes + $codes,`total_codes` = total_codes + $codes,`earn` = earn + $amount,`balance` = balance + $amount WHERE `id` = $ID";
                $db->sql($sql);
                 $result = $db->getResult();
                 if (!empty($result)) {
                     $result = 0;
                 } else {
                     $result = 1;
                 }
     
                 if ($result == 1) {
                     $error['add_codes'] = "<section class='content-header'>
                                                     <span class='label label-success'>Codes Added Successfully</span> </section>";
                 }
                 }

        }
?>
<section class="content-header">
    <h1>Add Codes <small><a href='users.php'> <i class='fa fa-angle-double-left'></i>&nbsp;&nbsp;&nbsp;Back to Users</a></small></h1>
    <?php echo isset($error['add_codes']) ? $error['add_codes'] : ''; ?>
    <ol class="breadcrumb">
        <li><a href="home.php"><i class="fa fa-home"></i> Home</a></li>
    </ol>
</section>
<section class="content">
    <div class="row">
        <div class="col-md-6">
           
            <!-- general form elements -->
            <div class="box box-primary">
                <div class="box-header with-border">

                </div>
                <!-- /.box-header -->
                <!-- form start -->
                <form name="add_codes_form" method="post" enctype="multipart/form-data">
                    <div class="box-body">
                        <div class="row">
                            <div class="form-group">
                                <div class='col-md-8'>
                                    <label for="exampleInputEmail1">Codes</label> <i class="text-danger asterik">*</i><?php echo isset($error['codes']) ? $error['codes'] : ''; ?>
                                    <input type="number" class="form-control" name="codes" required>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- /.box-body -->

                    <div class="box-footer">
                        <button type="submit" class="btn btn-primary" name="btnAdd">Add</button>
                    </div>

                </form>

            </div><!-- /.box -->
        </div>
    </div>
</section>

<div class="separator"> </div>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.17.0/jquery.validate.min.js"></script>

<?php $db->disconnect(); ?>