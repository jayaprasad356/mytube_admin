<?php
include_once('includes/functions.php');
$function = new functions;
include_once('includes/custom-functions.php');
$fn = new custom_functions;

if (isset($_POST['btnUpdate'])) {
    $mobile = $db->escapeString(($_POST['mobile']));
    $referred_by = $db->escapeString(($_POST['referred_by']));
    $datetime = date('Y-m-d H:i:s');
    $error = array();

    if (!empty($mobile) && !empty($referred_by)) {

    $sql_query = "UPDATE users SET referred_by='$referred_by' WHERE id =  $mobile";
    $db->sql($sql_query);
    $update_result = $db->getResult();
    if (!empty($update_result)) {
        $code_bonus = 1000 * COST_PER_CODE;
        $referral_bonus = REFER_BONUS;
        $sql_query = "UPDATE users SET `total_referrals` = total_referrals + 1,`earn` = earn + $referral_bonus,`balance` = balance + $referral_bonus WHERE refer_code =  '$referred_by' AND status = 1";
        $db->sql($sql_query);
        $sql_query = "SELECT * FROM users WHERE refer_code =  '$referred_by'";
        $db->sql($sql_query);
        $res = $db->getResult();
        $user_id = $res[0]['id'];
        $num = $db->numRows($res);
        if ($num == 1){
            $sql_query = "INSERT INTO transactions (user_id,amount,datetime,type)VALUES($user_id,$referral_bonus,'$datetime','refer_bonus')";
            $db->sql($sql_query);
            $code_generate = $res[0]['code_generate'];
            if($code_generate == 1){
                $sql_query = "UPDATE users SET `earn` = earn + $code_bonus,`balance` = balance + $code_bonus,`today_codes` = today_codes + 1000,`total_codes` = total_codes + 1000 WHERE refer_code =  '$referred_by' AND code_generate = 1";
                $db->sql($sql_query);
                $sql_query = "INSERT INTO transactions (user_id,amount,codes,datetime,type)VALUES($user_id,$code_bonus,1000,'$datetime','code_bonus')";
                $db->sql($sql_query);
            }
            $sql_query = "UPDATE users SET refer_bonus_sent = 1 WHERE id =  $ID";
            $db->sql($sql_query);

        }

        $update_result = 0;
    } else {
        $update_result = 1;
    }

    // check update result
    if ($update_result == 1) {
        $error['update_users'] = " <section class='content-header'><span class='label label-success'>Refer code updated Successfully</span></section>";
    } else {
        $error['update_users'] = " <span class='label label-danger'>Failed update refer code</span>";
    }


    }
}


?>
<section class="content-header">
    <h1>
    Manage Referred By</h1>
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
                                <div class='col-md-6'>
                                       <label for="">Mobile Number</label> <i class="text-danger asterik">*</i>
                                        <select id='mobile' name="mobile" class='form-control' required>
                                            <option value="">select</option>
                                                <?php
                                                if($_SESSION['role'] == 'Super Admin'){
                                                    $join = "WHERE id IS NOT NULL";
                                                }
                                                else{
                                                    $refer_code = $_SESSION['refer_code'];
                                                    $join = "WHERE refer_code REGEXP '^$refer_code'";
                                                }
                                                $sql = "SELECT id,mobile,name FROM `users` $join ORDER BY ID DESC ";
                                                $db->sql($sql);
                                                $result = $db->getResult();
                                                foreach ($result as $value) {
                                                ?>
                                                    <option value='<?= $value['id'] ?>'><?= $value['name'] .' - '. $value['mobile']?></option>
                                            <?php } ?>
                                        </select>
                                </div>
                            </div>
                        </div>
                        <br>
                        <div class="row">
                            <div class="form-group">
                                <div class='col-md-6'>
                                    <label for="exampleInputEmail1">Referred By</label> <i class="text-danger asterik">*</i>
                                    <input type="text" class="form-control" id="referred_by" name="referred_by" value="" >
                                </div>
                            </div>
                            
                        </div>
                        <br>
                        <div class="row">
                            <div class="form-group">
                                <div class="col-md-2">
                                   <input type="submit" class="btn-primary btn" value="Update" name="btnUpdate" />&nbsp;
                               </div>
                            </div>

                        </div>
                </form>


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
<script>
    $(document).ready(function () {
        $('#mobile').select2({
        width: 'element',
        placeholder: 'Type in Mobile to search',

    });
    });

</script>
<script>
    $(document).on('change', '#mobile', function() {
        $.ajax({
            url: 'public/db-operation.php',
            method: 'POST',
            data: 'user_id=' + $('#mobile').val() + '&referred_by_code_change=1',
            success: function(data) {
                $('#referred_by').val(data);
            }
        });
    });
</script>

<?php $db->disconnect(); ?>