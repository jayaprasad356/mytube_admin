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

    $error = array();
    $name = $db->escapeString($_POST['name']);
    $short_code = $db->escapeString($_POST['short_code']);
    $mobile = $db->escapeString(($_POST['mobile']));




    if (!empty($name) && !empty($short_code)&& !empty($mobile)) 
		{

        $sql_query = "UPDATE branches SET name='$name',short_code='$short_code',mobile='$mobile' WHERE id =  $ID";
        $db->sql($sql_query);
        $update_result = $db->getResult();
        if (!empty($update_result)) {
            $update_result = 0;
        } else {
            $update_result = 1;
        }

        // check update result
        if ($update_result == 1) {
            $error['update_banch'] = " <section class='content-header'><span class='label label-success'>Branch updated Successfully</span></section>";
        } else {
            $error['update_banch'] = " <span class='label label-danger'>Failed update</span>";
        }
    }
}

// create array variable to store previous data
$data = array();

$sql_query = "SELECT * FROM branches WHERE id =" . $ID;
$db->sql($sql_query);
$res = $db->getResult();

if (isset($_POST['btnCancel'])) { ?>
    <script>
        window.location.href = "branches.php";
    </script>
<?php } ?>
<section class="content-header">
    <h1>
        Edit Branch<small><a href='branches.php'><i class='fa fa-angle-double-left'></i>&nbsp;&nbsp;&nbsp;Back to Branches</a></small></h1>
    <small><?php echo isset($error['update_banch']) ? $error['update_banch'] : ''; ?></small>
    <ol class="breadcrumb">
        <li><a href="reports.php"><i class="fa fa-home"></i> Home</a></li>
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
                <form url="update_branches_form" method="post" enctype="multipart/form-data">
                    <div class="box-body">
                       <div class="row">
                            <div class="form-group">
                                <div class='col-md-10'>
                                    <label for="exampleInputEmail1">Name</label> <i class="text-danger asterik">*</i>
                                    <input type="text" class="form-control" name="name" value="<?php echo $res[0]['name']; ?>">
                                </div>
                            </div>
                        </div>
                        <br>
                        <div class="row">
                            <div class="form-group">
                                <div class='col-md-10'>
                                    <label for="exampleInputEmail1">Mobile</label> <i class="text-danger asterik">*</i>
                                    <input type="text" class="form-control" name="mobile" value="<?php echo $res[0]['mobile']; ?>">
                                </div> 
                            </div>
                        </div>
                        <br>
                        <div class="row">
                            <div class="form-group">
                                <div class='col-md-10'>
                                    <label for="exampleInputEmail1">Short Code</label> <i class="text-danger asterik">*</i>
                                    <input type="text" class="form-control" name="short_code" value="<?php echo $res[0]['short_code']; ?>">
                                </div> 
                            </div>
                        </div>
                        <br>
                    </div>
                  
                    <!-- /.box-body -->
                    <div class="box-footer">
                        <button type="submit" class="btn btn-primary" name="btnEdit">Update</button>
                    </div>

                </form>

            </div><!-- /.box -->
        </div>
    </div>
</section>
<div class="separator"> </div>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.17.0/jquery.validate.min.js"></script>

<?php $db->disconnect(); ?>
<script>
    var changeCheckbox = document.querySelector('#trial_earning_button');
    var init = new Switchery(changeCheckbox);
    changeCheckbox.onchange = function() {
        if ($(this).is(':checked')) {
            $('#trial_earnings').val(1);

        } else {
            $('#trial_earnings').val(0);
        }
    };
</script>