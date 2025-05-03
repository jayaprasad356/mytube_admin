<?php
include_once('includes/functions.php');
$function = new functions;
include_once('includes/custom-functions.php');
$fn = new custom_functions;

if (isset($_POST['btnAdd'])) {

    $name = $db->escapeString($_POST['name']);
    $description = $db->escapeString($_POST['description']);
    $demo_video = $db->escapeString($_POST['demo_video']);
    $price = $db->escapeString($_POST['price']);
    $invite_bonus = $db->escapeString($_POST['invite_bonus']);
    $num_sync = $db->escapeString($_POST['num_sync']);

    $error = array();

    if (empty($name)) $error['name'] = " <span class='label label-danger'>Required!</span>";
    if (empty($description)) $error['description'] = " <span class='label label-danger'>Required!</span>";
    if (empty($demo_video)) $error['demo_video'] = " <span class='label label-danger'>Required!</span>";
    if (empty($invite_bonus)) $error['invite_bonus'] = " <span class='label label-danger'>Required!</span>";

    if ($_FILES['image']['size'] != 0 && $_FILES['image']['error'] == 0 && !empty($_FILES['image'])) {
        $extension = pathinfo($_FILES["image"]["name"])["extension"];
        $result = $fn->validate_image($_FILES["image"]);
        $target_path = 'upload/images/';
        $filename = microtime(true) . '.' . strtolower($extension);
        $full_path = $target_path . $filename;

        if (!move_uploaded_file($_FILES["image"]["tmp_name"], $full_path)) {
            echo '<p class="alert alert-danger">Cannot upload image.</p>';
            echo '<pre>';
            print_r(error_get_last());
            print_r($_FILES["image"]);
            echo '</pre>';
            return false;
            exit();
        }
        
        $upload_image = 'upload/images/' . $filename;
        $sql = "INSERT INTO plan (name, description, image, demo_video, price, invite_bonus, num_sync) VALUES ('$name', '$description', '$upload_image', '$demo_video', '$price', '$invite_bonus', '$num_sync')";
        $db->sql($sql);
    } else {
        $sql = "INSERT INTO plan (name, description, demo_video, price, invite_bonus, num_sync) VALUES ('$name', '$description', '$demo_video', '$price', '$invite_bonus', '$num_sync')";
        $db->sql($sql);
    }

    $result = $db->getResult();
    if (!empty($result)) {
        $error['add_languages'] = " <span class='label label-danger'>Failed</span>";
    } else {
        $error['add_languages'] = "<section class='content-header'><span class='label label-success'>Plan Added Successfully</span></section>";
    }
}
?>

<section class="content-header">
    <h1>Add New Plan <small><a href='plan.php'><i class='fa fa-angle-double-left'></i>&nbsp;&nbsp;&nbsp;Back to Plan</a></small></h1>
    <?php echo isset($error['add_languages']) ? $error['add_languages'] : ''; ?>
    <ol class="breadcrumb">
        <li><a href="reports.php"><i class="fa fa-home"></i> Home</a></li>
    </ol>
    <hr />
</section>
<section class="content">
    <div class="row">
        <div class="col-md-10">
            <div class="box box-primary">
                <form method="post" enctype="multipart/form-data">
                    <div class="box-body">
                        <div class="row">
                            <div class='col-md-3'>
                                <label>Name</label> <i class="text-danger asterik">*</i>
                                <input type="text" class="form-control" name="name" required>
                            </div>
                            <div class='col-md-3'>
                                <label>Demo Video</label> <i class="text-danger asterik">*</i>
                                <input type="text" class="form-control" name="demo_video" required>
                            </div>
                            <div class='col-md-3'>
                                <label>Price</label> <i class="text-danger asterik">*</i>
                                <input type="number" class="form-control" name="price">
                            </div>
                            <div class='col-md-3'>
                                <label>Invite Bonus</label> <i class="text-danger asterik">*</i>
                                <input type="number" class="form-control" name="invite_bonus" required>
                            </div>
                        </div>
                        <br>
                        <div class="row">
                            <div class='col-md-3'>
                                <label>Image</label> <i class="text-danger asterik">*</i>
                                <input type="file" name="image" onchange="readURL(this);" accept="image/png, image/jpeg" required>
                                <br>
                                <img id="blah" src="#" alt="" style="display: none; max-height: 200px; max-width: 200px;" />
                            </div>
                            <div class='col-md-3'>
                                <label>Num Sync</label> <i class="text-danger asterik">*</i>
                                <input type="number" class="form-control" name="num_sync">
                            </div>
                        </div>
                        <br>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>Description</label> <i class="text-danger asterik">*</i>
                                    <textarea name="description" id="description" class="form-control" rows="8"></textarea>
                                    <script src="css/js/ckeditor/ckeditor.js"></script>
                                    <script>
                                        CKEDITOR.replace('description');
                                    </script>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="box-footer">
                        <button type="submit" class="btn btn-primary" name="btnAdd">Submit</button>
                        <input type="reset" onClick="refreshPage()" class="btn-warning btn" value="Clear" />
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>

<script>
function readURL(input) {
    if (input.files && input.files[0]) {
        var reader = new FileReader();
        reader.onload = function (e) {
            document.getElementById('blah').src = e.target.result;
            document.getElementById('blah').style.display = 'block';
        };
        reader.readAsDataURL(input.files[0]);
    }
}
function refreshPage() {
    window.location.reload();
}
</script>

<?php $db->disconnect(); ?>
