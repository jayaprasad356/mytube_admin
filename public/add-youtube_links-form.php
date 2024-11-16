<?php
include_once('includes/functions.php');
$function = new functions;
include_once('includes/custom-functions.php');
$fn = new custom_functions;


?>
<?php
if (isset($_POST['btnAdd'])) {

        $name = $db->escapeString(($_POST['name']));
        $link = $db->escapeString(($_POST['link']));
        $error = array();
       
        if (empty($name)) {
            $error['name'] = " <span class='label label-danger'>Required!</span>";
        }
        if (empty($link)) {
            $error['link'] = " <span class='label label-danger'>Required!</span>";
        }
       
       if (!empty($name) && !empty($link) ) 
       {
            $sql_query = "INSERT INTO youtube_link (name,link)VALUES('$name','$link')";
            $db->sql($sql_query);
            $result = $db->getResult();
            if (!empty($result)) {
                $result = 0;
            } else {
                $result = 1;
            }
            if ($result == 1) {
                
                $error['add_faq'] = "<section class='content-header'>
                                                <span class='label label-success'>Youtube Links Added Successfully</span> </section>";
            } else {
                $error['add_faq'] = " <span class='label label-danger'>Failed</span>";
           }
        }
    }
?>
<section class="content-header">
    <h1>Add New Youtube Links <small><a href='youtube_links.php'> <i class='fa fa-angle-double-left'></i>&nbsp;&nbsp;&nbsp;Back to Youtube Links</a></small></h1>

    <?php echo isset($error['add_faq']) ? $error['add_faq'] : ''; ?>
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
                <form url="add_faq_form" method="post" enctype="multipart/form-data">
                    <div class="box-body">
                            <div class="form-group">
                                    <label for="exampleInputEmail1">Name</label> <i class="text-danger asterik">*</i>
                                    <input type="text" class="form-control" name="name" required>

                            </div>
                        <br>
                            <div class="form-group">
                                    <label for="exampleInputEmail1">Links</label> <i class="text-danger asterik">*</i>
                                    <input type="text" class="form-control" name="link" required>
                            </div>
                    </div>
                  
                    <!-- /.box-body -->

                    <div class="box-footer">
                        <button type="submit" class="btn btn-primary" name="btnAdd">Add</button>
                        <input type="reset" onClick="refreshPage()" class="btn-warning btn" value="Clear" />
                    </div>

                </form>

            </div><!-- /.box -->
        </div>
    </div>
</section>

<div class="separator"> </div>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.17.0/jquery.validate.min.js"></script>
<script>
    $('#add_faq_form').validate({

        ignore: [],
        debug: false,
        rules: {
            question: "required",
            answer: "required",
        }
    });
    $('#btnClear').on('click', function() {
        for (instance in CKEDITOR.instances) {
            CKEDITOR.instances[instance].setData('');
        }
    });
</script>

<!--code for page clear-->
<script>
    function refreshPage(){
    window.location.reload();
} 
</script>

<?php $db->disconnect(); ?>