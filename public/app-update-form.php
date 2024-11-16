<?php
include_once('includes/functions.php');
$function = new functions;
include_once('includes/custom-functions.php');
$fn = new custom_functions;

?>
<?php
if (isset($_POST['btnUpdate'])) {

    $link = $db->escapeString($_POST['link']);
    $version = $db->escapeString($_POST['version']);
    $description = $db->escapeString($_POST['description']);

    if (!empty($_FILES['file_upload']['name'])) {
        $file_name = $_FILES['file_upload']['name'];
        $file_tmp_name = $_FILES['file_upload']['tmp_name'];

        $upload_directory = 'upload/files/';
        $file_path = $upload_directory . $file_name;
        move_uploaded_file($file_tmp_name, $file_path);
        
        $file_path_sql = ", `file_upload` = '$file_path'";
    } else {
        $file_path_sql = "";
    }

    if (!empty($link) && !empty($version) && !empty($description)) {
        $sql_query = "UPDATE `app_settings` SET `link` = '$link',`version` = '$version',`description` = '$description' $file_path_sql WHERE `app_settings`.`id` = 1;";
        $db->sql($sql_query);
        $result = $db->getResult();
        if (!empty($result)) {
            $result = 0;
        } else {
            $result = 1;
        }

        if ($result == 1) {
            $error['update_app_settings'] = "<section class='content-header'>
                                                <span class='label label-success'>Updated Successfully</span> </section>";
        } else {
            $error['update_app_settings'] = " <span class='label label-danger'>Failed</span>";
        }
    }
}
$sql_query = "SELECT * FROM app_settings ";
$db->sql($sql_query);
$res = $db->getResult();
?>
<section class="content-header">
    <h1>App Update</h1>

    <?php echo isset($error['add_staff']) ? $error['add_staff'] : ''; ?>
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
                <form name="add_staff_form" method="post" enctype="multipart/form-data">
                    <div class="box-body">
                           <div class="row">
                                <div class="form-group">
                                    <div class="col-md-12">
                                            <label for="exampleInputEmail1">Link</label> <i class="text-danger asterik">*</i><?php echo isset($error['link']) ? $error['link'] : ''; ?>
                                            <input type="text" class="form-control" name="link" value="<?php echo $res[0]['link']?>" required>
                                    </div>
                                </div>
                            </div>
                            <br>
                            <div class="row">
                                <div class="form-group">
                                    <div class="col-md-6">
                                            <label for="exampleInputEmail1"> App Version</label> <i class="text-danger asterik">*</i><?php echo isset($error['version']) ? $error['version'] : ''; ?>
                                            <input type="number" class="form-control" name="version" value="<?php echo  $res[0]['version']?>" required>
                                    </div>
                                </div>
                            </div>
                            <br>
                            <div class="row">
                                <div class="form-group">
                                    <div class="col-md-6">
                                            <label for="exampleInputEmail1"> Description</label> <i class="text-danger asterik">*</i><?php echo isset($error['description']) ? $error['description'] : ''; ?>
                                            <textarea type="text" rows="4" class="form-control" name="description" required><?php echo $res[0]['description']?></textarea>
                                    </div>
                                 </div>
                            </div>
                            <div class="row">
                                <div class="form-group">
                                    <div class="col-md-10">
                                        <label for="exampleInputFile">File Upload</label><?php echo isset($error['file_upload']) ? $error['file_upload'] : ''; ?>
                                        <input type="file" id="exampleInputFile" name="file_upload">
                                        <p class="help-block">Upload your file here.</p>
                                        <?php if (!empty($res[0]['file_upload'])): ?>
                                            <p>Uploaded File: <a href="<?php echo $res[0]['file_upload']; ?>"><?php echo basename($res[0]['file_upload']); ?></a></p>
                                        <?php endif; ?>
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
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.17.0/jquery.validate.min.js"></script>
<script>
    $('#add_staff_form').validate({

        ignore: [],
        debug: false,
        rules: {
            name: "required",
            role: "required",
            category_image: "required",
            mobile:"required",
        }
    });
    $('#btnClear').on('click', function() {
        for (instance in CKEDITOR.instances) {
            CKEDITOR.instances[instance].setData('');
        }
    });
</script>
<script>
    function readURL(input) {
            if (input.files && input.files[0]) {
                var reader = new FileReader();

                reader.onload = function (e) {
                    $('#blah')
                        .attr('src', e.target.result)
                        .width(150)
                        .height(200);
                };

                reader.readAsDataURL(input.files[0]);
            }
        }
</script>

<!--code for page clear-->
<script>
    function refreshPage(){
    window.location.reload();
} 
</script>

<?php $db->disconnect(); ?>