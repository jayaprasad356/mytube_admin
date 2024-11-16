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
           
            $date = $db->escapeString(($_POST['date']));
            $reason = $db->escapeString(($_POST['reason']));
            $error = array();

     if (!empty($date) && !empty($reason)) {
    
            $sql_query = "UPDATE leaves SET date='$date', reason='$reason' WHERE id =  $ID";
            $db->sql($sql_query);
        }
        $update_result = $db->getResult();
        if (!empty($update_result)) {
            $update_result = 0;
        } else {
            $update_result = 1;
        }

        // check update result
        if ($update_result == 1) {
            $error['update_leave'] = " <section class='content-header'><span class='label label-success'>Leave updated Successfully</span></section>";
        } else {
            $error['update_leave'] = " <span class='label label-danger'>Failed update Leave</span>";
        }


    }



// create array variable to store previous data
$data = array();

    $sql_query = "SELECT * FROM leaves WHERE id =" . $ID;
    $db->sql($sql_query);
    $res = $db->getResult();

if (isset($_POST['btnCancel'])) { ?>
    <script>
        window.location.href = "leaves.php";
    </script>
<?php } ?>
<section class="content-header">
    <h1>
        Edit Leave Details<small><a href='leaves.php'><i class='fa fa-angle-double-left'></i>&nbsp;&nbsp;&nbsp;Back to Leaves</a></small></h1>
    <small><?php echo isset($error['update_leave']) ? $error['update_leave'] : ''; ?></small>
    <ol class="breadcrumb">
        <li><a href="reports.php"><i class="fa fa-home"></i> Home</a></li>
    </ol>
</section>
<section class="content">
    <!-- Main row -->

    <div class="row">
        <div class="col-md-6">

            <!-- general form elements -->
            <div class="box box-primary">
                <div class="box-header with-border">
                </div>
                <form id="edit_user_form" method="post" enctype="multipart/form-data">
                    <div class="box-body">
                        <div class="form-group">
                                <label for="exampleInputEmail1">Date</label> <i class="text-danger asterik">*</i>
                                <input type="date" class="form-control" name="date" value="<?php echo $res[0]['date']; ?>">
                        </div>
                        <br>
                        <div class="form-group">
                                <label for="exampleInputEmail1">Reason</label><i class="text-danger asterik">*</i>
                                <textarea type="text" rows="3" class="form-control" name="reason"><?php echo $res[0]['reason']; ?></textarea>
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
<script>
    $(document).ready(function () {
        $('#user_id').select2({
        width: 'element',
        placeholder: 'Type in name to search',

    });
    });

    if ( window.history.replaceState ) {
  window.history.replaceState( null, null, window.location.href );
}

</script>
<?php $db->disconnect(); ?>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.17.0/jquery.validate.min.js"></script>
