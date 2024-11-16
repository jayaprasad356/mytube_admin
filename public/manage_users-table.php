<section class="content-header">
    <h1>Manage Verify Users /<small><a href="reports.php"><i class="fa fa-home"></i> Home</a></small></h1>

   
</section>
    <!-- Main content -->
    <section class="content">
        <!-- Main row -->
        <div class="row">
            <!-- Left col -->
            <div class="col-xs-12">
                <div class="box">
                    <div class="box-header">
                            <div class="form-group col-md-3">
                                    <h4 class="box-title">Filter by Status </h4>
                                    <select id='status' name="status" class='form-control'>
                                            <option value="">All</option>
                                            <option value="0">Not-verified</option>
                                            <option value="1">Verified</option>
                                            <option value="2">Blocked</option>
                                    </select>
                            </div>
                    </div>
                    <!-- /.box-header -->
                    <div class="box-body table-responsive">
                        <table id='users_table' class="table table-hover" data-toggle="table" data-url="api-firebase/get-bootstrap-table-data.php?table=manage_users" data-page-list="[5, 10, 20, 50, 100, 200]" data-show-refresh="true" data-show-columns="true" data-side-pagination="server" data-pagination="true" data-search="true" data-trim-on-search="false" data-filter-control="true" data-query-params="queryParams" data-sort-name="id" data-sort-order="desc" data-show-export="true" data-export-types='["txt","csv"]' data-export-options='{
                            "fileName": "yellow app-users-list-<?= date('d-m-Y') ?>",
                            "ignoreColumn": ["operate"] 
                        }'>
                            <thead>
                                <tr>
                                   <th data-field="operate">Verify</th>
                                    <th data-field="id" data-sortable="true">ID</th>
                                    <th data-field="name" data-sortable="true">Name</th>
                                    <th data-field="mobile" data-sortable="true">Phone Number</th>
                                    <th data-field="refer_code" data-sortable="true">Refer Code</th>
                                    <th data-field="referred_by" data-sortable="true">Refered By</th>
                                    <th data-field="balance" data-sortable="true">Balance</th>
                                    <th data-field="withdrawal" data-sortable="true">Withdrawal</th>
                                    <th data-field="history" data-sortable="true">History</th>
                                    <th data-field="earn" data-sortable="true">Earn</th>
                                    <th data-field="today_codes" data-sortable="true">Today Codes</th>
                                    <th data-field="total_referrals" data-sortable="true">Total Referrals</th>
                                    <th data-field="total_codes" data-sortable="true">Total Codes</th>
                                    <th data-field="code_generate" data-sortable="true">Code Generate</th>
                                    <th data-field="withdrawal_status" data-sortable="true">Withdrawal Status</th>
                                    <!-- <th data-field="status" data-sortable="true">Status</th> -->
                                    <th data-field="email" data-sortable="true">Email</th>
                                    <th data-field="city" data-sortable="true">City</th>
                                    <th data-field="device_id" data-sortable="true">Device Id</th>
                                    <th data-field="password" data-sortable="true">Password</th>
                                    <th data-field="dob" data-sortable="true">Date of Birth</th>

                                   
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
    $('#status').on('change', function() {
            id = $('#status').val();
            $('#users_table').bootstrapTable('refresh');
    });

    function queryParams(p) {
        return {
            "status": $('#status').val(),
            limit: p.limit,
            sort: p.sort,
            order: p.order,
            offset: p.offset,
            search: p.search
        };
    }
</script>

