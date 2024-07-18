<?php
include_once('includes/functions.php');
$function = new functions;
include_once('includes/custom-functions.php');
$fn = new custom_functions;

?>
<?php
if (isset($_POST['btnUpdate'])) {

    $champion_task = $db->escapeString(($_POST['champion_task']));
    $champion_codes = $db->escapeString(($_POST['champion_codes']));
    $champion_search_count = $db->escapeString(($_POST['champion_search_count']));
    $champion_demo_link = $db->escapeString(($_POST['champion_demo_link']));
    $error = array();
    $sql_query = "UPDATE settings SET champion_task=$champion_task,champion_codes = $champion_codes,champion_demo_link = '$champion_demo_link',champion_search_count = $champion_search_count WHERE id=1";
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
    <h1>Champion Settings</h1>
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
                                        <label for="">Champion Task</label><br>
                                        <input type="checkbox" id="task_button" class="js-switch" <?= isset($res[0]['champion_task']) && $res[0]['champion_task'] == 1 ? 'checked' : '' ?>>
                                        <input type="hidden" id="champion_task" name="champion_task" value="<?= isset($res[0]['champion_task']) && $res[0]['champion_task'] == 1 ? 1 : 0 ?>">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="">Champion Codes</label><br>
                                        <input type="number"class="form-control" name="champion_codes" value="<?= $res[0]['champion_codes'] ?>">
                                    </div>
                                </div>
                            </div>
                            <br>
                            <div class="row">
                               <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="">Champion Search Count</label><br>
                                        <input type="number"class="form-control" name="champion_search_count" value="<?= $res[0]['champion_search_count'] ?>">
                                    </div>
                                </div>
                            
                            </div>
                            <br>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="">Champion Demo Link</label><br>
                                        <input type="link"class="form-control" name="champion_demo_link" value="<?= $res[0]['champion_demo_link'] ?>">
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
    var changeCheckbox = document.querySelector('#task_button');
    var init = new Switchery(changeCheckbox);
    changeCheckbox.onchange = function() {
        if ($(this).is(':checked')) {
            $('#champion_task').val(1);
        } else {
            $('#champion_task').val(0);
        }
    };
</script>