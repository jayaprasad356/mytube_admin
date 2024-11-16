<?php
include_once('includes/functions.php');
$function = new functions;
include_once('includes/custom-functions.php');
$fn = new custom_functions;

?>
<?php

if (isset($_POST['btnAdd'])) {

        $date = $db->escapeString(($_POST['date']));
        $reason = $db->escapeString(($_POST['reason']));
        $error = array();
       
        if (empty($date)) {
            $error['date'] = " <span class='label label-danger'>Required!</span>";
        }
        if (empty($reason)) {
            $error['reason'] = " <span class='label label-danger'>Required!</span>";
        }
       
        if (!empty($date) && !empty($reason)) {
            $sql_query = "INSERT INTO leaves (date, reason) VALUES ('$date', '$reason')";
            $db->sql($sql_query);
        }
            $result = $db->getResult();
            if (!empty($result)) {
                $result = 0;
            } else {
                $result = 1;
            }
            if ($result == 1) {
                
                $error['add_leave'] = "<section class='content-header'>
                                                <span class='label label-success'>Leave Added Successfully</span> </section>";
            } else {
                $error['add_leave'] = " <span class='label label-danger'>Failed</span>";
           }
        }
        
        
?>
<section class="content-header">
    <h1>Add New Leave <small><a href='leaves.php'> <i class='fa fa-angle-double-left'></i>&nbsp;&nbsp;&nbsp;Back to Leaves</a></small></h1>

    <?php echo isset($error['add_leave']) ? $error['add_leave'] : ''; ?>
    <ol class="breadcrumb">
        <li><a href="reports.php"><i class="fa fa-home"></i> Home</a></li>
    </ol>
    <hr />
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
                <form url="add_leave_form" method="post" enctype="multipart/form-data">
                    <input type="hidden" class="form-control" name="user_id" value = "<?php echo $user_id?>">
                    <div class="box-body">
                       <div class="row">
                            <div class="form-group">
                                <div class='col-md-12'>
                                    <label for="exampleInputEmail1">Date</label> <i class="text-danger asterik">*</i>
                                    <input type="date" class="form-control" name="date" required>
                                </div>

                            </div>
                        </div>
                        <br>
                        <div class="row">
                            <div class="form-group">
                                <div class='col-md-12'>
                                    <label for="exampleInputEmail1">Reason</label> <i class="text-danger asterik">*</i>
                                    <textarea type="text" rows="3" class="form-control" name="reason" required></textarea>
                                </div>

                            </div>
                        </div>
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
    $('#add_leave_form').validate({

        ignore: [],
        debug: false,
        rules: {
        reason: "required",
            date: "required",
        }
    });
    $('#btnClear').on('click', function() {
        for (instance in CKEDITOR.instances) {
            CKEDITOR.instances[instance].setData('');
        }
    });
</script>
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

<!--code for page clear-->
<script>
    function refreshPage(){
    window.location.reload();
} 
</script>

<?php $db->disconnect(); ?>