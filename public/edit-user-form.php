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
            $datetime = date('Y-m-d H:i:s');
            $date = date('Y-m-d');

            $name = $db->escapeString(($_POST['name']));
            $device_id = (isset($_POST['device_id']) && !empty($_POST['device_id'])) ? $db->escapeString($_POST['device_id']) : '';
            $mobile = $db->escapeString(($_POST['mobile']));
            $password = $db->escapeString(($_POST['password']));
            $dob = $db->escapeString(($_POST['dob']));
            $email = $db->escapeString(($_POST['email']));
            $city = $db->escapeString(($_POST['city']));
            $status = $db->escapeString(($_POST['status']));
            $refer_code = $db->escapeString(($_POST['refer_code']));
            $security = $db->escapeString(($_POST['security']));
            $joined_date = (isset($_POST['joined_date']) && !empty($_POST['joined_date'])) ? $db->escapeString($_POST['joined_date']) : $date;
            $code_generate_time = $db->escapeString(($_POST['code_generate_time']));
            $withdrawal_status = $db->escapeString(($_POST['withdrawal_status']));
            $refer_bonus_sent = (isset($_POST['refer_bonus_sent']) && !empty($_POST['refer_bonus_sent'])) ? $db->escapeString($_POST['refer_bonus_sent']) : 0;
            $register_bonus_sent = (isset($_POST['register_bonus_sent']) && !empty($_POST['register_bonus_sent'])) ? $db->escapeString($_POST['register_bonus_sent']) : 0;
            $referred_by = (isset($_POST['referred_by']) && !empty($_POST['referred_by'])) ? $db->escapeString($_POST['referred_by']) : "";
            $earn = (isset($_POST['earn']) && !empty($_POST['earn'])) ? $db->escapeString($_POST['earn']) : 0;
            $code_generate = (isset($_POST['code_generate']) && !empty($_POST['code_generate'])) ? $db->escapeString($_POST['code_generate']) : 0;
            $total_referrals = (isset($_POST['total_referrals']) && !empty($_POST['total_referrals'])) ? $db->escapeString($_POST['total_referrals']) : 0;
            $balance = (isset($_POST['balance']) && !empty($_POST['balance'])) ? $db->escapeString($_POST['balance']) : "0";
            $today_codes = (isset($_POST['today_codes']) && !empty($_POST['today_codes'])) ? $db->escapeString($_POST['today_codes']) : 0;
            $total_codes = (isset($_POST['total_codes']) && !empty($_POST['total_codes'])) ? $db->escapeString($_POST['total_codes']) : 0;

            $salary_advance_balance = $db->escapeString(($_POST['salary_advance_balance']));
            $task_type = $db->escapeString(($_POST['task_type']));
            $champion_task_eligible = $db->escapeString(($_POST['champion_task_eligible']));
            $mcg_timer = $db->escapeString(($_POST['mcg_timer']));
            $ad_status = $db->escapeString(($_POST['ad_status']));
            $l_referral_count = (isset($_POST['l_referral_count']) && !empty($_POST['l_referral_count'])) ? $db->escapeString($_POST['l_referral_count']) : 0;
            $per_code_cost = $db->escapeString(($_POST['per_code_cost']));
            //$level = $db->escapeString(($_POST['level']));
            $per_code_val = $db->escapeString(($_POST['per_code_val']));
            $support_id = $db->escapeString(($_POST['support_id']));
            $branch_id = $db->escapeString(($_POST['branch_id']));
            $worked_days = $db->escapeString(($_POST['worked_days']));
            $black_box = $db->escapeString(($_POST['black_box']));
            $error = array();
            
            if (empty($mobile)) {
                $error['mobile'] = " <span class='label label-danger'>Required!</span>";
            }
            if (empty($support_id)) {
                $error['update_users'] = " <span class='label label-danger'> Support Required!</span>";
            }
            if (empty($branch_id)) {
                $error['update_users'] = " <span class='label label-danger'> Branch Required!</span>";
            }

     if (!empty($name) && !empty($mobile) && !empty($password)&& !empty($dob) && !empty($email) && !empty($city) && !empty($code_generate_time) && !empty($support_id) && !empty($branch_id)) {
        $refer_bonus_sent = $fn->get_value('users','refer_bonus_sent',$ID);

        if($status == 1 && !empty($referred_by) && $refer_bonus_sent != 1){
            $refer_bonus_codes = $function->getSettingsVal('refer_bonus_codes');
            $code_bonus =  $refer_bonus_codes * COST_PER_CODE;
            $referral_bonus = 250;
            $sql_query = "SELECT * FROM users WHERE refer_code =  '$referred_by'";
            $db->sql($sql_query);
            $res = $db->getResult();
            $num = $db->numRows($res);
            if ($num == 1){
                $user_id = $res[0]['id'];
                $refer_code_generate = $res[0]['code_generate'];
                $ref_user_status = $res[0]['status'];
                if($ref_user_status == 1){
                    $referral_bonus = $function->getSettingsVal('refer_bonus_amount');

                }

                $sa_refer_count=$res[0]['sa_refer_count'];
                $refer_sa_balance=200;


                $sql_query = "UPDATE users SET `l_referral_count` = l_referral_count + 1,`earn` = earn + $referral_bonus,`balance` = balance + $referral_bonus,`salary_advance_balance`=salary_advance_balance +$refer_sa_balance,`sa_refer_count`=sa_refer_count + 1  WHERE id =  $user_id";
                $db->sql($sql_query);
                $fn->update_refer_code_cost($user_id);
                $sql_query = "INSERT INTO transactions (user_id,amount,datetime,type)VALUES($user_id,$referral_bonus,'$datetime','refer_bonus')";
                $db->sql($sql_query);
                $sql_query = "INSERT INTO salary_advance_trans (user_id,refer_user_id,amount,datetime,type)VALUES($ID,$user_id,'$refer_sa_balance','$datetime','credit')";
                $db->sql($sql_query);
                if($ref_user_status == 1 && $refer_code_generate == 1){
                    $ref_per_code_cost = $fn->get_code_per_cost($user_id);
                    $amount = $refer_bonus_codes  * $ref_per_code_cost;
                    $sql_query = "UPDATE users SET `earn` = earn + $amount,`balance` = balance + $amount,`today_codes` = today_codes + $refer_bonus_codes,`total_codes` = total_codes + $refer_bonus_codes WHERE refer_code =  '$referred_by' AND status = 1";
                    $db->sql($sql_query);
                    $sql_query = "INSERT INTO transactions (user_id,amount,codes,datetime,type)VALUES($user_id,$amount,$refer_bonus_codes,'$datetime','code_bonus')";
                    $db->sql($sql_query);
                }
                $sql_query = "UPDATE users SET refer_bonus_sent = 1 WHERE id =  $ID";
                $db->sql($sql_query);

            }


        }
        $fn->update_refer_code_cost($ID);
        $register_bonus_sent = $fn->get_value('users','register_bonus_sent',$ID);
        if($status == 1 && $register_bonus_sent != 1){
            $join_codes = 0;
            $register_bonus = $join_codes * COST_PER_CODE;
            $total_codes = $total_codes +  $join_codes;
            $today_codes = $today_codes +  $join_codes;
            $earn = $earn + $register_bonus;
            $balance = $balance + $register_bonus;
            $sql_query = "UPDATE users SET register_bonus_sent = 1 WHERE id =  $ID";
            $db->sql($sql_query);
            $sql_query = "INSERT INTO transactions (user_id,amount,codes,datetime,type)VALUES($ID,$register_bonus,0,'$datetime','register_bonus')";
            $db->sql($sql_query);
            
        }
    
        $sql_query = "UPDATE users SET name='$name', mobile='$mobile', password='$password', dob='$dob', email='$email', city='$city', refer_code='$refer_code', referred_by='$referred_by', earn='$earn', total_referrals='$total_referrals', balance='$balance', withdrawal_status=$withdrawal_status,total_codes=$total_codes, today_codes=$today_codes,device_id='$device_id',status = $status,code_generate = $code_generate,code_generate_time = $code_generate_time,joined_date = '$joined_date',task_type='$task_type',champion_task_eligible='$champion_task_eligible',mcg_timer='$mcg_timer',ad_status='$ad_status',security='$security',salary_advance_balance = $salary_advance_balance,l_referral_count=$l_referral_count,per_code_val=$per_code_val,per_code_cost=$per_code_cost,support_id='$support_id',branch_id='$branch_id',black_box='$black_box',worked_days='$worked_days'  WHERE id =  $ID";
        $db->sql($sql_query);
        $update_result = $db->getResult();
        if (!empty($update_result)) {
            $update_result = 0;
        } else {
            $update_result = 1;
        }

        // check update result
        if ($update_result == 1) {
            $error['update_users'] = " <section class='content-header'><span class='label label-success'>Users updated Successfully</span></section>";
        } else {
            $error['update_users'] = " <span class='label label-danger'>Failed update users</span>";
        }


    }
 
}


