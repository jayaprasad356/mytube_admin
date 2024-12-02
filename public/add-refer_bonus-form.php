<?php
include_once('includes/functions.php');
$function = new functions;
include_once('includes/custom-functions.php');
$fn = new custom_functions;
date_default_timezone_set('Asia/Kolkata');

// Fetch plans from the database
$plans = [];
$sql = "SELECT id, name FROM plan";  // Assuming you have a 'plans' table
$db->sql($sql);
$plan = $db->getResult(); 

$plan = [
    1 => ['name' => 'Plan 1 - Basic Plan - ₹ 2999', 'refer_bonus' => 300],
    2 => ['name' => 'Plan 2 - Standard Plan - ₹ 3999', 'refer_bonus' => 400],
    4 => ['name' => 'Plan 4 - Advanced Plan - ₹5999', 'refer_bonus' => 600],
];
?>
<?php
$error = array();
$ID = isset($_GET['id']) ? $db->escapeString($_GET['id']) : null;


if(isset($_POST['btnAdd'])) {
    $plan_id = $db->escapeString($_POST['plan_id']);
    $refer_bonus = $db->escapeString($_POST['refer_bonus']);

    if (empty($plan_id) || empty($refer_bonus)) {
        $error['plan'] = "<span class='label label-danger'>Required!</span>";
    } else {
        $datetime = date('Y-m-d H:i:s');
        $type = 'refer_bonus';
        
        // Insert into transactions table
        $sql = "INSERT INTO transactions (`user_id`, `amount`, `datetime`, `type`) 
                VALUES ('$ID', '$refer_bonus', '$datetime', '$type')";
        $db->sql($sql);
        
        // Update user's bonus_wallet
        $update_sql = "UPDATE users SET bonus_wallet = bonus_wallet + $refer_bonus , total_referrals = total_referrals + 1 WHERE id = $ID";
        $db->sql($update_sql);
        $result = $db->getResult();

        if (!empty($result)) {
            $result = 0;
        } else {
            $result = 1;
        }

        if ($result == 1) {
            header("Location: add-refer_bonus.php?status=success");
            exit();
        } else {
            $error['add_balance'] = "<section class='content-header'>
                                        <span class='label label-danger'>Failed</span>
                                     </section>";
        }
    }
}
?>
<section class="content-header">
    <h1>Add Refer Bonus <small><a href='users.php'><i class='fa fa-angle-double-left'></i>&nbsp;&nbsp;&nbsp;Back to Refer Bonus</a></small></h1>
    <?php 
    if (isset($_GET['status']) && $_GET['status'] == 'success') {
        echo "<section class='content-header'>
                <span class='label label-success'>Refer Bonus Added Successfully</span>
              </section>";
    } else {
        echo isset($error['add_balance']) ? $error['add_balance'] : ''; 
    }
    ?>
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
                <form name="add_bonus_form" method="post" enctype="multipart/form-data">
                    <div class="box-body">
                        <div class="form-group">
                            <label for="plan_id">Select Plan</label>
                            <select class="form-control" name="plan_id" id="plan_id" required>
                            <?php foreach ($plan as $id => $planDetails) { ?>
                                <option value="<?php echo $id; ?>" data-bonus="<?php echo $planDetails['refer_bonus']; ?>" <?php echo ($id == 1) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($planDetails['name']); ?>
                                </option>
                            <?php } ?>
                        </select>

                        </div>
                        <div class="form-group">
                            <label for="refer_bonus">Refer Bonus</label>
                            <input type="number" class="form-control" name="refer_bonus" id="refer_bonus" readonly value="<?php echo $plans[1]['refer_bonus']; ?>">
                        </div>
                    </div>
                    <div class="box-footer">
                        <button type="submit" class="btn btn-primary" name="btnAdd">Add</button>
                    </div>
                </form>


            </div><!-- /.box -->
        </div>  
    </div>
</section>

<div class="separator"> </div>
<script>
document.getElementById('plan_id').addEventListener('change', function() {
    var selectedOption = this.options[this.selectedIndex];
    var bonus = selectedOption.getAttribute('data-bonus');
    document.getElementById('refer_bonus').value = bonus;
});

// Ensure the default plan's bonus is set on page load
window.onload = function() {
    var planSelect = document.getElementById('plan_id');
    var defaultBonus = planSelect.options[planSelect.selectedIndex].getAttribute('data-bonus');
    document.getElementById('refer_bonus').value = defaultBonus;
};
</script>


<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.17.0/jquery.validate.min.js"></script>

<?php $db->disconnect(); ?>
