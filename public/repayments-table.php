<?php

if (isset($_POST['btnPaid'])  && isset($_POST['enable'])) {
       $sql="SELECT * FROM repayments WHERE id IN (".implode(',',$_POST['enable']).")";
       $db->sql($sql);
       $result = $db->getResult();
            foreach ($result as $row) {
                $amount=$row['amount'];
                $sql = "UPDATE users SET ongoing_sa_balance= ongoing_sa_balance -$amount WHERE id = $row[user_id]";
                $db->sql($sql);
                $result = $db->getResult();
            }
    for ($i = 0; $i < count($_POST['enable']); $i++) {
    
        $enable = $db->escapeString($fn->xss_clean($_POST['enable'][$i]));
        $sql = "UPDATE repayments SET status=1 WHERE id = $enable";
        $db->sql($sql);
        $result = $db->getResult();
    }
}

?>
<section class="content-header">
    <h1>Repayments /<small><a href="home.php"><i class="fa fa-home"></i> Home</a></small></h1>

</section>
    <!-- Main content -->
    <section class="content">
            <form name="withdrawal_form" method="post" enctype="multipart/form-data">
                <!-- Main row -->
                <div class="row">
                    <!-- Left col -->
                    <div class="col-12 col-md-10">
                        <div class="box">
                                    <div class="box-header">
                                        <div class="row">
                                                <div class="form-group col-md-3">
                                                    <h4 class="box-title">Filter by Name </h4>
                                                        <select id='user_id' name="user_id" class='form-control'>
                                                        <option value=''>All</option>
                                                        
                                                                <?php
                                                                $sql = "SELECT id,name FROM `users`";
                                                                $db->sql($sql);
                                                                $result = $db->getResult();
                                                                foreach ($result as $value) {
                                                                ?>
                                                                    <option value='<?= $value['id'] ?>'><?= $value['name'] ?></option>
                                                            <?php } ?>
                                                        </select>
                                                </div>
                                        </div>
                                    </div>
                            <!-- /.box-header -->
                            <div class="box-body table-responsive">
                                    <div class="row">
                                            <?php 
                                            if($_SESSION['role'] == 'Super Admin'){?>
                                                <div class="text-left col-md-2">
                                                    <input type="checkbox" onchange="checkAll(this)" name="chk[]" > Select All</input>
                                                </div> 
                                                <div class="col-md-3">
                                                        <button type="submit" class="btn btn-success" name="btnPaid">Paid</button>                                                
                                                </div>
                                            <?php } ?>
                                        </div>
                                <table id='users_table' class="table table-hover" data-toggle="table" data-url="api-firebase/get-bootstrap-table-data.php?table=repayments" data-page-list="[5, 10, 20, 50, 100, 200]" data-show-refresh="true" data-show-columns="true" data-side-pagination="server" data-pagination="true" data-search="true" data-trim-on-search="false" data-filter-control="true" data-query-params="queryParams" data-sort-name="id" data-sort-order="desc" data-show-export="true" data-export-types='["txt","csv"]' data-export-options='{
                                    "fileName": "Yellow app-repayments-list-<?= date('d-m-Y') ?>",
                                    "ignoreColumn": ["operate"] 
                                }'>
                                    <thead>
                                        <tr>
                                            <th data-field="column"> All</th>
                                            <th data-field="id" data-sortable="true">ID</th>
                                            <th data-field="name" data-sortable="true">Name</th>
                                            <th data-field="mobile" data-sortable="true">Mobile</th>
                                            <th data-field="amount" data-sortable="true">Amount</th>
                                            <th data-field="due_date" data-sortable="true">Due Date</th>
                                            <th data-field="status" data-sortable="true">Status</th>
                                        </tr>
                                    </thead>
                                </table>
                            </div>
                            <!-- /.box-body -->
                        </div>
                        <!-- /.box -->
                    </div>
                    <div class="separator"> </div>
                </div>
                <!-- /.row (main row) -->
            </form>

    </section>
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
<script>
 function checkAll(ele) {
     var checkboxes = document.getElementsByTagName('input');
     if (ele.checked) {
         for (var i = 0; i < checkboxes.length; i++) {
             if (checkboxes[i].type == 'checkbox') {
                 checkboxes[i].checked = true;
             }
         }
     } else {
         for (var i = 0; i < checkboxes.length; i++) {
             console.log(i)
             if (checkboxes[i].type == 'checkbox') {
                 checkboxes[i].checked = false;
             }
         }
     }
 }
    
</script>
<script>
     $('#user_id').on('change', function() {
            id = $('#user_id').val();
            $('#users_table').bootstrapTable('refresh');
    });
    // $('#type').on('change', function() {
    //     $('#users_table').bootstrapTable('refresh');
    // });

    function queryParams(p) {
        return {
            // "type": $('#type').val(),
            "user_id": $('#user_id').val(),
            // "community": $('#community').val(),
            limit: p.limit,
            sort: p.sort,
            order: p.order,
            offset: p.offset,
            search: p.search
        };
    }
</script>
