<?php
if (isset($_POST['btnDelete'])) {


    $from_date = $db->escapeString(($_POST['from_date']));
    $to_date = $db->escapeString(($_POST['to_date']));


    $sql = "DELETE  FROM transactions WHERE DATE(datetime) BETWEEN '$from_date' AND '$to_date' AND type = 'generate'";
    $db->sql($sql);
    $result = $db->getResult();

}
?>


<section class="content-header">
    <h1>Salary Advance Transactions /<small><a href="home.php"><i class="fa fa-home"></i> Home</a></small></h1>

</section>
    <!-- Main content -->
    <section class="content">
        <!-- Main row -->
        <div class="row">
            <!-- Left col -->
            <div class="col-12 col-md-10">
                <div class="box">
                    <div class="box-header">
                            <div class="row">
                                    <!-- <div class="form-group col-md-3">
                                            <h4 class="box-title">Filter by Type </h4>
                                            <select id='type' name="type" class='form-control'>
                                            
                                            
                                                    <?php
                                                    $sql = "SELECT * FROM `transactions` GROUP BY type ORDER BY id";
                                                    $db->sql($sql);
                                                    $result = $db->getResult();
                                                    foreach ($result as $value) {
                                                    ?>
                                                        <option value='<?= $value['type'] ?>'><?= $value['type'] ?></option>
                                                <?php } ?>
                                            </select> 
                                    </div> -->
                                    <!-- <form name="delete_transaction" method="post">
                                        <div class="form-group col-md-3">
                                                <h4 class="box-title">From Date </h4>
                                                <input type="date" class="form-control" name="from_date" required>
                                                </select> 
                                        </div>
                                        <div class="form-group col-md-3">
                                                <h4 class="box-title">To Date </h4>
                                                <input type="date" class="form-control" name="to_date" required>
                                                </select> 
                                        </div>
                                        <div class="form-group col-md-3">
                                                <button style="margin-top:22px;" type='submit'  class="btn btn-danger" name="btnDelete">Delete</button>
                                        </div>

                                    </form> -->
                            </div>
                            <!-- <form action="export-transaction.php">
                                <button type='submit'  class="btn btn-primary"><i class="fa fa-download"></i> Export All Transactions</button>
                            </form> -->
                        </div>

                    <!-- /.box-header -->
                    <div class="box-body table-responsive">
                        <table id='users_table' class="table table-hover" data-toggle="table" data-url="api-firebase/get-bootstrap-table-data.php?table=salary_advance_transactions" data-page-list="[5, 10, 20, 50, 100, 200]" data-show-refresh="true" data-show-columns="true" data-side-pagination="server" data-pagination="true" data-search="true" data-trim-on-search="false" data-filter-control="true" data-query-params="queryParams" data-sort-name="id" data-sort-order="desc" data-show-export="true" data-export-types='["txt","csv"]' data-export-options='{
                            "fileName": "Yellow app-sa_transactions-list-<?= date('d-m-Y') ?>",
                            "ignoreColumn": ["operate"] 
                        }'>
                            <thead>
                                <tr>
                                    <th data-field="id" data-sortable="true">ID</th>
                                    <th data-field="name" data-sortable="true">Name</th>
                                    <th data-field="mobile" data-sortable="true">Mobile</th>
                                    <th data-field="amount" data-sortable="true">Amount</th>
                                    <th data-field="type" data-sortable="true">Type</th>
                                    <th data-field="datetime" data-sortable="true">DateTime</th>
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
    </section>

<script>
    $('#seller_id').on('change', function() {
        $('#products_table').bootstrapTable('refresh');
    });
    // $('#type').on('change', function() {
    //     $('#users_table').bootstrapTable('refresh');
    // });

    function queryParams(p) {
        return {
            // "type": $('#type').val(),
            "seller_id": $('#seller_id').val(),
            "community": $('#community').val(),
            limit: p.limit,
            sort: p.sort,
            order: p.order,
            offset: p.offset,
            search: p.search
        };
    }
</script>
