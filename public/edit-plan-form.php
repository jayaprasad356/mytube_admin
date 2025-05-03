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

    $name = $db->escapeString(($_POST['name']));
    $description = $db->escapeString(($_POST['description']));
    $demo_video = $db->escapeString(($_POST['demo_video']));
    $price = $db->escapeString(($_POST['price']));
    $invite_bonus = $db->escapeString(trim($_POST['invite_bonus']));
    $num_sync = $db->escapeString(trim($_POST['num_sync']));

    $sql_query = "UPDATE plan SET name='$name', description='$description', demo_video='$demo_video', price='$price', invite_bonus='$invite_bonus', num_sync='$num_sync' WHERE id = $ID";
    $db->sql($sql_query);
    $result = $db->getResult();             
    if (!empty($result)) {
        $error['update_languages'] = " <span class='label label-danger'>Failed</span>";
    } else {
        $error['update_languages'] = " <span class='label label-success'>Plan Updated Successfully</span>";
    }

    if ($_FILES['image']['size'] != 0 && $_FILES['image']['error'] == 0 && !empty($_FILES['image'])) {
        $old_image = $db->escapeString($_POST['old_image']);
        $extension = pathinfo($_FILES["image"]["name"])["extension"];

        $result = $fn->validate_image($_FILES["image"]);
        $target_path = 'upload/images/';

        $filename = microtime(true) . '.' . strtolower($extension);
        $full_path = $target_path . $filename;
        if (!move_uploaded_file($_FILES["image"]["tmp_name"], $full_path)) {
            echo '<p class="alert alert-danger">Cannot upload image.</p>';
            return false;
            exit();
        }
        if (!empty($old_image) && file_exists($old_image)) {
            unlink($old_image);
        }

        $upload_image = 'upload/images/' . $filename;
        $sql = "UPDATE plan SET image='$upload_image' WHERE id='$ID'";
        $db->sql($sql);

        $update_result = $db->getResult();
        if (!empty($update_result)) {
            $update_result = 0;
        } else {
            $update_result = 1;
        }

        if ($update_result == 1) {
            $error['update_languages'] = " <section class='content-header'><span class='label label-success'>Image updated Successfully</span></section>";
        } else {
            $error['update_languages'] = " <span class='label label-danger'>Failed to update image</span>";
        }
    }
}

$sql_query = "SELECT * FROM plan WHERE id =" . $ID;
$db->sql($sql_query);
$res = $db->getResult();

if (isset($_POST['btnCancel'])) { ?>
    <script>
        window.location.href = "plan.php";
    </script>
<?php } ?>

<section class="content-header">
    <h1>
        Edit Plan <small><a href='plan.php'><i class='fa fa-angle-double-left'></i>&nbsp;&nbsp;&nbsp;Back to Plan</a></small>
    </h1>
    <small><?php echo isset($error['update_languages']) ? $error['update_languages'] : ''; ?></small>
    <ol class="breadcrumb">
        <li><a href="reports.php"><i class="fa fa-home"></i> Home</a></li>
    </ol>
</section>
<section class="content">
    <div class="row">
        <div class="col-md-10">
            <div class="box box-primary">
                <div class="box-header with-border"></div>
                <form id="edit_plan_form" method="post" enctype="multipart/form-data">
                    <div class="box-body">
                        <input type="hidden" name="old_image" value="<?php echo isset($res[0]['image']) ? $res[0]['image'] : ''; ?>">
                        <div class="row">
                            <div class="col-md-3">
                                <label>Name</label><i class="text-danger asterik">*</i>
                                <input type="text" class="form-control" name="name" value="<?php echo $res[0]['name']; ?>">
                            </div>
                            <div class="col-md-3">
                                <label>Demo Video</label><i class="text-danger asterik">*</i>
                                <input type="text" class="form-control" name="demo_video" value="<?php echo $res[0]['demo_video']; ?>">
                            </div>
                            <div class="col-md-3">
                                <label>Price</label><i class="text-danger asterik">*</i>
                                <input type="number" class="form-control" name="price" value="<?php echo $res[0]['price']; ?>">
                            </div>
                            <div class="col-md-3">
                                <label>Invite Bonus</label><i class="text-danger asterik">*</i>
                                <input type="number" class="form-control" name="invite_bonus" value="<?php echo $res[0]['invite_bonus']; ?>">
                            </div>
                        </div>
                        <br>
                        <div class="row">
                            <div class="col-md-3">
                                <label>Image</label><i class="text-danger asterik">*</i>
                                <input type="file" name="image" onchange="readURL(this);" accept="image/png, image/jpeg" />
                                <br>
                                <img id="blah" src="<?php echo $res[0]['image']; ?>" alt="" width="150" height="200" <?php echo empty($res[0]['image']) ? 'style="display: none;"' : ''; ?> />
                            </div>
                            <div class="col-md-3">
                                <label>Num Sync</label><i class="text-danger asterik">*</i>
                                <input type="number" class="form-control" name="num_sync" value="<?php echo $res[0]['num_sync']; ?>">
                            </div>
                        </div>
                        <br>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>Description</label> <i class="text-danger asterik">*</i>
                                    <textarea name="description" id="description" class="form-control" rows="8"><?php echo $res[0]['description']; ?></textarea>
                                    <script type="text/javascript" src="css/js/ckeditor/ckeditor.js"></script>
                                    <script type="text/javascript">
                                        CKEDITOR.replace('description');
                                    </script>
                                </div>
                            </div>
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
    function readURL(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function (e) {
                $('#blah').attr('src', e.target.result).width(150).height(200).css('display', 'block');
            };
            reader.readAsDataURL(input.files[0]);
        }
    }
</script>
