<?php
include_once('includes/functions.php');
$function = new functions;
include_once('includes/custom-functions.php');
$fn = new custom_functions;

?>
<?php
if (isset($_POST['btnUpdate'])) {

    $code_generate = $db->escapeString(($_POST['code_generate']));
    $outsource_code_generate = $db->escapeString(($_POST['outsource_code_generate']));
    $withdrawal_status = $db->escapeString(($_POST['withdrawal_status']));
    $sync_time = $db->escapeString(($_POST['sync_time']));
    $duration = $db->escapeString(($_POST['duration']));
    $min_withdrawal = $db->escapeString(($_POST['min_withdrawal']));
    $chat_support = $db->escapeString(($_POST['chat_support']));
    $reward = $db->escapeString(($_POST['reward']));
    $ad_show_time = $db->escapeString(($_POST['ad_show_time']));
    $ad_status = $db->escapeString(($_POST['ad_status']));
    $ad_type = (isset($_POST['ad_type']) && !empty($_POST['ad_type'])) ? $db->escapeString($fn->xss_clean($_POST['ad_type'])) : "0";   
    $fetch_time = $db->escapeString(($_POST['fetch_time']));
    $sync_codes = $db->escapeString(($_POST['sync_codes']));
    $num_sync_times = $db->escapeString(($_POST['num_sync_times']));
    $min_sync_refer_wallet = $db->escapeString(($_POST['min_sync_refer_wallet']));
    $main_content = $db->escapeString(($_POST['main_content']));
    $ad_link = $db->escapeString(($_POST['ad_link']));
    $error = array();
    $sql_query = "UPDATE settings SET code_generate=$code_generate,withdrawal_status=$withdrawal_status,sync_time=$sync_time,duration='$duration',min_withdrawal = $min_withdrawal,chat_support = $chat_support,reward = $reward,ad_show_time = $ad_show_time,ad_status = $ad_status,ad_type='$ad_type',fetch_time = $fetch_time,sync_codes = $sync_codes,min_sync_refer_wallet = $min_sync_refer_wallet,num_sync_times='$num_sync_times',main_content='$main_content',ad_link = '$ad_link',outsource_code_generate = '$outsource_code_generate' WHERE id=1";
    $db->sql($sql_query);
    $result = $db->getResult();
    if (!empty($result)) {
        $result = 0;
    } else {
        $result = 1;
    }

    if ($result == 1) {
        
        $error['update'] = "<section class='content-header'>
                                        <span class='label label-success'>Settings Updated Successfully</span> </section>";
    } else {
        $error['update'] = " <span class='label label-danger'>Failed</span>";
    }
}

    // create array variable to store previous data
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
        <div class="col-md-10">
           
            <!-- general form elements -->
            <div class="box box-primary">
                <div class="box-header with-border">

                </div>
                <!-- /.box-header -->
                <!-- form start -->
                <form name="delivery_charge" method="post" enctype="multipart/form-data">
                    <div class="box-body">
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="">Code Generate</label><br>
                                        <input type="checkbox" id="code_generate_button" class="js-switch" <?= isset($res[0]['code_generate']) && $res[0]['code_generate'] == 1 ? 'checked' : '' ?>>
                                        <input type="hidden" id="code_generate_status" name="code_generate" value="<?= isset($res[0]['code_generate']) && $res[0]['code_generate'] == 1 ? 1 : 0 ?>">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="">Outsource Code Generate</label><br>
                                        <input type="checkbox" id="outsource_code_generate_button" class="js-switch" <?= isset($res[0]['outsource_code_generate']) && $res[0]['outsource_code_generate'] == 1 ? 'checked' : '' ?>>
                                        <input type="hidden" id="outsource_code_generate_status" name="outsource_code_generate" value="<?= isset($res[0]['outsource_code_generate']) && $res[0]['outsource_code_generate'] == 1 ? 1 : 0 ?>">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="">Withdrawal Status</label><br>
                                        <input type="checkbox" id="withdrawal_button" class="js-switch" <?= isset($res[0]['withdrawal_status']) && $res[0]['withdrawal_status'] == 1 ? 'checked' : '' ?>>
                                        <input type="hidden" id="withdrawal_status" name="withdrawal_status" value="<?= isset($res[0]['withdrawal_status']) && $res[0]['withdrawal_status'] == 1 ? 1 : 0 ?>">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="">Chat Support</label><br>
                                        <input type="checkbox" id="chat_button" class="js-switch" <?= isset($res[0]['chat_support']) && $res[0]['chat_support'] == 1 ? 'checked' : '' ?>>
                                        <input type="hidden" id="chat_support" name="chat_support" value="<?= isset($res[0]['chat_support']) && $res[0]['chat_support'] == 1 ? 1 : 0 ?>">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="">Ad Status</label><br>
                                        <input type="checkbox" id="ad_button" class="js-switch" <?= isset($res[0]['ad_status']) && $res[0]['ad_status'] == 1 ? 'checked' : '' ?>>
                                        <input type="hidden" id="ad_status" name="ad_status" value="<?= isset($res[0]['ad_status']) && $res[0]['ad_status'] == 1 ? 1 : 0 ?>">
                                    </div>
                                </div>
                            </div>
                            <br>
                            <div class="row">
                                <div class="col-md-4">
                                   <div class="form-group" id="status" style="<?php echo isset($res[0]['ad_status']) == 1 ? '' : 'display:none;' ?>">
                                        <label class="control-label">Ad Type</label> <i class="text-danger asterik">*</i><br>
                                        <div  class="btn-group">
                                            <label class="btn btn-primary" data-toggle-class="btn-default" data-toggle-passive-class="btn-default">
                                                <input type="radio" name="ad_type" value="1" <?= ($res[0]['ad_type'] == 1) ? 'checked' : ''; ?>> Google Ad
                                            </label>
                                            <label class="btn btn-info" data-toggle-class="btn-primary" data-toggle-passive-class="btn-default">
                                                <input type="radio" name="ad_type" value="2" <?= ($res[0]['ad_type'] == 2) ? 'checked' : ''; ?>> Private Ad
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="">Sync Time(min)</label><br>
                                        <input type="number"class="form-control" name="sync_time" value="<?= $res[0]['sync_time'] ?>">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="">Duration <small>(days)</small></label><br>
                                        <input type="number"class="form-control" name="duration" value="<?= $res[0]['duration'] ?>">
                                    </div>
                                </div>
                            </div>
                            <br>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="">Minimum Withdrawal</label><br>
                                        <input type="number"class="form-control" name="min_withdrawal" value="<?= $res[0]['min_withdrawal'] ?>">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="">Code Rewards</label><br>
                                        <input type="number"class="form-control" name="reward" value="<?= $res[0]['reward'] ?>">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="">Ad Show Time(min)</label><br>
                                        <input type="number"class="form-control" name="ad_show_time" value="<?= $res[0]['ad_show_time'] ?>">
                                    </div>
                                </div>
                            </div>
                            <br>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="">Fetch Time(sec)</label><br>
                                        <input type="number"class="form-control" name="fetch_time" value="<?= $res[0]['fetch_time'] ?>">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="">Sync Codes</label><br>
                                        <input type="number"class="form-control" name="sync_codes" value="<?= $res[0]['sync_codes'] ?>">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="">Number of Sync Times</label><br>
                                        <input type="number"class="form-control" name="num_sync_times" value="<?= $res[0]['num_sync_times'] ?>">
                                    </div>
                                </div>
                            </div>
                            <br>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="">Minimum Sync Refer Wallet</label><br>
                                        <input type="number"class="form-control" name="min_sync_refer_wallet" value="<?= $res[0]['min_sync_refer_wallet'] ?>">
                                    </div>
                                </div>  
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="">Ad Link</label><br>
                                        <input type="link" class="form-control" name="ad_link" value="<?= $res[0]['ad_link'] ?>">
                                    </div>
                                </div>  
                            </div> 
                            <br>   
                            <div class="form-group">
                                    <label for="main_content">Main Content :</label> <i class="text-danger asterik">*</i><?php echo isset($error['main_content']) ? $error['main_content'] : ''; ?>
                                    <textarea name="main_content" id="main_content" class="form-control" rows="8"><?php echo $res[0]['main_content']; ?></textarea>
                                    <script type="text/javascript" src="css/js/ckeditor/ckeditor.js"></script>
                                    <script type="text/javascript">
                                        CKEDITOR.replace('main_content');
                                    </script>
                            </div>              
                            
                           
                    </div>
                  
                    <!-- /.box-body -->

                    <div class="box-footer">
                        <button type="submit" class="btn btn-primary" name="btnUpdate">Update</button>
                    </div>

                </form>

            </div><!-- /.box -->
        </div>
    </div>
</section>

<div class="separator"> </div>

<?php $db->disconnect(); ?>
<script>
    $('#delivery_charge').validate({
        rules: {
            main_content: {
                required: function(textarea) {
                    CKEDITOR.instances[textarea.id].updateElement();
                    var editorcontent = textarea.value.replace(/<[^>]*>/gi, '');
                    return editorcontent.length === 0;
                }
            }
        }
    });
</script>

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
    var changeCheckbox = document.querySelector('#outsource_code_generate_button');
    var init = new Switchery(changeCheckbox);
    changeCheckbox.onchange = function() {
        if ($(this).is(':checked')) {
            $('#outsource_code_generate_status').val(1);

        } else {
            $('#outsource_code_generate_status').val(0);
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
    var changeCheckbox = document.querySelector('#chat_button');
    var init = new Switchery(changeCheckbox);
    changeCheckbox.onchange = function() {
        if ($(this).is(':checked')) {
            $('#chat_support').val(1);

        } else {
            $('#chat_support').val(0);
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