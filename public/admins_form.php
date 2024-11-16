<?php

include_once('includes/crud.php');
$db = new Database();
$db->connect();
$db->sql("SET NAMES 'utf8'");

include('includes/variables.php');
include_once('includes/custom-functions.php');

$fn = new custom_functions;
// $config = $fn->get_configurations();
$permissions = $fn->get_permissions($_SESSION['id']);
?>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.16.0/jquery.validate.min.js"></script>
<style>
    table {
        font-family: arial, sans-serif;
        border-collapse: collapse;
        width: 100%;
    }

    td,
    th {
        border: 1px solid #dddddd;
        text-align: left;
        padding: 8px;
    }

    tr:nth-child(even) {
        background-color: #dddddd;
    }
</style>
<?php
if ($_SESSION['role'] == 'editor') {
    echo "<p class='alert alert-danger topmargin-sm'>Access denied - You are not authorized to access this page.</p>";
    return false;
}
?>
<section class="content-header">
    <h1>Create admin <small><a href="reports.php"><i class="fa fa-home"></i> Home</a></small></h1>

</section>
<!-- Main content -->
<section class="content">
    <!-- Main row -->
    <div class="row">
        <div class="col-md-6">
            <!-- general form elements -->
            <div class="box box-primary">
                <div class="box-header with-border">
                    <h3 class="box-title">Add Admin </h3>

                </div><!-- /.box-header -->
                <!-- form start -->
                <form method="post" id="add_form" action="public/db-operation.php">
                    <input type="hidden" id="add_system_user" name="add_system_user" value="1">
                    <div class="box-body">
                        <div class="form-group">
                            <label for="">Username</label>
                            <input type="text" class="form-control" name="username">
                        </div>
                        <div class="form-group">
                            <label for="">Email</label>
                            <input type="text" class="form-control" name="email">
                        </div>
                        <div class="form-group">
                            <label for="">Refer Code</label>
                            <input type="text" class="form-control" name="refer_code">
                        </div>
                        <div class="form-group">
                            <label for="">Password</label>
                            <input type="password" class="form-control" name="password" id="password">
                        </div>
                        <div class="form-group">
                            <label for="">Confirm Password</label>
                            <input type="password" class="form-control" name="confirm_password">
                        </div>
                        <div class="form-group">
                            <label for="">Role</label>
                            <select name="role" class="form-control">
                                <option value="">---Select---</option>
                                <option value="Admin">Admin</option>
                                <option value="editor">Editor</option>
                            </select>
                        </div>

                    </div><!-- /.box-body -->


                    <div class="box-footer">
                        <button type="submit" class="btn btn-primary" id="submit_btn" name="btnAdd">Add</button>
                        <input type="reset" class="btn-warning btn" value="Clear" />

                    </div>
                    <div class="form-group">

                        <div id="result" style="display: none;"></div>
                    </div>

            </div><!-- /.box -->
            <?php
            if ($_SESSION['role'] != 'editor') { ?>
                <div class="box">
                    <div class="box-header">
                        <h3 class="box-title">Multiple Admin</h3>
                    </div>
                    <div class="box-body table-responsive">
                        <table class="table table-hover" data-toggle="table" id="system-users" data-url="api-firebase/get-bootstrap-table-data.php?table=system-users" data-page-list="[5, 10, 20, 50, 100, 200]" data-show-refresh="true" data-show-columns="true" data-side-pagination="server" data-pagination="true" data-search="true" data-trim-on-search="false" data-sort-name="id" data-sort-order="asc">
                            <thead>
                                <tr>
                                    <th data-field="id" data-sortable="true">ID</th>
                                    <th data-field="username" data-sortable="true">Username</th>
                                    <th data-field="email" data-sortable="true">Email</th>
                                    <th data-field="password" data-sortable="true">Password</th>
                                    <th data-field="refer_code" data-sortable="true">Refer Code</th>
                                    <th data-field="role" data-sortable="true">Role</th>
                                    <th data-field="created_by" data-sortable="true" data-visible="false">Created By</th>
                                    <th data-field="created_by_id" data-sortable="true" data-visible="false">Created By Id</th>
                                    <th data-field="date_created" data-visible="false">Date Created</th>
                                    <th data-field="operate" data-events="actionEvents">Action</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            <?php } ?>
        </div>
        <div class="col-xs-6">
            <div class="box box-primary">
                <table>
                    <tr>
                        <th>Module/Permissions</th>
                        <th>Create</th>
                        <th>Read</th>
                        <th>Update</th>
                        <th>Delete</th>
                    </tr>
                    <tr>
                        <td>Users</td>
                        <td><input type="checkbox" id="create-user-button" class="js-switch" checked>
                            <input type="hidden" id="is-create-user" name="is-create-user" value="1">
                        </td>
                        <td><input type="checkbox" id="read-user-button" class="js-switch" checked>
                            <input type="hidden" id="is-read-user" name="is-read-user" value="1">
                        </td>
                        <td><input type="checkbox" id="update-user-button" class="js-switch" checked>
                            <input type="hidden" id="is-update-user" name="is-update-user" value="1">
                        </td>
                        <td><input type="checkbox" id="delete-user-button" class="js-switch" checked>
                            <input type="hidden" id="is-delete-user" name="is-delete-user" value="1">
                        </td>
                    </tr>
                    <tr>
                        <td>Transactions</td>
                        <td><input type="checkbox" id="create-transaction-button" class="js-switch" checked>
                            <input type="hidden" id="is-create-transaction" name="is-create-transaction" value="1">
                        </td>
                        <td><input type="checkbox" id="read-transaction-button" class="js-switch" checked>
                            <input type="hidden" id="is-read-transaction" name="is-read-transaction" value="1">
                        </td>
                        <td><input type="checkbox" id="update-transaction-button" class="js-switch" checked>
                            <input type="hidden" id="is-update-transaction" name="is-update-transaction" value="1">
                        </td>
                        <td><input type="checkbox" id="delete-transaction-button" class="js-switch" checked>
                            <input type="hidden" id="is-delete-transaction" name="is-delete-transaction" value="1">
                        </td>
                    </tr>
                    <tr>
                        <td>Withdrawals</td>
                        <td><input type="checkbox" id="create-withdrawal-button" class="js-switch" checked>
                            <input type="hidden" id="is-create-withdrawal" name="is-create-withdrawal" value="1">
                        </td>
                        <td><input type="checkbox" id="read-withdrawal-button" class="js-switch" checked>
                            <input type="hidden" id="is-read-withdrawal" name="is-read-withdrawal" value="1">
                        </td>
                        <td><input type="checkbox" id="update-withdrawal-button" class="js-switch" checked>
                            <input type="hidden" id="is-update-withdrawal" name="is-update-withdrawal" value="1">
                        </td>
                        <td><input type="checkbox" id="delete-withdrawal-button" class="js-switch" checked>
                            <input type="hidden" id="is-delete-withdrawal" name="is-delete-withdrawal" value="1">
                        </td>
                    </tr>
               

                </table>

            </div>
            </form>
        </div>
        <!-- Left col -->

        <div class="separator"> </div>
    </div>
    <div class="modal fade" id='editSystemUserModal' tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel">
        <div class="modal-dialog modal-md" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel">Update Permissions</h4>
                </div>

                <div class="modal-body">
                    <div class="box-body">
                        <form id="update_form" method="POST" action="public/db-operation.php" data-parsley-validate class="form-horizontal form-label-left">
                            <input type='hidden' name="system_user_id" id="system_user_id" value='' />
                            <input type='hidden' name="update_system_user" id="update_system_user" value='1' />
                            <div class="box box-primary">
                                <table>
                                    <tr>
                                        <th>Module/Permissions</th>
                                        <th>Create</th>
                                        <th>Read</th>
                                        <th>Update</th>
                                        <th>Delete</th>
                                    </tr>
                                    <tr>
                                        <td>Users</td>
                                        <td><input type="checkbox" id="permission-create-user-button" class="js-switch" checked>
                                            <input type="hidden" id="permission-is-create-user" name="permission-is-create-user" value="1">
                                        </td>
                                        <td><input type="checkbox" id="permission-read-user-button" class="js-switch" checked>
                                            <input type="hidden" id="permission-is-read-user" name="permission-is-read-user" value="1">
                                        </td>
                                        <td><input type="checkbox" id="permission-update-user-button" class="js-switch" checked>
                                            <input type="hidden" id="permission-is-update-user" name="permission-is-update-user" value="1">
                                        </td>
                                        <td><input type="checkbox" id="permission-delete-user-button" class="js-switch" checked>
                                            <input type="hidden" id="permission-is-delete-user" name="permission-is-delete-user" value="1">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Transactions</td>
                                        <td><input type="checkbox" id="permission-create-transaction-button" class="js-switch" checked>
                                            <input type="hidden" id="permission-is-create-transaction" name="permission-is-create-transaction" value="1">
                                        </td>
                                        <td><input type="checkbox" id="permission-read-transaction-button" class="js-switch" checked>
                                            <input type="hidden" id="permission-is-read-transaction" name="permission-is-read-transaction" value="1">
                                        </td>
                                        <td><input type="checkbox" id="permission-update-transaction-button" class="js-switch" checked>
                                            <input type="hidden" id="permission-is-update-transaction" name="permission-is-update-transaction" value="1">
                                        </td>
                                        <td><input type="checkbox" id="permission-delete-transaction-button" class="js-switch" checked>
                                            <input type="hidden" id="permission-is-delete-transaction" name="permission-is-delete-transaction" value="1">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Withdrawals</td>
                                        <td><input type="checkbox" id="permission-create-withdrawal-button" class="js-switch" checked>
                                            <input type="hidden" id="permission-is-create-withdrawal" name="permission-is-create-withdrawal" value="1">
                                        </td>
                                        <td><input type="checkbox" id="permission-read-withdrawal-button" class="js-switch" checked>
                                            <input type="hidden" id="permission-is-read-withdrawal" name="permission-is-read-withdrawal" value="1">
                                        </td>
                                        <td><input type="checkbox" id="permission-permission-update-withdrawal-button" class="js-switch" checked>
                                            <input type="hidden" id="permission-is-update-withdrawal" name="permission-is-update-withdrawal" value="1">
                                        </td>
                                        <td><input type="checkbox" id="permission-delete-withdrawal-button" class="js-switch" checked>
                                            <input type="hidden" id="permission-is-delete-withdrawal" name="permission-is-delete-withdrawal" value="1">
                                        </td>
                                    </tr>
 
 

                                </table>

                            </div>
                            <input type="hidden" id="id" name="id">
                            <div class="ln_solid"></div>
                            <div class="form-group">
                                <div class="col-md-6 col-sm-6 col-xs-12 col-md-offset-3">
                                    <button type="submit" id="update_btn" class="btn btn-success">Update</button>
                                </div>
                            </div>
                            <div class="form-group">

                                <div class="row">
                                    <div class="col-md-offset-3 col-md-8" style="display:none;" id="update_result"></div>
                                </div>
                            </div>
                        </form>
                    </div>

                </div>
            </div>
        </div>
    </div>

