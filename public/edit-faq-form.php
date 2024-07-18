<?php
include_once('includes/functions.php');
$function = new functions;
include_once('includes/custom-functions.php');
$fn = new custom_functions;
?>
<?php

if (isset($_GET['id'])) {
    $ID = $db->escapeString($_GET['id']);
} else {
    // $ID = "";
    return false;
    exit(0);
}
if (isset($_POST['btnEdit'])) {
            // $datetime = date('Y-m-d H:i:s');
            // $date = date('Y-m-d');
            $question = $db->escapeString(($_POST['question']));
            $answer = $db->escapeString(($_POST['answer']));
            $error = array();

     if (!empty($question) && !empty($answer)) {
    
        $sql_query = "UPDATE faq SET question='$question', answer='$answer' WHERE id =  $ID";
        $db->sql($sql_query);
        $update_result = $db->getResult();
        if (!empty($update_result)) {
            $update_result = 0;
        } else {
            $update_result = 1;
        }

        // check update result
        if ($update_result == 1) {
            $error['update_faq'] = " <section class='content-header'><span class='label label-success'>FAQ updated Successfully</span></section>";
        } else {
            $error['update_faq'] = " <span class='label label-danger'>Failed update FAQ</span>";
        }


    }
}


// create array variable to store previous data
$data = array();

$sql_query = "SELECT * FROM faq WHERE id =" . $ID;
$db->sql($sql_query);
$res = $db->getResult();
if (isset($_POST['btnCancel'])) { ?>
    <script>
        window.location.href = "faq.php";
    </script>
<?php } ?>
<section class="content-header">
    <h1>
        Edit FAQ<small><a href='faq.php'><i class='fa fa-angle-double-left'></i>&nbsp;&nbsp;&nbsp;Back to FAQ's</a></small></h1>
    <small><?php echo isset($error['update_faq']) ? $error['update_faq'] : ''; ?></small>
    <ol class="breadcrumb">
        <li><a href="home.php"><i class="fa fa-home"></i> Home</a></li>
    </ol>
</section>
<section class="content">
    <!-- Main row -->

    <div class="row">
        <div class="col-md-10">

            <!-- general form elements -->
            <div class="box box-primary">
                <div class="box-header with-border">
                </div>
                <form id="edit_user_form" method="post" enctype="multipart/form-data">
                    <div class="box-body">
                        <div class="form-group">
                                <label for="exampleInputEmail1">Question</label> <i class="text-danger asterik">*</i>
                                <input type="text" class="form-control" name="question" value="<?php echo $res[0]['question']; ?>">
                        </div>
                        <br>
                        <div class="form-group">
                                <label for="exampleInputEmail1">Answer</label><i class="text-danger asterik">*</i>
                                <textarea type="text" rows="3" class="form-control" name="answer"><?php echo $res[0]['answer']; ?></textarea>
                        </div>

                    </div><!-- /.box-body -->

                    <div class="box-footer">
                        <button type="submit" class="btn btn-primary" name="btnEdit">Update</button>

                    </div>
                </form>
            </div><!-- /.box -->
        </div>
    </div>
</section>

<div class="separator"> </div>
<?php $db->disconnect(); ?>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.17.0/jquery.validate.min.js"></script>
