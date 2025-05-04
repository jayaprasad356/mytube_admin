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
    $balance = $db->escapeString($_POST['balance']);
    $earn = $db->escapeString($_POST['earn']);
    $plan_type = isset($_POST['plan_type']) ? $db->escapeString($_POST['plan_type']) : 'trial';
    $code_generate = isset($_POST['code_generate']) ? $db->escapeString($_POST['code_generate']) : 0;

    if (!empty($name) && !empty($mobile) && !empty($password)) {
        $sql_query = "UPDATE users SET 
            name='$name', 
            mobile='$mobile', 
            password='$password', 
            dob='$dob', 
            email='$email', 
            city='$city', 
            refer_code='$refer_code', 
            earn='$earn', 
            balance='$balance', 
            status='$status', 
            plan_type='$plan_type', 
            code_generate='$code_generate' 
            WHERE id=$ID";
        $db->sql($sql_query);
        $update_result = $db->getResult();

        if (empty($update_result)) {
            $error['update_users'] = "<section class='content-header'><span class='label label-success'>User updated successfully</span></section>";
        } else {
            $error['update_users'] = "<span class='label label-danger'>Failed to update user</span>";
        }
    }
}

$sql_query = "SELECT * FROM users WHERE id=$ID";
$db->sql($sql_query);
$res = $db->getResult();
?>

<section class="content-header">
    <h1>Edit User 
        <small>
            <a href='users.php'><i class='fa fa-angle-double-left'></i>&nbsp;&nbsp;&nbsp;Back to Users</a>
        </small>
    </h1>
    <small><?php echo isset($error['update_users']) ? $error['update_users'] : ''; ?></small>
</section>

<section class="content">
    <div class="row">
        <div class="col-md-10">
            <div class="box box-primary">
                <div class="box-header with-border">
                    <div class="form-group col-md-3">
                        <a class="btn btn-block btn-primary" href="add-recharge.php?id=<?php echo $ID ?>">
                            <i class="fa fa-plus-square"></i> Add Recharge
                        </a>
                    </div>
                    <div class="form-group col-md-3">
                        <a class="btn btn-block btn-success" href="add-balance.php?id=<?php echo $ID ?>">
                            <i class="fa fa-plus-square"></i> Add Balance
                        </a>
                    </div>
                    <div class="form-group col-md-3">
                        <a class="btn btn-block btn-danger" href="add-refer_bonus.php?id=<?php echo $ID ?>">
                            <i class="fa fa-plus-square"></i> Add Refer Bonus
                        </a>
                    </div>
                    <div class="form-group col-md-3">
                        <a class="btn btn-block btn-warning" href="add-refund.php?id=<?php echo $ID ?>">
                            <i class="fa fa-plus-square"></i> Add Refund
                        </a>
                    </div>
                </div>
                <form method="post">
                    <div class="box-body">
                        <div class="form-group col-md-6">
                            <label>Name</label>
                            <input type="text" class="form-control" name="name" value="<?php echo $res[0]['name']; ?>">
                        </div>
                        <div class="form-group col-md-6">
                            <label>Mobile</label>
                            <input type="text" class="form-control" name="mobile" value="<?php echo $res[0]['mobile']; ?>">
                        </div>
                        <div class="form-group col-md-6">
                            <label>Password</label>
                            <input type="text" class="form-control" name="password" value="<?php echo $res[0]['password']; ?>">
                        </div>
                        <div class="form-group col-md-6">
                            <label>Date of Birth</label>
                            <input type="date" class="form-control" name="dob" value="<?php echo $res[0]['dob']; ?>">
                        </div>
                        <div class="form-group col-md-6">
                            <label>Email</label>
                            <input type="email" class="form-control" name="email" value="<?php echo $res[0]['email']; ?>">
                        </div>
                        <div class="form-group col-md-6">
                            <label>City</label>
                            <input type="text" class="form-control" name="city" value="<?php echo $res[0]['city']; ?>">
                        </div>
                        <div class="form-group col-md-6">
                            <label>Refer Code</label>
                            <input type="text" class="form-control" name="refer_code" value="<?php echo $res[0]['refer_code']; ?>">
                        </div>
                        <div class="form-group col-md-6">
                            <label>Earn</label>
                            <input type="text" class="form-control" name="earn" value="<?php echo $res[0]['earn']; ?>">
                        </div>
                        <div class="form-group col-md-6">
                            <label>Balance</label>
                            <input type="text" class="form-control" name="balance" value="<?php echo $res[0]['balance']; ?>">
                        </div>
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
                                <option value="trial" <?= (isset($res[0]['plan_type']) && $res[0]['plan_type'] == 'trial') ? 'selected' : ''; ?>>Trial</option>
                                <option value="basic" <?= (isset($res[0]['plan_type']) && $res[0]['plan_type'] == 'basic') ? 'selected' : ''; ?>>Basic</option>
                                <option value="premium" <?= (isset($res[0]['plan_type']) && $res[0]['plan_type'] == 'premium') ? 'selected' : ''; ?>>Premium</option>
                            </select>
                        </div>

                        <!-- Code Generate Switch -->
                        <div class="form-group col-md-3">
                            <label for="code_generate_button">Code Generate</label><br>
                            <input 
                                type="checkbox" 
                                id="code_generate_button" 
                                class="js-switch" 
                                <?= isset($res[0]['code_generate']) && $res[0]['code_generate'] == 1 ? 'checked' : '' ?>
                            >
                            <input 
                                type="hidden" 
                                id="code_generate_status" 
                                name="code_generate" 
                                value="<?= isset($res[0]['code_generate']) && $res[0]['code_generate'] == 1 ? 1 : 0 ?>"
                            >
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

<!-- Switchery Script -->
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