</section>




<script>
    $('#add_form').validate({
        rules: {
            username: "required",
            email: "required",
            password: "required",
            role: "required",
            confirm_password: {
                required: true,
                equalTo: "#password"
            }
        }
    });
</script>

<script>
    $('#add_form').on('submit', function(e) {
        e.preventDefault();
        var formData = new FormData(this);
        if ($("#add_form").validate().form()) {
            if (confirm('Are you sure?Want to Add.')) {
                $.ajax({
                    type: 'POST',
                    url: $(this).attr('action'),
                    data: formData,
                    beforeSend: function() {
                        $('#submit_btn').html('Please wait..');
                    },
                    cache: false,
                    contentType: false,
                    processData: false,
                    success: function(result) {
                        $('#result').html(result);
                        $('#result').show().delay(6000).fadeOut();
                        $('#submit_btn').html('Submit');
                        $('#add_form')[0].reset();
                        $('#system-users').bootstrapTable('refresh');
                    }
                });
            }
        }
    });
</script>

<script>
    $('#update_form').on('submit', function(e) {
        e.preventDefault();
        var formData = new FormData(this);
        if (confirm('Are you sure?Want to update.')) {
            $.ajax({
                type: 'POST',
                url: $(this).attr('action'),
                data: formData,
                beforeSend: function() {
                    $('#update_btn').html('Please wait..');
                },
                cache: false,
                contentType: false,
                processData: false,
                success: function(result) {
                    $('#update_result').html(result);
                    $('#update_result').show().delay(6000).fadeOut();
                    $('#update_btn').html('Submit');
                    $('#system-users').bootstrapTable('refresh');
                    setTimeout(function() {
                        $('#editSystemUserModal').modal('hide');
                    }, 3000);
                }
            });
        }
    });
