<?php
include_once('includes/functions.php');
$function = new functions;
include_once('includes/custom-functions.php');
$fn = new custom_functions;

?>
<?php
if (isset($_POST['btnUpdate'])) {

    $join_codes = $db->escapeString(($_POST['join_codes']));
    $refer_bonus_codes = $db->escapeString(($_POST['refer_bonus_codes']));
    $refer_bonus_amount = $db->escapeString(($_POST['refer_bonus_amount']));
    $refer_description = $db->escapeString(($_POST['refer_description']));
    $error = array();
    $sql_query = "UPDATE settings SET join_codes=$join_codes,refer_bonus_codes=$refer_bonus_codes,refer_bonus_amount=$refer_bonus_amount,refer_description='$refer_description' WHERE id=1";
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
    <h1>Reward Settings</h1>
    <?php echo isset($error['update']) ? $error['update'] : ''; ?>
    <ol class="breadcrumb">
        <li><a href="home.php"><i class="fa fa-home"></i> Home</a></li>
    </ol>
    <hr />
</section>
<section class="content">
    <div class="row">
        <div class="col-md-8">
           
            <!-- general form elements -->
            <div class="box box-primary">
                <div class="box-header with-border">

                </div>
                <!-- /.box-header -->
                <!-- form start -->
                <form name="delivery_charge" method="post" enctype="multipart/form-data">
                    <div class="box-body">
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="">Joining Codes</label><br>
                                        <input type="number"class="form-control" name="join_codes" value="<?= $res[0]['join_codes'] ?>">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="">Refer Bonus Codes</label><br>
                                        <input type="number"class="form-control" name="refer_bonus_codes" value="<?= $res[0]['refer_bonus_codes'] ?>">
                                    </div>
                                </div>
                            </div>
                            <br>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="">Refer Bonus Amount</label><br>
                                        <input type="number"class="form-control" name="refer_bonus_amount" value="<?= $res[0]['refer_bonus_amount'] ?>">
                                    </div>
                                </div>
                            
                            </div>
                            <br>
                            <div class="row">
                                <div class="col-md-10">
                                <div class="form-group">
                                    <label for="description">Refer Description :</label> <i class="text-danger asterik">*</i><?php echo isset($error['refer_description']) ? $error['refer_description'] : ''; ?>
                                    <textarea name="refer_description" id="refer_description" class="form-control" rows="3"><?= $res[0]['refer_description'] ?></textarea>
                                </div>
                                </div>

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