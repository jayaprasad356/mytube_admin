
<section class="content-header">
    <h1>Withdrawals /<small><a href="home.php"><i class="fa fa-home"></i> Home</a></small></h1>

</section>
    <!-- Main content -->
    <section class="content">
        <!-- Main row -->
        <form name="withdrawal_form" method="post" enctype="multipart/form-data">
            <div class="row">
                <!-- Left col -->
                <div class="col-12">
                    <div class="box">
                        <div class="box-header">
                                        <div class="form-group col-md-3">
                                            <label for="exampleInputEmail1">Mobile Number</label>
                                            <input type="text" class="form-control" name="mobile" id="mobile" required>
                                        </div>
                        </div>
                        <!-- /.box-header -->
                        <div class="box-body table-responsive">
                            <table id='users_table' class="table table-hover" data-toggle="table" data-url="api-firebase/get-bootstrap-table-data.php?table=search_withdrawals" data-page-list="[5, 10, 20, 50, 100, 200,500,700,1000]" data-show-refresh="true" data-show-columns="true" data-side-pagination="server" data-pagination="true" data-search="true" data-trim-on-search="false" data-filter-control="true" data-query-params="queryParams" data-sort-name="w.id" data-show-footer="true" data-sort-order="desc" data-show-export="true" data-export-types='["txt","csv"]' data-export-options='{
                                "fileName": "Yellow app-withdrawals-list-<?= date('d-m-Y') ?>",
                                "ignoreColumn": ["operate"] 
                            }'>
                                <thead>
                                    <tr>
                                        <th data-field="id" data-sortable="true">ID</th>
                                        <th data-field="name" data-sortable="true" data-visible="true" data-footer-formatter="totalFormatter">Name</th>
                                        <th data-field="amount" data-sortable="true" data-visible="true" data-footer-formatter="priceFormatter">Amount</th>
                                        <th data-field="status" data-sortable="true">Status</th>
                                        <th data-field="balance" data-sortable="true">Balance</th>
                                        <th data-field="datetime" data-sortable="true">DateTime</th>
                                        <th data-field="account_num" data-sortable="true">Account Number</th>
                                        <th data-field="holder_name" data-sortable="true">Holder Name</th>
                                        <th data-field="bank" data-sortable="true">Bank</th>
                                        <th data-field="branch" data-sortable="true">Branch</th>
                                        <th data-field="ifsc" data-sortable="true">IFSC</th>
                                        <th data-field="total_codes" data-sortable="true">Total Codes</th>
                                        <th data-field="total_referrals" data-sortable="true">Total Referrals</th>
                                        <th data-field="mobile" data-sortable="true">Mobile</th>
                                        <th data-field="referred_by" data-sortable="true">Referred By</th>
                                        <th data-field="refer_code" data-sortable="true">Refer Code</th>
                                        <th data-field="history" data-sortable="true">History</th>
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
        </form>

        <!-- /.row (main row) -->
    </section>

<script>
        $('#mobile').on('change', function() {
            id = $('#mobile').val();
            $('#users_table').bootstrapTable('refresh');
        });
       
    function queryParams(p) {
        return {
            "mobile": $('#mobile').val(),
            limit: p.limit,
            sort: p.sort,
            order: p.order,
            offset: p.offset,
            search: p.search
        };
    }
    function totalFormatter() {
        return '<span style="color:green;font-weight:bold;font-size:large;">TOTAL</span>'
    }

    var total = 0;

    function priceFormatter(data) {
        var field = this.field
        return '<span style="color:green;font-weight:bold;font-size:large;"> ' + data.map(function(row) {
                return +row[field]
            })
            .reduce(function(sum, i) {
                return sum + i
            }, 0);
    }
</script>