</script>

<script>
    var changeCheckbox = document.querySelector('#create-user-button');
    var init = new Switchery(changeCheckbox);
    changeCheckbox.onchange = function() {
        if ($(this).is(':checked')) {
            $('#is-create-user').val(1);
        } else {
            $('#is-create-user').val(0);
        }
    };
    var changeCheckbox = document.querySelector('#read-user-button');
    var init = new Switchery(changeCheckbox);
    changeCheckbox.onchange = function() {
        if ($(this).is(':checked')) {
            $('#is-read-user').val(1);
        } else {
            $('#is-read-user').val(0);
        }
    };
    var changeCheckbox = document.querySelector('#update-user-button');
    var init = new Switchery(changeCheckbox);
    changeCheckbox.onchange = function() {
        if ($(this).is(':checked')) {
            $('#is-update-user').val(1);
        } else {
            $('#is-update-user').val(0);
        }
    };
    var changeCheckbox = document.querySelector('#delete-user-button');
    var init = new Switchery(changeCheckbox);
    changeCheckbox.onchange = function() {
        // alert(changeCheckbox.checked);
        if ($(this).is(':checked')) {
            $('#is-delete-user').val(1);
        } else {
            $('#is-delete-user').val(0);
        }
    };
</script>
<script>
    var changeCheckbox = document.querySelector('#create-transaction-button');
    var init = new Switchery(changeCheckbox);
    changeCheckbox.onchange = function() {
        // alert(changeCheckbox.checked);
        if ($(this).is(':checked')) {
            $('#is-create-transaction').val(1);
        } else {
            $('#is-create-transaction').val(0);
        }
    };
    var changeCheckbox = document.querySelector('#read-transaction-button');
    var init = new Switchery(changeCheckbox);
    changeCheckbox.onchange = function() {
        // alert(changeCheckbox.checked);
        if ($(this).is(':checked')) {
            $('#is-read-transaction').val(1);
        } else {
            $('#is-read-transaction').val(0);
        }
    };
    var changeCheckbox = document.querySelector('#update-transaction-button');
    var init = new Switchery(changeCheckbox);
    changeCheckbox.onchange = function() {
        // alert(changeCheckbox.checked);
        if ($(this).is(':checked')) {
            $('#is-update-transaction').val(1);
        } else {
            $('#is-update-transaction').val(0);
        }
    };
    var changeCheckbox = document.querySelector('#delete-transaction-button');
    var init = new Switchery(changeCheckbox);
    changeCheckbox.onchange = function() {
        // alert(changeCheckbox.checked);
        if ($(this).is(':checked')) {
            $('#is-delete-transaction').val(1);
        } else {
            $('#is-delete-transaction').val(0);
        }
    };
