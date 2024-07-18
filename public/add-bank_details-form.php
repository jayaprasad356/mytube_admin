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
        $account_num = $db->escapeString(($_POST['account_num']));
        $holder_name = $db->escapeString(($_POST['holder_name']));
        $bank = $db->escapeString(($_POST['bank']));
        $branch = $db->escapeString(($_POST['branch']));
        $ifsc = $db->escapeString(($_POST['ifsc']));
        $error = array();
       
        if (empty($name)) {
            $error['name'] = " <span class='label label-danger'>Required!</span>";
        }
        if (empty($account_num)) {
            $error['account_num'] = " <span class='label label-danger'>Required!</span>";
        }
        if (empty($holder_name)) {
            $error['holder_name'] = " <span class='label label-danger'>Required!</span>";
        }
        if (empty($bank)) {
            $error['bank'] = " <span class='label label-danger'>Required!</span>";
        }
        if (empty($branch)) {
            $error['branch'] = " <span class='label label-danger'>Required!</span>";
        }
        if (empty($ifsc)) {
            $error['ifsc'] = " <span class='label label-danger'>Required!</span>";
        }
       
       
        $sql_query = "SELECT * FROM bank_details";
        $db->sql($sql_query);
        $res = $db->getResult();
        if($_POST['name']==$res[0]['user_id']){
            $error['add_bank_details'] = " <span class='label label-danger'>Bank Details Already added for this user</span>";
        
        }
        else{
            if (!empty($name) && !empty($account_num) && !empty($holder_name) && !empty($bank) && !empty($branch) && !empty($ifsc)) 
            {
                
                 $sql_query = "INSERT INTO bank_details (user_id,account_num,holder_name,bank,branch,ifsc)VALUES('$name','$account_num','$holder_name','$bank','$branch','$ifsc')";
                 $db->sql($sql_query);
                 $result = $db->getResult();
                 if (!empty($result)) {
                     $result = 0;
                 } else {
                     $result = 1;
                 }
     
                 if ($result == 1) {
                     
                     $error['add_bank_details'] = "<section class='content-header'>
                                                     <span class='label label-success'>Bank Details Added Successfully</span> </section>";
                 } else {
                     $error['add_bank_details'] = " <span class='label label-danger'>Failed</span>";
                 }
                 }

        }

        }
?>
<section class="content-header">
    <h1>Add Bank Details <small><a href='bank_details.php'> <i class='fa fa-angle-double-left'></i>&nbsp;&nbsp;&nbsp;Back to Bank Details</a></small></h1>

    <?php echo isset($error['add_bank_details']) ? $error['add_bank_details'] : ''; ?>
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
                <form name="add_bank_details_form" method="post" enctype="multipart/form-data">
                    <div class="box-body">
                        <div class="row">
                            <div class="form-group">
                                <div class='col-md-6'>
                                       <label for="">Name</label> <i class="text-danger asterik">*</i>
                                        <select id='name' name="name" class='form-control' required>
                                            <option value="">select</option>
                                                <?php
                                                $sql = "SELECT id,name FROM `users`";
                                                $db->sql($sql);
                                                $result = $db->getResult();
                                                foreach ($result as $value) {
                                                ?>
                                                    <option value='<?= $value['id'] ?>'><?= $value['name'] ?></option>
                                            <?php } ?>
                                        </select>
                                </div>
                                <div class='col-md-6'>
                                    <label for="exampleInputEmail1">Account Number</label> <i class="text-danger asterik">*</i><?php echo isset($error['account_num']) ? $error['account_num'] : ''; ?>
                                    <input type="number" class="form-control" name="account_num" required>
                                </div>
                            </div>
                            
                        </div>
                        <br>
                        <div class="row">
                            <div class="form-group">
                                <div class="col-md-6">
                                    <label for="exampleInputEmail1">Holder Name</label><i class="text-danger asterik">*</i><?php echo isset($error['holder_name']) ? $error['holder_name'] : ''; ?>
                                    <input type="text" class="form-control" name="holder_name" required>
                                </div>
                                <div class="col-md-6">
                                    <label for="exampleInputEmail1">IFSC</label><i class="text-danger asterik">*</i><?php echo isset($error['ifsc']) ? $error['ifsc'] : ''; ?>
                                    <input type="text" class="form-control" name="ifsc" required>
                                </div>
                            </div>
                        </div>
                        <br>
                        <div class="row">
                            <div class="form-group">
                                <div class="col-md-6">
                                    <label for="exampleInputEmail1">Bank</label><i class="text-danger asterik">*</i><?php echo isset($error['bank']) ? $error['bank'] : ''; ?>
                                    <input type="text" class="form-control" name="bank" required>
                                </div>
                                <div class="col-md-6">
                                    <label for="exampleInputEmail1">Branch</label><i class="text-danger asterik">*</i><?php echo isset($error['branch']) ? $error['branch'] : ''; ?>
                                    <input type="text" class="form-control" name="branch" required>
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
    $('#add_bank_details_form').validate({

        ignore: [],
        debug: false,
        rules: {
            name: "required",
            account_num: "required",
            holder_name: "required",
            bank: "required",
            branch: "required",
            ifsc: "required",

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
        $('#name').select2({
        width: 'element',
        placeholder: 'Type in name to search',

    });
    });

</script>
<!--code for page clear-->
<script>
    function refreshPage(){
    window.location.reload();
} 
</script>

<?php $db->disconnect(); ?>