// create array variable to store previous data
$data = array();

$sql_query = "SELECT * FROM users WHERE id =" . $ID;
$db->sql($sql_query);
$res = $db->getResult();
if (isset($_POST['btnCancel'])) { ?>
    <script>
        window.location.href = "users.php";
    </script>
<?php } ?>
<section class="content-header">
    <h1>
        Edit User<small><a href='users.php'><i class='fa fa-angle-double-left'></i>&nbsp;&nbsp;&nbsp;Back to Users</a></small></h1>
    <small><?php echo isset($error['update_users']) ? $error['update_users'] : ''; ?></small>
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
                <div class="form-group col-md-3">
                                <h4 class="box-title"> </h4>
                                <a class="btn btn-block btn-primary" href="add-codes.php?id=<?php echo $ID ?>"><i class="fa fa-plus-square"></i> Add Codes</a>
                            </div>
                            <div class="form-group col-md-3">
                                <h4 class="box-title"> </h4>
                                <a class="btn btn-block btn-success" href="add-balance.php?id=<?php echo $ID ?>"><i class="fa fa-plus-square"></i>  Add Balance</a>
                            </div>
                </div>
                <div class="box-header">
                    <?php echo isset($error['cancelable']) ? '<span class="label label-danger">Till status is required.</span>' : ''; ?>
                </div>

                <!-- /.box-header -->
                <!-- form start -->
                <form id="edit_user_form" method="post" enctype="multipart/form-data">
                    <input type="hidden" class="form-control" name="refer_bonus_sent" value="<?php echo $res[0]['refer_bonus_sent']; ?>">
                    <input type="hidden" class="form-control" name="register_bonus_sent" value="<?php echo $res[0]['register_bonus_sent']; ?>">
                    <div class="box-body">
                        <div class="row">
                            <div class="form-group">
                                <div class='col-md-6'>
                                    <label for="exampleInputEmail1">Name</label> <i class="text-danger asterik">*</i>
                                    <input type="text" class="form-control" name="name" value="<?php echo $res[0]['name']; ?>">
                                </div>
                                <div class='col-md-6'>
                                    <label for="exampleInputEmail1">Device Id</label>
                                    <input type="text" class="form-control" name="device_id" value="<?php echo $res[0]['device_id']; ?>">
                                </div>
                            </div>
                            
                        </div>
                        <br>
                        <div class="row">
                            <div class="form-group">
                                <div class="col-md-6">
                                    <label for="exampleInputEmail1">Phone Number</label><i class="text-danger asterik">*</i><?php echo isset($error['mobile']) ? $error['mobile'] : ''; ?>
                                    <input type="number" class="form-control" name="mobile" value="<?php echo $res[0]['mobile']; ?>">
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
                                    <label for="exampleInputEmail1">Date of Birth</label><i class="text-danger asterik">*</i>
                                    <input type="date" class="form-control" name="dob" value="<?php echo $res[0]['dob']; ?>" required>
                                </div>
                                <div class="col-md-6">
                                    <label for="exampleInputEmail1">E-mail</label><i class="text-danger asterik">*</i>
                                    <input type="text" class="form-control" name="email" value="<?php echo $res[0]['email']; ?>" required>
                                </div>
                            </div>
                        </div>
                        <br>
                        <div class="row">
                            <div class="form-group">
                                <div class="col-md-3">
                                    <label for="exampleInputEmail1">City</label><i class="text-danger asterik">*</i>
                                    <input type="text" class="form-control" name="city" value="<?php echo $res[0]['city']; ?>">
                                </div>
                                <div class="col-md-3">
                                    <label for="exampleInputEmail1">Earn</label><i class="text-danger asterik">*</i>
                                    <input type="text" class="form-control" name="earn" value="<?php echo $res[0]['earn']; ?>">
                                </div>
                                <div class="col-md-3">
                                    <label for="exampleInputEmail1">Referred By</label>
                                    <input type="text" class="form-control" name="referred_by" value="<?php echo $res[0]['referred_by']; ?>">
                                </div>
                                <div class="col-md-3">
                                    <label for="exampleInputEmail1">Refer Code</label><i class="text-danger asterik">*</i>
                                    <input type="text" class="form-control" name="refer_code" value="<?php echo $res[0]['refer_code']; ?>">
                                </div>
                            </div>
                        </div>
                        <br>
                        <div class="row">
                            <div class="form-group">
                                <div class="col-md-3">
                                    <label for="exampleInputEmail1">Total Referrals</label><i class="text-danger asterik">*</i>
                                    <input type="text" class="form-control" name="total_referrals" value="<?php echo $res[0]['total_referrals']; ?>">
                                </div>
                                <div class="col-md-3">
                                    <label for="exampleInputEmail1">Balance</label><i class="text-danger asterik">*</i>
                                    <input type="text" class="form-control" name="balance" value="<?php echo $res[0]['balance']; ?>">
                                </div>
                                <div class="col-md-3">
                                    <label for="exampleInputEmail1">Today Codes</label><i class="text-danger asterik">*</i>
                                    <input type="text" class="form-control" name="today_codes" value="<?php echo $res[0]['today_codes']; ?>">
                                </div>
                                <div class="col-md-3">
                                    <label for="exampleInputEmail1">Total Codes</label><i class="text-danger asterik">*</i>
                                    <input type="text" class="form-control" name="total_codes" value="<?php echo $res[0]['total_codes']; ?>">
                                </div>
                            </div>
                        </div>
                        <br>
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="">Code Generate</label><br>
                                    <input type="checkbox" id="code_generate_button" class="js-switch" <?= isset($res[0]['code_generate']) && $res[0]['code_generate'] == 1 ? 'checked' : '' ?>>
                                    <input type="hidden" id="code_generate_status" name="code_generate" value="<?= isset($res[0]['code_generate']) && $res[0]['code_generate'] == 1 ? 1 : 0 ?>">
                                </div>

                            </div>
                            <div class="col-md-3">
                                    <label for="exampleInputEmail1">Code Generate Time</label><i class="text-danger asterik">*</i>
                                    <input type="number" class="form-control" name="code_generate_time" value="<?php echo $res[0]['code_generate_time']; ?>">
                                </div>
                            <div class="col-md-3">
                                    <label for="exampleInputEmail1">Joined Date</label><i class="text-danger asterik">*</i>
                                    <input type="date" class="form-control" name="joined_date" value="<?php echo $res[0]['joined_date']; ?>">
                            </div>
                        </div>
                        <br>
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="">Withdrawal Status</label><br>
                                    <input type="checkbox" id="withdrawal_button" class="js-switch" <?= isset($res[0]['withdrawal_status']) && $res[0]['withdrawal_status'] == 1 ? 'checked' : '' ?>>
                                    <input type="hidden" id="withdrawal_status" name="withdrawal_status" value="<?= isset($res[0]['withdrawal_status']) && $res[0]['withdrawal_status'] == 1 ? 1 : 0 ?>">
                                </div>
                            </div>
                            <div class="form-group col-md-4">
                                    <label class="control-label">Task Type</label><i class="text-danger asterik">*</i><br>
                                    <div id="task_type" class="btn-group">
                                        <label class="btn btn-primary" data-toggle-class="btn-primary" data-toggle-passive-class="btn-default">
                                            <input type="radio" name="task_type" value="regular" <?= ($res[0]['task_type'] == 'regular') ? 'checked' : ''; ?>> Regular
                                        </label>
                                        <label class="btn btn-info" data-toggle-class="btn-default" data-toggle-passive-class="btn-default">
                                            <input type="radio" name="task_type" value="champion" <?= ($res[0]['task_type'] == 'champion') ? 'checked' : ''; ?>> Champion
                                        </label>
                                    </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="">Champion Task Eligible</label><br>
                                    <input type="checkbox" id="eligible_button" class="js-switch" <?= isset($res[0]['champion_task_eligible']) && $res[0]['champion_task_eligible'] == 1 ? 'checked' : '' ?>>
                                    <input type="hidden" id="champion_task_eligible" name="champion_task_eligible" value="<?= isset($res[0]['champion_task_eligible']) && $res[0]['champion_task_eligible'] == 1 ? 1 : 0 ?>">
                                </div>
                            </div>
                        </div>
                        <br>
                        <div class="row">
                                <div class="col-md-3">
                                    <label for="exampleInputEmail1">MCG Timer</label><i class="text-danger asterik">*</i>
                                    <input type="number" class="form-control" name="mcg_timer" value="<?php echo $res[0]['mcg_timer']; ?>">
                                </div>
                                <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="">Ad Status</label><br>
                                            <input type="checkbox" id="ad_button" class="js-switch" <?= isset($res[0]['ad_status']) && $res[0]['ad_status'] == 1 ? 'checked' : '' ?>>
                                            <input type="hidden" id="ad_status" name="ad_status" value="<?= isset($res[0]['ad_status']) && $res[0]['ad_status'] == 1 ? 1 : 0 ?>">
                                        </div>
                                 </div>
                                 <div class="col-md-4">
                                 <div class="form-group">
                                    <label for="">Black Box</label><br>
                                    <input type="checkbox" id="black_box_button" class="js-switch" <?= isset($res[0]['black_box']) && $res[0]['black_box'] == 1 ? 'checked' : '' ?>>
                                    <input type="hidden" id="black_box" name="black_box" value="<?= isset($res[0]['black_box']) && $res[0]['black_box'] == 1 ? 1 : 0 ?>">
                                </div>
                                 </div>
                        </div>
                        <br>
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="">Security</label><br>
                                    <input type="checkbox" id="security_button" class="js-switch" <?= isset($res[0]['security']) && $res[0]['security'] == 1 ? 'checked' : '' ?>>
                                    <input type="hidden" id="security" name="security" value="<?= isset($res[0]['security']) && $res[0]['security'] == 1 ? 1 : 0 ?>">
                                </div>      
                            </div>
                            <div class="col-md-3">
                                <label for="exampleInputEmail1">Salary Advance Balance</label><i class="text-danger asterik">*</i>
                                <input type="text" class="form-control" name="salary_advance_balance" value="<?php echo $res[0]['salary_advance_balance']; ?>">
                            </div>
                            <div class="col-md-3">
                                    <label for="exampleInputEmail1">Level Referral Count</label><i class="text-danger asterik">*</i>
                                    <input type="text" class="form-control" name="l_referral_count" value="<?php echo $res[0]['l_referral_count']; ?>">
                                </div>
                                <div class="col-md-3">
                                    <label for="exampleInputEmail1">Per Code Cost</label><i class="text-danger asterik">*</i>
                                    <input type="text" class="form-control" name="per_code_cost" value="<?php echo $res[0]['per_code_cost']; ?>">
                                </div>
                        </div>

                            <br>
                            <div class="row">
                            <div class="form-group">
                                <div class="col-md-3">
                                    <label for="exampleInputEmail1">Per Code Value</label><i class="text-danger asterik">*</i>
                                    <input type="number" class="form-control" name="per_code_val" value="<?php echo $res[0]['per_code_val']; ?>">
                                </div>
                                <div class="col-md-3">
                                    <label for="exampleInputEmail1">Worked Days</label><i class="text-danger asterik">*</i>
                                    <input type="text" class="form-control" name="worked_days" value="<?php echo $res[0]['worked_days']; ?>">
                                </div>
                            </div>
                        </div>
                        <br>
                        <div class="row">
                        <div class="form-group col-md-3">
                                    <label for="exampleInputEmail1">Select Support</label> <i class="text-danger asterik">*</i>
                                    <select id='support_id' name="support_id" class='form-control' style="background-color: #7EC8E3">
                                             <option value="">--Select--</option>
                                                <?php
                                                $sql = "SELECT * FROM `staffs`";
                                                $db->sql($sql);

                                                $result = $db->getResult();
                                                foreach ($result as $value) {
                                                ?>
                                                    <option value='<?= $value['id'] ?>' <?= $value['id']==$res[0]['support_id'] ? 'selected="selected"' : '';?>><?= $value['name'] ?></option>
                                                    
                                                <?php } ?>
                                    </select>
                            </div>
                            <div class="form-group col-md-3">
                                    <label for="exampleInputEmail1">Select Branch</label> <i class="text-danger asterik">*</i>
                                    <select id='branch_id' name="branch_id" class='form-control'>
                                           <option value="">--Select--</option>
                                                <?php
                                                $sql = "SELECT * FROM `branches`";
                                                $db->sql($sql);

                                                $result = $db->getResult();
                                                foreach ($result as $value) {
                                                ?>
                                                    <option value='<?= $value['id'] ?>' <?= $value['id']==$res[0]['branch_id'] ? 'selected="selected"' : '';?>><?= $value['name'] ?></option>
                                                    
                                                <?php } ?>
                                    </select>
                            </div>
                        </div>

                        <div class="row">
									<div class="form-group col-md-12">
										<label class="control-label">Status</label><i class="text-danger asterik">*</i><br>
										<div id="status" class="btn-group">
											<label class="btn btn-primary" data-toggle-class="btn-primary" data-toggle-passive-class="btn-default">
												<input type="radio" name="status" value="0" <?= ($res[0]['status'] == 0) ? 'checked' : ''; ?>> Not-verified
											</label>
											<label class="btn btn-success" data-toggle-class="btn-default" data-toggle-passive-class="btn-default">
												<input type="radio" name="status" value="1" <?= ($res[0]['status'] == 1) ? 'checked' : ''; ?>> Verified
											</label>
                                            <label class="btn btn-danger" data-toggle-class="btn-default" data-toggle-passive-class="btn-default">
												<input type="radio" name="status" value="2" <?= ($res[0]['status'] == 2) ? 'checked' : ''; ?>> Blocked
											</label>
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

