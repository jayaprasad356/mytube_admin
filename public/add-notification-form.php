

<?php
include_once('includes/functions.php');
$function = new functions;
include_once('includes/custom-functions.php');
$fn = new custom_functions;

// OneSignal Credentials
define('ONESIGNAL_APP_ID', '26ce46d3-9d0a-45c1-93b6-bf0c60b43c5a'); 
define('ONESIGNAL_REST_API_KEY', 'os_v2_app_e3henu45bjc4de5wx4ggbnb4ljv5gqfnwnjusz4k7dnjzpqi4sw2opgky7z7qliphmiekgex54kfhnjg2oytqlw4tgqrgt6ktpyc3ra'); 

function sendOneSignalNotification($title, $description, $image_url = '') {
    $content = array("en" => $description);
    $headings = array("en" => $title);

    $fields = array(
        'app_id' => ONESIGNAL_APP_ID,
        'included_segments' => array('All'),
        'headings' => $headings,
        'contents' => $content,
    );

    if (!empty($image_url)) {
        $fields['big_picture'] = $image_url;
    }

    $fields_json = json_encode($fields);

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, "https://onesignal.com/api/v1/notifications");
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        'Content-Type: application/json; charset=utf-8',
        'Authorization: Basic ' . ONESIGNAL_REST_API_KEY
    ));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
    curl_setopt($ch, CURLOPT_POSTFIELDS, $fields_json);

    $response = curl_exec($ch);
    $error = curl_error($ch);

    if ($error) {
        // Log any cURL errors
        error_log("cURL Error: " . $error);
        echo "Error: " . $error;
    } else {
        // Log OneSignal response
        error_log("OneSignal Response: " . $response);
        echo $response;
    }

    curl_close($ch);
    return $response;
}


if (isset($_POST['btnAdd'])) {
    $title = $db->escapeString($_POST['title']);
    $description = $db->escapeString($_POST['description']);
    $link = $db->escapeString($_POST['link']);
    $error = array();

    // Input validation
    if (empty($title)) {
        $error['title'] = "<span class='label label-danger'>Required!</span>";
    }
    if (empty($description)) {
        $error['description'] = "<span class='label label-danger'>Required!</span>";
    }
    if (empty($link)) {
        $error['link'] = "<span class='label label-danger'>Required!</span>";
    }

    // Initialize default values
    $upload_image = 'dist/img/icon.jpeg'; // Default image path
    $current_datetime = date('Y-m-d H:i:s');

    // Handle image upload
    if ($_FILES['image']['size'] != 0 && $_FILES['image']['error'] == 0 && !empty($_FILES['image'])) {
        $extension = pathinfo($_FILES["image"]["name"], PATHINFO_EXTENSION);
        $result = $fn->validate_image($_FILES["image"]);
        $target_path = 'upload/images/';

        $filename = microtime(true) . '.' . strtolower($extension);
        $full_path = $target_path . $filename;

        if (move_uploaded_file($_FILES["image"]["tmp_name"], $full_path)) {
            $upload_image = $full_path;
        } else {
            echo '<p class="alert alert-danger">Cannot upload image.</p>';
            return false;
        }
    }

    // Attempt to send OneSignal notification
    $response = sendOneSignalNotification($title, $description, $upload_image);
    $response_data = json_decode($response, true); // Decode JSON response

    if (isset($response_data['id'])) {
        // Notification sent successfully, now insert into database
        $sql = "INSERT INTO notifications (title, description, image, link, datetime) 
                VALUES ('$title', '$description', '$upload_image', '$link', '$current_datetime')";
        $db->sql($sql);

        $result = $db->getResult();
        if (empty($result)) {
            $error['add_languages'] = "<section class='content-header'>
                                            <span class='label label-success'>Notification Added Successfully</span>
                                       </section>";
        } else {
            $error['add_languages'] = "<span class='label label-danger'>Failed to Insert Notification into Database</span>";
        }
    } else {
        // Notification failed to send
        echo '<p class="alert alert-danger">Failed to send notification. Error: ' . $response . '</p>';
    }
}

?>

<section class="content-header">
    <h1>Add New Notification <small><a href='notifications.php'> <i class='fa fa-angle-double-left'></i>&nbsp;&nbsp;&nbsp;Back to Notification</a></small></h1>

    <?php echo isset($error['add_languages']) ? $error['add_languages'] : ''; ?>
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
                <form url="add-languages-form" method="post" enctype="multipart/form-data">
                    <div class="box-body">
                       <div class="row">
                            <div class="form-group">
                                <div class='col-md-6'>
                                    <label for="exampleInputtitle">Title</label> <i class="text-danger asterik">*</i>
                                    <input type="text" class="form-control" name="title" required>
                                </div>
                                <div class='col-md-6'>
                                    <label for="exampleInputtitle">Link</label> <i class="text-danger asterik">*</i>
                                    <input type="text" class="form-control" name="link">
                                </div>
                            </div>
                        </div>
                        <br>    
                        <div class="row">
                            <div class="form-group">
                            <div class='col-md-6'>
                                    <label for="exampleInputtitle">Description</label> <i class="text-danger asterik">*</i>
                                    <textarea type="text" rows="3" class="form-control" name="description" required></textarea>
                                </div>
                                 <div class="col-md-3">
                                    <label for="exampleInputFile">Image</label> <i class="text-danger asterisk">*</i><?php echo isset($error['image']) ? $error['image'] : ''; ?>
                                    <input type="file" name="image" onchange="readURL(this);" accept="image/png, image/jpeg" id="image"/><br>
                                    <img id="blah" src="#" alt="" style="display: none; max-height: 200px; max-width: 200px;" /> <!-- Adjust max-height and max-width as needed -->
                                 </div>
                            </div> 
                        </div> 
                        <br>
      
                    <!-- /.box-body -->

                    <div class="box-footer">
                        <button type="submit" class="btn btn-primary" name="btnAdd">Submit</button>
                        <input type="reset" onClick="refreshPage()" class="btn-warning btn" value="Clear" />
                    </div>

                </form>
                <div id="result"></div>

            </div><!-- /.box -->
        </div>
    </div>
</section>
<div class="separator"> </div>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.17.0/jquery.validate.min.js"></script>
<script>
    $('#add_leave_form').validate({

        ignore: [],
        debug: false,
        rules: {
        reason: "required",
            date: "required",
        }
    });
    $('#btnClear').on('click', function() {
        for (instance in CKEDITOR.instances) {
            CKEDITOR.instances[instance].setData('');
        }
    });
</script>
<script>
    $(document).ready(function () {
        $('#user_id').select2({
        width: 'element',
        placeholder: 'Type in name to search',

    });
    });

    if ( window.history.replaceState ) {
  window.history.replaceState( null, null, window.location.href );
}

</script>

<!--code for page clear-->
<script>
    function refreshPage(){
    window.location.reload();
} 
</script>
<script>
function readURL(input) {
    if (input.files && input.files[0]) {
        var reader = new FileReader();

        reader.onload = function (e) {
            // Set the source of the image to the selected file
            document.getElementById('blah').src = e.target.result;
            // Display the image by changing its style to block
            document.getElementById('blah').style.display = 'block';
        };

        reader.readAsDataURL(input.files[0]);
    }
}
</script>
<script>
    var changeCheckbox = document.querySelector('#stock_button');
    var init = new Switchery(changeCheckbox);
    changeCheckbox.onchange = function() {
        if ($(this).is(':checked')) {
            $('#stock').val(1);

        } else {
            $('#stock').val(0);
        }
    };
</script>
<?php $db->disconnect(); ?>

