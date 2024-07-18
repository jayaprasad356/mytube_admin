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
if ($_SESSION['role'] == 'Super Admin') {
    $joinCondition = "WHERE ID is NOT NULL";
    $referCodePattern = "";
} else {
    $referCode = $_SESSION['refer_code'];
    $joinCondition = "WHERE refer_code REGEXP '^$referCode'";
    $referCodePattern = "^$referCode";
}

// Fetch user count
$sql = "SELECT COUNT(*) AS userCount FROM users $joinCondition";
$db->sql($sql);
$res = $db->getResult();
$userCount = (isset($res[0]['userCount'])) ? $res[0]['userCount'] : 0;
// Fetch active user count
$sql = "SELECT COUNT(*) AS activeUserCount FROM users $joinCondition AND status = 1 AND code_generate = 1 AND today_codes != 0";
$db->sql($sql);
$res = $db->getResult();
$activeUserCount = (isset($res[0]['activeUserCount'])) ? $res[0]['activeUserCount'] : 0;
// Fetch today's registration count
$currentDate = date('Y-m-d');
$sql = "SELECT COUNT(*) AS todayRegistrationCount FROM users $joinCondition AND joined_date = '$currentDate' AND status = 1";
$db->sql($sql);
$res = $db->getResult();
$todayRegistrationCount = (isset($res[0]['todayRegistrationCount'])) ? $res[0]['todayRegistrationCount'] : 0;

// Fetch unpaid withdrawals amount
$sql = "SELECT SUM(w.amount) AS unpaidWithdrawalsAmount FROM withdrawals w INNER JOIN users u ON u.id = w.user_id WHERE u.refer_code REGEXP '$referCodePattern' AND w.status = 0";
$db->sql($sql);
$res = $db->getResult();
$unpaidWithdrawalsAmount = "Rs." . (isset($res[0]['unpaidWithdrawalsAmount'])) ? $res[0]['unpaidWithdrawalsAmount'] : 0;
// Fetch paid withdrawals amount
$sql = "SELECT SUM(w.amount) AS paidWithdrawalsAmount FROM withdrawals w INNER JOIN users u ON u.id = w.user_id WHERE u.refer_code REGEXP '$referCodePattern' AND w.status = 1";
$db->sql($sql);
$res = $db->getResult();
$paidWithdrawalsAmount = "Rs." . (isset($res[0]['paidWithdrawalsAmount'])) ? $res[0]['paidWithdrawalsAmount'] : 0;
// Fetch total transactions amount
$sql = "SELECT SUM(t.amount) AS totalTransactionsAmount FROM transactions t INNER JOIN users u ON u.id = t.user_id WHERE u.refer_code REGEXP '$referCodePattern'";
$db->sql($sql);
$res = $db->getResult();
$totalTransactionsAmount = "Rs." . (isset($res[0]['totalTransactionsAmount'])) ? $res[0]['totalTransactionsAmount'] : 0;


?>
<html>

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <title>Fortune - Dashboard</title>
</head>

<body>
    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <h1>Reports</h1>
            <ol class="breadcrumb">
                <li>
                    <a href="home.php"> <i class="fa fa-home"></i> Home</a>
                </li>
            </ol>
        </section>
        <section class="content">
            <div class="row">
                <div class="col-lg-4 col-xs-6">
                    <div class="small-box bg-aqua">
                        <div class="inner">
                            <h3><?php
                            echo $userCount;
                             ?></h3>
                            <p>Users</p>
                        </div>
                        <div class="icon"><i class="fa fa-users"></i></div>
                        <a href="users.php" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>
                    </div>
                </div>
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
                                <div class="col-lg-4 col-xs-6">
                    <div class="small-box bg-red">
                        <div class="inner">
                        <h3><?php
                            echo $todayRegistrationCount;
                             ?></h3>
                            <p>Today Registration</p>
                        </div>
                        <div class="icon"><i class="fa fa-calendar"></i></div>
                        <a href="users.php?date=<?php echo date('Y-m-d') ?>" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>
                    </div>
                </div>
                <div class="col-lg-4 col-xs-6">
                    <div class="small-box bg-orange">
                        <div class="inner">
                        <h3><?php
                            echo $unpaidWithdrawalsAmount;
                             ?></h3>
                            <p>Unpaid Withdrawals</p>
                        </div>
                        <div class="icon"><i class="fa fa-money"></i></div>
                        <a href="withdrawals.php" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>
                    </div>
                </div>
                <div class="col-lg-4 col-xs-6">
                    <div class="small-box bg-purple">
                        <div class="inner">
                        <h3><?php
                            echo $paidWithdrawalsAmount;
                             ?></h3>
                            <p>Paid Withdrawals</p>
                        </div>
                        <div class="icon"><i class="fa fa-money"></i></div>
                        <a href="withdrawals.php" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>
                    </div>
                </div>
                <div class="col-lg-4 col-xs-6">
                    <div class="small-box bg-aqua">
                        <div class="inner">
                        <h3><?php
                            echo $totalTransactionsAmount;
                             ?></h3>
                            <p>Total Transactions</p>
                        </div>
                        <div class="icon"><i class="fa fa-arrow-right"></i></div>
                        <a href="transactions.php" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>
                    </div>
                </div>

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
</body>
</html>