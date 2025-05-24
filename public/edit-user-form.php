<?php
include_once('includes/functions.php');
$function = new functions;
include_once('includes/custom-functions.php');
$fn = new custom_functions;

if (isset($_GET['id'])) {
    $ID = $db->escapeString($_GET['id']);
} else {
    return false;
    exit(0);
}

if (isset($_POST['btnEdit'])) {
    $datetime = date('Y-m-d H:i:s');

    $name = $db->escapeString($_POST['name']);
    $mobile = $db->escapeString($_POST['mobile']);
    $password = $db->escapeString($_POST['password']);
    $dob = $db->escapeString($_POST['dob']);
    $email = $db->escapeString($_POST['email']);
    $city = $db->escapeString($_POST['city']);
    $status = $db->escapeString($_POST['status']);
    $refer_code = $db->escapeString($_POST['refer_code']);
    $referred_by = $db->escapeString($_POST['referred_by']);
    $balance = $db->escapeString($_POST['balance']);
    $earn = $db->escapeString($_POST['earn']);
    $plan_type = isset($_POST['plan_type']) ? $db->escapeString($_POST['plan_type']) : 'trial';
    $code_generate = isset($_POST['code_generate']) ? $db->escapeString($_POST['code_generate']) : 0;
    $joined_date = $db->escapeString($_POST['joined_date']);
    $video_duration = $db->escapeString($_POST['video_duration']); // ✅ New

    if (!empty($name) && !empty($mobile) && !empty($password)) {
        $sql_query = "UPDATE users SET 
            name='$name', 
            mobile='$mobile', 
            password='$password', 
            dob='$dob', 
            email='$email', 
            city='$city', 
            refer_code='$refer_code', 
            referred_by='$referred_by',
            earn='$earn', 
            balance='$balance', 
            status='$status', 
            plan_type='$plan_type', 
            code_generate='$code_generate',
            joined_date='$joined_date',
            video_duration='$video_duration'
            WHERE id=$ID";
        $db->sql($sql_query);
        $update_result = $db->getResult();

        if (empty($update_result)) {
            $error['update_users'] = "<section class='content-header'><span class='label label-success'>User updated successfully</span></section>";
        } else {
            $error['update_users'] = "<span class='label label-danger'>Failed to update user</span>";
        }

        $check_user_query = "SELECT * FROM users WHERE id = $ID";
        $db->sql($check_user_query);
        $check_user = $db->getResult();

        if (!empty($check_user) && $check_user[0]['refer_bonus_sent'] == 0 && !empty($referred_by)) {
            $user_plan_type = strtolower($check_user[0]['plan_type']);

            if ($user_plan_type == 'basic' || $user_plan_type == 'premium') {
                $referrer_query = "SELECT * FROM users WHERE refer_code = '$referred_by'";
                $db->sql($referrer_query);
                $referrer_result = $db->getResult();

                if (!empty($referrer_result)) {
                    $referrer_id = $referrer_result[0]['id'];
                    $referrer_name = $referrer_result[0]['name'];

                    $update_referrer = "UPDATE users 
                        SET today_codes = today_codes + 2000,
                            total_codes = total_codes + 2000,
                            earn = earn + 500,
                            bonus_wallet = bonus_wallet + 500
                        WHERE id = $referrer_id";
                    $db->sql($update_referrer);

                    $total_cost = 500;
                    $datetime = date('Y-m-d H:i:s');
                    $sql = "INSERT INTO transactions (user_id, amount, datetime, type) 
                            VALUES ('$referrer_id', '$total_cost', '$datetime', 'refer_bonus')";
                    $db->sql($sql);

                    $db->sql("UPDATE users SET refer_bonus_sent = 1 WHERE id = $ID");

                    $error['update_users'] .= "<br><span class='label label-info'>Referral bonus sent to $referrer_name (ID: $referrer_id) with transaction recorded</span>";
                } else {
                    $error['update_users'] .= "<br><span class='label label-warning'>Referrer with code $referred_by not found</span>";
                }
            } else {
                $error['update_users'] .= "<br><span class='label label-default'>No referral bonus: user plan type is '$user_plan_type'</span>";
            }
        }
    }
}

$sql_query = "SELECT * FROM users WHERE id=$ID";
$db->sql($sql_query);
$res = $db->getResult();
?>

<section class="content-header">
    <h1>Edit User 
        <small><a href='users.php'><i class='fa fa-angle-double-left'></i>&nbsp;&nbsp;&nbsp;Back to Users</a></small>
    </h1>
    <small><?php echo isset($error['update_users']) ? $error['update_users'] : ''; ?></small>
</section>