<script>
    var changeCheckbox = document.querySelector('#code_generate_button');
    var init = new Switchery(changeCheckbox);
    changeCheckbox.onchange = function() {
        if ($(this).is(':checked')) {
            $('#code_generate_status').val(1);

        } else {
            $('#code_generate_status').val(0);
        }
    };
</script>

<script>
    var changeCheckbox = document.querySelector('#withdrawal_button');
    var init = new Switchery(changeCheckbox);
    changeCheckbox.onchange = function() {
        if ($(this).is(':checked')) {
            $('#withdrawal_status').val(1);

        } else {
            $('#withdrawal_status').val(0);
        }
    };
</script>
<script>
    var changeCheckbox = document.querySelector('#security_button');
    var init = new Switchery(changeCheckbox);
    changeCheckbox.onchange = function() {
        if ($(this).is(':checked')) {
            $('#security').val(1);

        } else {
            $('#security').val(0);
        }
    };
</script>
<script>
    var changeCheckbox = document.querySelector('#eligible_button');
    var init = new Switchery(changeCheckbox);
    changeCheckbox.onchange = function() {
        if ($(this).is(':checked')) {
            $('#champion_task_eligible').val(1);

        } else {
            $('#champion_task_eligible').val(0);
        }
    };
</script>
<script>
    var changeCheckbox = document.querySelector('#black_box_button');
    var init = new Switchery(changeCheckbox);
    changeCheckbox.onchange = function() {
        if ($(this).is(':checked')) {
            $('#black_box').val(1);

        } else {
            $('#black_box').val(0);
        }
    };
</script>

<script>
    var changeCheckbox = document.querySelector('#ad_button');
    var init = new Switchery(changeCheckbox);
    changeCheckbox.onchange = function() {
        if ($(this).is(':checked')) {
            $('#ad_status').val(1);

        } else {
            $('#ad_status').val(0);
        }
    };
</script>