</script>
<script>
    var changeCheckbox = document.querySelector('#create-withdrawal-button');
    var init = new Switchery(changeCheckbox);
    changeCheckbox.onchange = function() {
        // alert(changeCheckbox.checked);
        if ($(this).is(':checked')) {
            $('#is-create-withdrawal').val(1);
        } else {
            $('#is-create-withdrawal').val(0);
        }
    };
    var changeCheckbox = document.querySelector('#read-withdrawal-button');
    var init = new Switchery(changeCheckbox);
    changeCheckbox.onchange = function() {
        // alert(changeCheckbox.checked);
        if ($(this).is(':checked')) {
            $('#is-read-withdrawal').val(1);
        } else {
            $('#is-read-withdrawal').val(0);
        }
    };
    var changeCheckbox = document.querySelector('#update-withdrawal-button');
    var init = new Switchery(changeCheckbox);
    changeCheckbox.onchange = function() {
        // alert(changeCheckbox.checked);
        if ($(this).is(':checked')) {
            $('#is-update-withdrawal').val(1);
        } else {
            $('#is-update-withdrawal').val(0);
        }
    };
    var changeCheckbox = document.querySelector('#delete-withdrawal-button');
    var init = new Switchery(changeCheckbox);
    changeCheckbox.onchange = function() {
        // alert(changeCheckbox.checked);
        if ($(this).is(':checked')) {
            $('#is-delete-withdrawal').val(1);
        } else {
            $('#is-delete-withdrawal').val(0);
        }
    };
    // var switchStatus = false;
