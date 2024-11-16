
<section class="content-header">
    <h1>
    Transaction Reports</h1>
    <small><?php echo isset($error['update_users']) ? $error['update_users'] : ''; ?></small>
    <ol class="breadcrumb">
        <li><a href="reports.php"><i class="fa fa-home"></i> Home</a></li>
    </ol>
</section>
<section class="content">
    <div class="row">
        <div class="col-md-10">
            <div class="box box-primary">
                <!-- /.box-header -->
                <!-- form start -->
                <form name="add_admin_form" method="post" enctype="multipart/form-data">
                    <div class="box-body">
                        <div class="row">
                            <div class="form-group">
                                <div class='col-md-4'>
                                    <label for="mobile_number">Mobile Number</label> <i class="text-danger asterik">*</i>
                                   <input type="text" id='mobile_number' name="mobile" class='form-control' required>
                                </div>
                                <input style="margin-top:22px;margin-left:22px;" type="submit" class="btn-primary btn" value="Search" name="btnSearch" />&nbsp;
                            </div>
                        </div>
                    
                </form>
                
                    <?php if(isset($_POST['btnSearch'])){ 
                            $mobile = $db->escapeString($fn->xss_clean($_POST['mobile']));
                            // $name = $db->escapeString($fn->xss_clean($_POST['name']));
                            // $refer_code = $db->escapeString($fn->xss_clean($_POST['name']));
                            $sql_query = "SELECT id,name,mobile,refer_code FROM users WHERE mobile = '$mobile'";
                            $db->sql($sql_query);
                            $ressus = $db->getResult();
                            if(count($ressus)>0){
                            ?>
                            <form id='add_suspense_form' method="post" enctype="multipart/form-data">
                            <input type="hidden" name="type" value="<?php echo $type?>">
                            <input type="hidden" name="user_id" value="<?php echo $ressus[0]['id']?>">
                            <!-- <input  type="hidden" name="name" value="<?php echo $ressus[0]['name']?>"> -->
                            <input  type="hidden" name="mobile" value="<?php echo $ressus[0]['mobile']?>">
                            <!-- <input  type="hidden" name="refer_code" value="<?php echo $ressus[0]['refer_code']?>"> -->

                            <div class="row">
                                <div class="form-group">
                                        <div class='col-md-6'>
                                            <label for="mobile_number">User Details</label> <i class="text-danger asterik">*</i>
                                            <input type="text" id='name' name="name" value="<?php echo $ressus[0]['name'] .' - '. $ressus[0]['refer_code'] .' - '. $ressus[0]['mobile'] ?>"  class='form-control' readonly>
                                        </div>
                                </div>
                            </div>
                            </form>


                            <br>
                            <form action="export-transaction.php?id=<?php echo $ressus[0]['id'] ?>" method="post" enctype="multipart/form-data">
                                <button type='submit'  class="btn btn-primary"><i class="fa fa-download"></i> Export Transactions</button>
                            </form>
                    <?php }
                    else{ echo '<div class="text-danger">Mobile number not found in the system</div>'; } ?>
                    <?php } ?>
                




            </div><!-- /.box -->
        </div>
    </div>
</section>

<div class="separator"> </div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.17.0/jquery.validate.min.js"></script>
<script>
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