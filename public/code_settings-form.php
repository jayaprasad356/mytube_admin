<?php
include_once('includes/functions.php');
$function = new functions;
include_once('includes/custom-functions.php');
$fn = new custom_functions;

?>
<?php
if (isset($_POST['btnUpdate'])) {

    $min_days = $db->escapeString(($_POST['min_days']));
    $min_codes = $db->escapeString(($_POST['min_codes']));
    $code_generate_time = $db->escapeString(($_POST['code_generate_time']));
    $error = array();
    $sql_query = "UPDATE code_settings SET min_days=$min_days,min_codes=$min_codes,code_generate_time=$code_generate_time WHERE id=1";
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

$sql_query = "SELECT * FROM code_settings WHERE id = 1";
$db->sql($sql_query);
$res = $db->getResult();
?>
<section class="content-header">
    <h1>Code Settings</h1>
    <?php echo isset($error['update']) ? $error['update'] : ''; ?>
    <ol class="breadcrumb">
        <li><a href="reports.php"><i class="fa fa-home"></i> Home</a></li>
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
                <form name="code_settings" method="post" enctype="multipart/form-data">
                    <div class="box-body">
                            <div class="row">
                                <div class="col-md-8">
                                    <div class="form-group">
                                        <label for="">Minimum Codes</label>
                                        <input type="number"class="form-control" name="min_codes" value="<?= $res[0]['min_codes'] ?>">
                                    </div>
                                </div>
                            </div>
                            <br>
                            <div class="row">
                                <div class="col-md-8">
                                    <div class="form-group">
                                        <label for="">Minimum Days</label>
                                        <input type="number"class="form-control" name="min_days" value="<?= $res[0]['min_days'] ?>">
                                    </div>
                                </div>
                            </div>
                            <br>
                            <div class="row">
                                <div class="col-md-8">
                                    <div class="form-group">
                                        <label for="">Code Generate Time</label>
                                        <input type="number"class="form-control" name="code_generate_time" value="<?= $res[0]['code_generate_time'] ?>">
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