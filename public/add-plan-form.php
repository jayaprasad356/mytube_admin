<?php
include_once('includes/functions.php');
$function = new functions;
include_once('includes/custom-functions.php');
$fn = new custom_functions;
?>
<?php
if (isset($_POST['btnAdd'])) {

        $name = $db->escapeString(($_POST['name']));
        $description = $db->escapeString(($_POST['description']));
        $demo_video = $db->escapeString(($_POST['demo_video']));
        $monthly_codes = $db->escapeString(($_POST['monthly_codes']));
        $monthly_earnings = $db->escapeString(($_POST['monthly_earnings']));
        $per_code_cost = $db->escapeString(($_POST['per_code_cost']));
        $price = $db->escapeString(($_POST['price']));
        $type = $db->escapeString(($_POST['type']));
        $min_refers = $db->escapeString(($_POST['min_refers']));
        $invite_bonus = $db->escapeString(($_POST['invite_bonus']));
        $num_sync = $db->escapeString(($_POST['num_sync']));
        $sub_description = $db->escapeString(($_POST['sub_description']));
        $active_link = $db->escapeString(($_POST['active_link']));
        $refund = $db->escapeString(($_POST['refund']));
        $refer_refund_amount = $db->escapeString(($_POST['refer_refund_amount']));
   
        $error = array();
       
        if (empty($name)) {
            $error['name'] = " <span class='label label-danger'>Required!</span>";
        }
        if (empty($description)) {
            $error['description'] = " <span class='label label-danger'>Required!</span>";
        }
        if (empty($demo_video)) {
            $error['demo_video'] = " <span class='label label-danger'>Required!</span>";
        }
        if (empty($monthly_codes)) {
            $error['monthly_codes'] = " <span class='label label-danger'>Required!</span>";
        }
        if (empty($monthly_earnings)) {
            $error['monthly_earnings'] = " <span class='label label-danger'>Required!</span>";
        }
        if (empty($per_code_cost)) {
            $error['per_code_cost'] = " <span class='label label-danger'>Required!</span>";
        }
        if (empty($invite_bonus)) {
            $error['invite_bonus'] = " <span class='label label-danger'>Required!</span>";
        }
        if (empty($sub_description)) {
            $error['sub_description'] = " <span class='label label-danger'>Required!</span>";
        }
        if (empty($active_link)) {
            $error['active_link'] = " <span class='label label-danger'>Required!</span>";
        }
  
  
       
            // Validate and process the image upload
    if ($_FILES['image']['size'] != 0 && $_FILES['image']['error'] == 0 && !empty($_FILES['image'])) {
        $extension = pathinfo($_FILES["image"]["name"])['extension'];

        $result = $fn->validate_image($_FILES["image"]);
        $target_path = 'upload/images/';

        $filename = microtime(true) . '.' . strtolower($extension);
        $full_path = $target_path . "" . $filename;

        if (!move_uploaded_file($_FILES["image"]["tmp_name"], $full_path)) {
            echo '<p class="alert alert-danger">Can not upload image.</p>';
            return false;
            exit();
        }

        $upload_image = 'upload/images/' . $filename;
        $sql = "INSERT INTO plan (name,description,image,demo_video,monthly_codes,per_code_cost,price,monthly_earnings,type,min_refers,invite_bonus,num_sync,sub_description,active_link,refund,refer_refund_amount) VALUES ('$name','$description','$upload_image','$demo_video','$monthly_codes','$per_code_cost','$price','$monthly_earnings','$type','$min_refers','$invite_bonus','$num_sync','$sub_description','$active_link','$refund','$refer_refund_amount')";
        $db->sql($sql);
    } else {
            $sql_query = "INSERT INTO plan (name,description,demo_video,monthly_codes,per_code_cost,price,monthly_earnings,type,min_refers,invite_bonus,num_sync,sub_description,active_link,refund,refer_refund_amount) VALUES ('$name','$description','$demo_video','$monthly_codes','$per_code_cost','$price','$monthly_earnings','$type','$min_refers','$invite_bonus','$num_sync','$sub_description','$active_link','$refund','$refer_refund_amount')";
            $db->sql($sql);
        }
            $result = $db->getResult();
            if (!empty($result)) {
                $result = 0;
            } else {
                $result = 1;
            }

            if ($result == 1) {
                
                $error['add_languages'] = "<section class='content-header'>
                                                <span class='label label-success'>Plan Added Successfully</span> </section>";
            } else {
                $error['add_languages'] = " <span class='label label-danger'>Failed</span>";
            }
     }
        