</script>


<script>
    window.actionEvents = {
        'click .edit-system-user': function(e, value, row, index) {
            permissions = row.permissions;
            permissions = JSON.parse(permissions);
            // console.log(permissions);
            $("#update_form").trigger("reset");


            $('#system_user_id').val(row.id);

            if (permissions.user.create == 1) {
                // $('#permission-create-order-button').attr('checked', true);
                $('#permission-create-user-button').prop('checked', true);
                $('#permission-is-create-user').val(1);
            } else {
                $('#permission-create-user-button').attr('checked', false);
                $('#permission-is-create-user').val(0);
            }
            if (permissions.user.read == 1) {
                $('#permission-read-user-button').attr('checked', true);
                $('#permission-is-read-user').val(1);
            } else {
                $('#permission-read-user-button').attr('checked', false);
                $('#permission-is-read-user').val(0);
            }
            if (permissions.user.update == 1) {
                $('#permission-update-user-button').attr('checked', true);
                $('#permission-is-update-user').val(1);
            } else {
                $('#permission-update-user-button').attr('checked', false);
                $('#permission-is-update-user').val(0);
            }
            if (permissions.user.delete == 1) {
                $('#permission-delete-user-button').attr('checked', true);
                $('#permission-is-delete-user').val(1);
            } else {
                $('#permission-delete-user-button').attr('checked', false);
                $('#permission-is-delete-user').val(0);
            }

            if (permissions.transaction.create == 1) {
                $('#permission-create-transaction-button').attr('checked', true);
                $('#permission-is-create-transaction').val(1);
            } else {
                $('#permission-create-transaction-button').attr('checked', false);
                $('#permission-is-create-transaction').val(0);
            }
            if (permissions.transaction.read == 1) {
                $('#permission-read-transaction-button').attr('checked', true);
                $('#permission-is-read-transaction').val(1);
            } else {
                $('#permission-read-transaction-button').attr('checked', false);
                $('#permission-is-read-transaction').val(0);
            }
            if (permissions.transaction.update == 1) {
                $('#permission-update-transaction-button').attr('checked', true);
                $('#permission-is-update-transaction').val(1);
            } else {
                $('#permission-update-transaction-button').attr('checked', false);
                $('#permission-is-update-transaction').val(0);
            }
            if (permissions.transaction.delete == 1) {
                $('#permission-delete-transaction-button').attr('checked', true);
                $('#permission-is-delete-transaction').val(1);
            } else {
                $('#permission-delete-transaction-button').attr('checked', false);
                $('#permission-is-delete-transaction').val(0);
            }
            if (permissions.withdrawal.create == 1) {
                $('#permission-create-withdrawal-button').attr('checked', true);
                $('#permission-is-create-withdrawal').val(1);
            } else {
                $('#permission-create-withdrawal-button').attr('checked', false);
                $('#permission-is-create-withdrawal').val(0);
            }
            if (permissions.withdrawal.read == 1) {
                $('#permission-read-withdrawal-button').attr('checked', true);
                $('#permission-is-read-withdrawal').val(1);
            } else {
                $('#permission-read-withdrawal-button').attr('checked', false);
                $('#permission-is-read-withdrawal').val(0);
            }
            if (permissions.withdrawal.update == 1) {
                $('#permission-update-withdrawal-button').attr('checked', true);
                $('#permission-is-update-withdrawal').val(1);
            } else {
                $('#permission-update-withdrawal-button').attr('checked', false);
                $('#permission-is-update-withdrawal').val(0);
            }
            if (permissions.withdrawal.delete == 1) {
                $('#permission-delete-withdrawal-button').attr('checked', true);
                $('#permission-is-delete-withdrawal').val(1);
            } else {
                $('#permission-delete-withdrawal-button').attr('checked', false);
                $('#permission-is-delete-withdrawal').val(0);
            }


          

        }
    }
    //   var changeCheckbox = document.querySelector('#permission-create-order-button');
    // var init = new Switchery(changeCheckbox);
    // changeCheckbox.onchange = function() {
    //     if ($(this).is(':checked')) {
    //         $('#permission-is-create-order').val(1);
    //     }else{
    //         $('#permission-is-create-order').val(0);
    //     }
    // };
    $('#permission-create-user-button').change(function() {
        if ($(this).is(':checked')) {
            $('#permission-is-create-user').val(1);
        } else {
            $('#permission-is-create-user').val(0);
        }
    });
    $('#permission-read-user-button').change(function() {
        if ($(this).is(':checked')) {
            $('#permission-is-read-user').val(1);
        } else {
            $('#permission-is-read-user').val(0);
        }
    });
    $('#permission-update-user-button').change(function() {
        if ($(this).is(':checked')) {
            $('#permission-is-update-user').val(1);
        } else {
            $('#permission-is-update-user').val(0);
        }
    });
    $('#permission-delete-user-button').change(function() {
        if ($(this).is(':checked')) {
            $('#permission-is-delete-user').val(1);
        } else {
            $('#permission-is-delete-user').val(0);
        }
    });


    $('#permission-create-transaction-button').change(function() {
        if ($(this).is(':checked')) {
            $('#permission-is-create-transaction').val(1);
        } else {
            $('#permission-is-create-transaction').val(0);
        }
    });
    $('#permission-read-transaction-button').change(function() {
        if ($(this).is(':checked')) {
            $('#permission-is-read-transaction').val(1);
        } else {
            $('#permission-is-read-transaction').val(0);
        }
    });
    $('#permission-update-transaction-button').change(function() {
        if ($(this).is(':checked')) {
            $('#permission-is-update-transaction').val(1);
        } else {
            $('#permission-is-update-transaction').val(0);
        }
    });
    $('#permission-delete-transaction-button').change(function() {
        if ($(this).is(':checked')) {
            $('#permission-is-delete-transaction').val(1);
        } else {
            $('#permission-is-delete-transaction').val(0);
        }
    });


    $('#permission-create-withdrawal-button').change(function() {
        if ($(this).is(':checked')) {
            $('#permission-is-create-withdrawal').val(1);
        } else {
            $('#permission-is-create-withdrawal').val(0);
        }
    });
    $('#permission-read-withdrawal-button').change(function() {
        if ($(this).is(':checked')) {
            $('#permission-is-read-withdrawal').val(1);
        } else {
            $('#permission-is-read-withdrawal').val(0);
        }
    });
    $('#permission-update-withdrawal-button').change(function() {
        if ($(this).is(':checked')) {
            $('#permission-is-update-withdrawal').val(1);
        } else {
            $('#permission-is-update-withdrawal').val(0);
        }
    });
    $('#permission-delete-withdrawal-button').change(function() {
        if ($(this).is(':checked')) {
            $('#permission-is-delete-withdrawal').val(1);
        } else {
            $('#permission-is-delete-withdrawal').val(0);
        }
    });

    


</script>
<script>
    $(document).on('click', '.delete-system-user', function() {
        if (confirm('Are you sure? Want to delete system user.')) {
            id = $(this).data("id");
            $.ajax({
                url: 'public/db-operation.php',
                type: "get",
                data: 'id=' + id + '&delete_system_user=1',
                success: function(result) {
                    if (result == 0) {
                        $('#system-users').bootstrapTable('refresh');
                    } else {
                        alert('Error! System user could not be deleted.');
                    }

                }
            });
        }
    });
</script>