<section class="content">
    <div class="row">
        <div class="col-md-10">
            <div class="box box-primary">
                <div class="box-header with-border">
                    <div class="form-group col-md-3"><a class="btn btn-block btn-primary" href="add-recharge.php?id=<?php echo $ID ?>"><i class="fa fa-plus-square"></i> Add Recharge</a></div>
                    <div class="form-group col-md-3"><a class="btn btn-block btn-success" href="add-balance.php?id=<?php echo $ID ?>"><i class="fa fa-plus-square"></i> Add Balance</a></div>
                    <div class="form-group col-md-3"><a class="btn btn-block btn-danger" href="add-refer_bonus.php?id=<?php echo $ID ?>"><i class="fa fa-plus-square"></i> Add Refer Bonus</a></div>
                    <div class="form-group col-md-3"><a class="btn btn-block btn-warning" href="add-refund.php?id=<?php echo $ID ?>"><i class="fa fa-plus-square"></i> Add Refund</a></div>
                </div>
                <form method="post">
                    <div class="box-body">
                        <div class="form-group col-md-6"><label>Name</label><input type="text" class="form-control" name="name" value="<?php echo $res[0]['name']; ?>"></div>
                        <div class="form-group col-md-6"><label>Mobile</label><input type="text" class="form-control" name="mobile" value="<?php echo $res[0]['mobile']; ?>"></div>
                        <div class="form-group col-md-6"><label>Password</label><input type="text" class="form-control" name="password" value="<?php echo $res[0]['password']; ?>"></div>
                        <div class="form-group col-md-6"><label>Date of Birth</label><input type="date" class="form-control" name="dob" value="<?php echo $res[0]['dob']; ?>"></div>
                        <div class="form-group col-md-6"><label>Email</label><input type="email" class="form-control" name="email" value="<?php echo $res[0]['email']; ?>"></div>
                        <div class="form-group col-md-6"><label>City</label><input type="text" class="form-control" name="city" value="<?php echo $res[0]['city']; ?>"></div>
                        <div class="form-group col-md-6"><label>Refer Code</label><input type="text" class="form-control" name="refer_code" value="<?php echo $res[0]['refer_code']; ?>"></div>
                        <div class="form-group col-md-6"><label>Referred By</label><input type="text" class="form-control" name="referred_by" value="<?php echo $res[0]['referred_by']; ?>"></div>
                        <div class="form-group col-md-6"><label>Earn</label><input type="text" class="form-control" name="earn" value="<?php echo $res[0]['earn']; ?>"></div>
                        <div class="form-group col-md-6"><label>Balance</label><input type="text" class="form-control" name="balance" value="<?php echo $res[0]['balance']; ?>"></div>
                        <div class="form-group col-md-6">
                            <label>Status</label>
                            <select class="form-control" name="status">
                                <option value="0" <?= ($res[0]['status'] == 0) ? 'selected' : ''; ?>>Not-verified</option>
                                <option value="1" <?= ($res[0]['status'] == 1) ? 'selected' : ''; ?>>Verified</option>
                                <option value="2" <?= ($res[0]['status'] == 2) ? 'selected' : ''; ?>>Blocked</option>
                            </select>
                        </div>
                        <div class="form-group col-md-6">
                            <label>Plan Type</label>
                            <select class="form-control" name="plan_type">
                                <option value="trial" <?= ($res[0]['plan_type'] == 'trial') ? 'selected' : ''; ?>>Trial</option>
                                <option value="basic" <?= ($res[0]['plan_type'] == 'basic') ? 'selected' : ''; ?>>Basic</option>
                                <option value="premium" <?= ($res[0]['plan_type'] == 'premium') ? 'selected' : ''; ?>>Premium</option>
                            </select>
                        </div>
                        <div class="form-group col-md-6">
                            <label>Joined Date</label>
                            <input type="date" class="form-control" name="joined_date" value="<?php echo date('Y-m-d', strtotime($res[0]['joined_date'])); ?>">
                        </div>
                        <div class="form-group col-md-6">
                            <label>Video Duration (in seconds)</label>
                            <input type="number" class="form-control" name="video_duration" value="<?php echo $res[0]['video_duration']; ?>">
                        </div>
                        <div class="form-group col-md-3">
                            <label for="code_generate_button">Code Generate</label><br>
                            <input type="checkbox" id="code_generate_button" class="js-switch" <?= ($res[0]['code_generate'] == 1) ? 'checked' : '' ?>>
                            <input type="hidden" id="code_generate_status" name="code_generate" value="<?= ($res[0]['code_generate'] == 1) ? 1 : 0 ?>">
                        </div>
                    </div>
                    <div class="box-footer">
                        <button type="submit" class="btn btn-primary" name="btnEdit">Update</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>

<script>
    $(document).ready(function() {
        var changeCheckbox = document.querySelector('#code_generate_button');
        if (changeCheckbox) {
            var switchery = new Switchery(changeCheckbox);
            changeCheckbox.onchange = function() {
                $('#code_generate_status').val(this.checked ? 1 : 0);
            };
        }
    });
</script>

<?php $db->disconnect(); ?>