?>
<section class="content-header">
    <h1>Add New Plan <small><a href='plan.php'> <i class='fa fa-angle-double-left'></i>&nbsp;&nbsp;&nbsp;Back to Plan</a></small></h1>

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
                                <div class='col-md-3'>
                                    <label for="exampleInputtitle">Name</label> <i class="text-danger asterik">*</i>
                                    <input type="text" class="form-control" name="name" required>
                                </div>
                                <div class='col-md-3'>
                                    <label for="exampleInputtitle">Demo Video</label> <i class="text-danger asterik">*</i>
                                    <input type="text" class="form-control" name="demo_video" required>
                                </div>
                                <div class='col-md-3'>
                                    <label for="exampleInputtitle">Price</label> <i class="text-danger asterik">*</i>
                                    <input type="number" class="form-control" name="price">
                                </div>
                                <div class='col-md-3'>
                                    <label for="exampleInputtitle">Monthly Earnings</label> <i class="text-danger asterik">*</i>
                                    <input type="number" class="form-control" name="monthly_earnings" required>
                                </div>
                            </div>
                        </div>
                        <br>
                        <div class="row">
                            <div class="form-group">
                                 <div class="col-md-3">
                                    <label for="exampleInputFile">Image</label> <i class="text-danger asterisk">*</i><?php echo isset($error['image']) ? $error['image'] : ''; ?>
                                    <input type="file" name="image" onchange="readURL(this);" accept="image/png, image/jpeg" id="image" required/><br>
                                    <img id="blah" src="#" alt="" style="display: none; max-height: 200px; max-width: 200px;" /> <!-- Adjust max-height and max-width as needed -->
                                 </div>
                                 <div class='col-md-3'>
                                    <label for="exampleInputtitle">Invite Bonus</label> <i class="text-danger asterik">*</i>
                                    <input type="number" class="form-control" name="invite_bonus" required>
                                </div>
                                 <div class='col-md-3'>
                                    <label for="exampleInputtitle">Per Code Cost</label> <i class="text-danger asterik">*</i>
                                    <input type="number" class="form-control" name="per_code_cost" required>
                                </div>
                                <div class='col-md-3'>
                                <label for="exampleInputEmail1">Select Type</label> <i class="text-danger asterik">*</i><?php echo isset($error['type']) ? $error['type'] : ''; ?>
                                    <select id='type' name="type" class='form-control'>
                                    <option value='jobs'>jobs</option>
                                      <option value='senior_jobs'>senior_jobs</option>
                                    </select>
                                </div>
                            </div> 
                        </div> 
                        <br>
                        <div class="row">
                            <div class="form-group">
                                 <div class='col-md-3'>
                                    <label for="exampleInputtitle">Min Refers</label> <i class="text-danger asterik">*</i>
                                    <input type="number" class="form-control" name="min_refers">
                                </div>
                                <div class='col-md-3'>
                                    <label for="exampleInputtitle">Monthly Earnings</label> <i class="text-danger asterik">*</i>
                                    <input type="number" class="form-control" name="monthly_earings">
                                </div>
                                <div class='col-md-3'>
                                    <label for="exampleInputtitle">Num Sync</label> <i class="text-danger asterik">*</i>
                                    <input type="number" class="form-control" name="num_sync">
                                </div>
                                <div class='col-md-3'>
                                    <label for="exampleInputtitle">Refund</label> <i class="text-danger asterik">*</i>
                                    <input type="number" step="0.01" class="form-control" name="refund">
                                </div>
                            </div> 
                        </div> 
                        <br>
                        <div class="row">
                            <div class="form-group">
                                 <div class='col-md-3'>
                                    <label for="exampleInputtitle">Sub Description</label> <i class="text-danger asterik">*</i>
                                    <input type="text" class="form-control" name="sub_description">
                                </div>
                                <div class='col-md-3'>
                                    <label for="exampleInputtitle">Active Link</label> <i class="text-danger asterik">*</i>
                                    <input type="text" class="form-control" name="active_link">
                                </div>
                                <div class='col-md-3'>
                                    <label for="exampleInputtitle">Refer Refund Amount</label> <i class="text-danger asterik">*</i>
                                    <input type="number" step="0.01" class="form-control" name="refer_refund_amount">
                                </div>
                            </div> 
                        </div> 
                        <br>
                        <div class="row">
                           <div class="col-md-12">
                                <div class="form-group">
                                   <label for="description">Description :</label> <i class="text-danger asterik">*</i><?php echo isset($error['description']) ? $error['description'] : ''; ?>
                                    <textarea name="description" id="description" class="form-control" rows="8"></textarea>
                                    <script type="text/javascript" src="css/js/ckeditor/ckeditor.js"></script>
                                    <script type="text/javascript">
                                       CKEDITOR.replace('description');
                                    </script>
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
