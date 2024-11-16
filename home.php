<?php session_start();

include_once('includes/custom-functions.php');
include_once('includes/functions.php');
$function = new custom_functions;

// set time for session timeout
$currentTime = time() + 25200;
$expired = 3600;
// if session not set go to login page
if (!isset($_SESSION['username'])) {
    header("location:index.php");
}
// if current time is more than session timeout back to login page
if ($currentTime > $_SESSION['timeout']) {
    session_destroy();
    header("location:index.php");
}
// destroy previous session timeout and create new one
unset($_SESSION['timeout']);
$_SESSION['timeout'] = $currentTime + $expired;
include "header.php";

$sql = "SELECT COUNT(*) AS activeUserCount FROM users WHERE status = 1 AND code_generate = 1 AND today_codes != 0";
$db->sql($sql);
$res = $db->getResult();
$activeUserCount = (isset($res[0]['activeUserCount'])) ? $res[0]['activeUserCount'] : 0;


?>
<html>

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <title>Smart Id Maker - Dashboard</title>
</head>

<body>
    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <h1>Home</h1>
            <ol class="breadcrumb">
                <li>
                    <a href="reports.php"> <i class="fa fa-home"></i> Home</a>
                </li>
            </ol>
        </section>
        <section class="content">
            <div class="row">
                <div class="col-lg-4 col-xs-6">
                    <div class="small-box bg-green">
                        <div class="inner">
                        <h3><?php
                            echo $activeUserCount;
                             ?></h3>
                            <p>Active Users</p>
                        </div>
                        <div class="icon"><i class="fa fa-user"></i></div>
                        <a href="users.php" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <div class="box box-warning">
                        <div class="box-header with-border">
                            <h3 class="box-title">Top Today Coders <small>( Day: <?= date("D"); ?>)</small></h3>
                            <div class="box-tools pull-right">
                                <button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                                <button class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
                            </div>
                        </div>
                        <div class="box-body">

                            <div class="table-responsive">
                            <table id='users_table' class="table table-hover" data-toggle="table" data-url="api-firebase/get-bootstrap-table-data.php?table=top_coders" data-page-list="[5, 10, 20, 50, 100, 200]" data-show-refresh="true" data-show-columns="true" data-side-pagination="server" data-pagination="true" data-search="true" data-trim-on-search="false" data-filter-control="true" data-query-params="queryParams" data-sort-name="id" data-sort-order="desc" data-show-export="true" data-export-types='["txt","csv"]' data-export-options='{
                                "fileName": "Yellow app-withdrawals-list-<?= date('d-m-Y') ?>",
                                "ignoreColumn": ["operate"] 
                            }'>
                                    <thead>
                                        <tr>
                                            <th data-field="id" data-sortable='true'>ID</th>
                                            <!-- <th data-field="joined_date" data-visible="true">Joined Date</th> -->
                                            <th data-field="name" data-sortable='true'>Name</th>
                                            <th data-field="mobile">Mobile</th>
                                            <th data-field="today_codes" data-sortable='true'>Codes</th>
                                            <th data-field="refer_code" data-sortable='true'>Refer Code</th>
                                            <th data-field="earn" >Earn</th>
                                          
                                        
                                        </tr>
                                    </thead>
                                </table>
                            </div>
                        </div>

                    </div>
                </div>
                <!-- <div class="col-md-6">
                    <div class="box box-primary">
                        <div class="box-header with-border">
                            <h3 class="box-title">Top Categories <small> ( Month: <?= date("M"); ?>) </small></h3>
                            <div class="box-tools pull-right">
                                <button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                                <button class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
                            </div>
                        </div>
                        <div class="box-body">

                            <div class="table-responsive">
                                <table class="table no-margin" id='top_seller_table' data-toggle="table" data-url="api-firebase/get-bootstrap-table-data.php?table=top_categories" data-page-list="[5,10]" data-page-size="5" data-show-refresh="true" data-show-columns="true" data-side-pagination="server" data-pagination="true" data-sort-name="total_revenues" data-sort-order="desc" data-toolbar="#toolbar" data-query-params="queryParams_top_cat">
                                    <thead>
                                        <tr>
                                            <th data-field="id" data-sortable='true'>Rank</th>
                                            <th data-field="cat_name" data-sortable='true' data-visible="true">Category</th>
                                            <th data-field="p_name" data-sortable='true' data-visible="true">Product Name</th>
                                            <th data-field="total_revenues" data-sortable='true'>Total Revenue(<?= $settings['currency'] ?>)</th>
                                            <th data-field="operate">Action</th>
                                        </tr>
                                    </thead>
                                </table>
                            </div>
                        </div>

                    </div>
                </div> -->
            </div>
        </section>
    </div>
    <script>
        $('#filter_order').on('change', function() {
            $('#orders_table').bootstrapTable('refresh');
        });
        $('#seller_id').on('change', function() {
            $('#orders_table').bootstrapTable('refresh');
        });
    </script>
    <script>
        function queryParams(p) {
            return {
                "filter_order": $('#filter_order').val(),
                "seller_id": $('#seller_id').val(),
                limit: p.limit,
                sort: p.sort,
                order: p.order,
                offset: p.offset,
                search: p.search
            };
        }

    </script>
    <?php include "footer.php"; ?>
    <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
    <script>
        google.charts.load('current', {
            'packages': ['bar']
        });
        google.charts.setOnLoadCallback(drawChart);

        function drawChart() {
            var data = google.visualization.arrayToDataTable([
                ['Hour', 'Total - <?= $stu_total[0]['total'] ?>'],
                <?php foreach ($result_order as $row) {
                    //$date = date('d-M', strtotime($row['order_date']));
                    echo "['" . $row['time'] . "'," . $row['numoft'] . "],";
                } ?>
            ]);
            var options = {
                chart: {
                    title: 'Transactions By Hour Wise',
                    //subtitle: 'Total Sale In Last Week (Month: <?php echo date("M"); ?>)',
                }
            };

            var chart = new google.charts.Bar(document.getElementById('earning_chart'));
            chart.draw(data, google.charts.Bar.convertOptions(options));


            var data = google.visualization.arrayToDataTable([
                ['Hour', 'Total - <?= $stu_total2[0]['total'] .'\n₹'.$stu_total2[0]['total'] * COST_PER_CODE ?>'],
                <?php foreach ($result_order2 as $row) {
                    //$date = date('d-M', strtotime($row['order_date']));
                    echo "['" . $row['time'] . "'," . $row['codes'] . "],";
                } ?>
            ]);
            var options = {
                chart: {
                    title: 'Codes By Hour Wise',
                    //subtitle: 'Total Sale In Last Week (Month: <?php echo date("M"); ?>)',
                }
            };

            var chart = new google.charts.Bar(document.getElementById('earning_chart2'));
            chart.draw(data, google.charts.Bar.convertOptions(options));
        }
    </script>
</body>
</html>