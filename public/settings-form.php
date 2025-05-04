<?php
include_once('includes/functions.php');
$function = new functions;

if (isset($_POST['btnUpdate'])) {
    $code_generate = $db->escapeString(($_POST['code_generate']));
    $withdrawal_status = $db->escapeString(($_POST['withdrawal_status']));
    $min_withdrawal = $db->escapeString(($_POST['min_withdrawal']));

    $error = array();
    $sql_query = "UPDATE settings SET code_generate=$code_generate, withdrawal_status=$withdrawal_status, min_withdrawal=$min_withdrawal WHERE id=1";
    $db->sql($sql_query);
    $result = $db->getResult();

    if (!empty($result)) {
        $result = 0;
    } else {
        $result = 1;
    }

    if ($result == 1) {
        $error['update'] = "<section class='content-header'>
                                <span class='label label-success'>Settings Updated Successfully</span>
                            </section>";
    } else {
        $error['update'] = "<span class='label label-danger'>Failed</span>";
    }
}

$data = array();
$sql_query = "SELECT * FROM settings WHERE id = 1";
$db->sql($sql_query);
$res = $db->getResult();
?>
<section class="content-header">
    <h1>Settings</h1>
    <?php echo isset($error['update']) ? $error['update'] : ''; ?>
    <ol class="breadcrumb">
        <li><a href="reports.php"><i class="fa fa-home"></i> Home</a></li>
    </ol>
    <hr />
</section>
<section class="content">
    <div class="row">
        <div class="col-md-6">
            <div class="box box-primary">
                <form name="settings_form" method="post" enctype="multipart/form-data">
                    <div class="box-body">
                        <div class="form-group">
                            <label for="">Code Generate</label><br>
                            <input type="checkbox" id="code_generate_button" class="js-switch" <?= isset($res[0]['code_generate']) && $res[0]['code_generate'] == 1 ? 'checked' : '' ?>>
                            <input type="hidden" id="code_generate_status" name="code_generate" value="<?= isset($res[0]['code_generate']) && $res[0]['code_generate'] == 1 ? 1 : 0 ?>">
                        </div>
                        <div class="form-group">
                            <label for="">Withdrawal Status</label><br>
                            <input type="checkbox" id="withdrawal_button" class="js-switch" <?= isset($res[0]['withdrawal_status']) && $res[0]['withdrawal_status'] == 1 ? 'checked' : '' ?>>
                            <input type="hidden" id="withdrawal_status" name="withdrawal_status" value="<?= isset($res[0]['withdrawal_status']) && $res[0]['withdrawal_status'] == 1 ? 1 : 0 ?>">
                        </div>
                        <div class="form-group">
                            <label for="">Minimum Withdrawal</label><br>
                            <input type="number" class="form-control" name="min_withdrawal" value="<?= $res[0]['min_withdrawal'] ?>">
                        </div>
                    </div>
                    <div class="box-footer">
                        <button type="submit" class="btn btn-primary" name="btnUpdate">Update</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>
<?php $db->disconnect(); ?>
<script>
    var codeGenerateCheckbox = document.querySelector('#code_generate_button');
    new Switchery(codeGenerateCheckbox);
    codeGenerateCheckbox.onchange = function() {
        $('#code_generate_status').val(this.checked ? 1 : 0);
    };

    var withdrawalCheckbox = document.querySelector('#withdrawal_button');
    new Switchery(withdrawalCheckbox);
    withdrawalCheckbox.onchange = function() {
        $('#withdrawal_status').val(this.checked ? 1 : 0);
    };
</script>
