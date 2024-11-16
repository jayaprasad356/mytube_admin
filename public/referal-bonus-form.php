<?php
include_once('includes/functions.php');
$function = new functions;
include_once('includes/custom-functions.php');
$fn = new custom_functions;

if (isset($_POST['btnAdd'])) {
    $user_id = $db->escapeString(($_POST['user_id']));
    $error = array();

    if (!empty($user_id)) {
        $code_bonus = 1000 * COST_PER_CODE;
        $referral_bonus = REFER_BONUS;
        $datetime = date('Y-m-d H:i:s');
        $sql_query = "UPDATE users SET `total_referrals` = total_referrals + 1,`earn` = earn + $referral_bonus,`balance` = balance + $referral_bonus WHERE id =  '$user_id' AND status = 1";
        $db->sql($sql_query);
        $res = $db->getResult();
        if (empty($res)) {
            $sql = "SELECT * FROM `users` WHERE id =  '$user_id'";
            $db->sql($sql);
            $ures = $db->getResult();

            $sql_query = "INSERT INTO transactions (user_id,amount,datetime,type)VALUES($user_id,$referral_bonus,'$datetime','refer_bonus')";
            $db->sql($sql_query);
            $code_generate = $ures[0]['code_generate'];
            if($code_generate == 1){
                $sql_query = "UPDATE users SET `earn` = earn + $code_bonus,`balance` = balance + $code_bonus,`today_codes` = today_codes + 1000,`total_codes` = total_codes + 1000 WHERE id =  '$user_id' AND code_generate = 1";
                $db->sql($sql_query);
                $sql_query = "INSERT INTO transactions (user_id,amount,codes,datetime,type)VALUES($user_id,$code_bonus,1000,'$datetime','code_bonus')";
                $db->sql($sql_query);
            }


        }
        $error['update_users'] = " <section class='content-header'><span class='label label-success'>Refer code updated Successfully</span></section>";
    }




}


?>
<section class="content-header">
    <h1>
    Referal Bonus</h1>
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
                                        <select id='user_id' name="user_id" class='form-control' required>
                                            <option value="">select</option>
                                                <?php
                                                if($_SESSION['role'] == 'Super Admin'){
                                                    $join = "WHERE id IS NOT NULL";
                                                }
                                                else{
                                                    $refer_code = $_SESSION['refer_code'];
                                                    $join = "WHERE refer_code REGEXP '^$refer_code'";
                                                }
                                                $sql = "SELECT id,mobile,name,refer_code FROM `users` $join ORDER BY ID DESC ";
                                                $db->sql($sql);
                                                $result = $db->getResult();
                                                foreach ($result as $value) {
                                                ?>
                                                    <option value='<?= $value['id'] ?>'><?= $value['name'] .' - '. $value['mobile'].' - '. $value['refer_code']?></option>
                                            <?php } ?>
                                        </select>
                                </div>
                            </div>
                        </div>
                        <br>
                        <div class="row">
                            <div class="form-group">
                                <div class='col-md-6'>
                                    <label for="exampleInputEmail1">Total Referal Count</label> <i class="text-danger asterik">*</i>
                                    <input type="text" class="form-control" id="referal_count" name="referred_by" value="1"  readonly>
                                </div>
                            </div>
                            
                        </div>
                        <br>
                        <div class="row">
                            <div class="form-group">
                                <div class="col-md-2">
                                   <input type="submit" class="btn-primary btn" value="Send Referral Bonus" name="btnAdd" />&nbsp;
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
        $('#user_id').select2({
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