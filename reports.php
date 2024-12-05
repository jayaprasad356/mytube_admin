<?php session_start();

include_once('includes/custom-functions.php');
include_once('includes/functions.php');
$function = new custom_functions;
date_default_timezone_set('Asia/Kolkata');
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
$date = date('Y-m-d');
// Fetch user count
$sql = "SELECT COUNT(*) AS userCount FROM users $joinCondition";
$db->sql($sql);
$res = $db->getResult();
$userCount = (isset($res[0]['userCount'])) ? $res[0]['userCount'] : 0;

$sql = "SELECT COUNT(DISTINCT user_id) AS generatedUserCount  FROM transactions WHERE type = 'Generated' AND amount > 5 AND DATE(datetime) = '$date'";
$db->sql($sql);
$res = $db->getResult();
$generatedUserCount = (isset($res[0]['generatedUserCount'])) ? $res[0]['generatedUserCount'] : 0;

// Fetch today's registration count
$currentDate = date('Y-m-d');
$sql = "SELECT COUNT(*) AS todayRegistrationCount FROM users $joinCondition AND joined_date = '$currentDate'";
$db->sql($sql);
$res = $db->getResult();
$todayRegistrationCount = (isset($res[0]['todayRegistrationCount'])) ? $res[0]['todayRegistrationCount'] : 0;

// Fetch unpaid withdrawals amount
$sql = "SELECT SUM(amount) AS amount FROM withdrawals WHERE status = 0";
$db->sql($sql);
$res = $db->getResult();
$unpaidWithdrawalsAmount = "Rs." . (isset($res[0]['amount']) ? $res[0]['amount'] : 0);

// Assuming your current date is stored in $currentDate
$sql = "SELECT SUM(amount) AS todayTotalAmount FROM payments WHERE DATE(datetime) = '$currentDate'";
$db->sql($sql);
$res = $db->getResult();
$todayTotalAmount = (isset($res[0]['todayTotalAmount'])) ? $res[0]['todayTotalAmount'] : 0;

$sql = "SELECT COUNT(id) AS generatedBasicUserCount  FROM user_plan WHERE plan_id = 1";
$db->sql($sql);
$res = $db->getResult();
$generatedBasicUserCount = (isset($res[0]['generatedBasicUserCount'])) ? $res[0]['generatedBasicUserCount'] : 0;

$sql = "SELECT COUNT(id) AS generatedStandardUserCount  FROM user_plan WHERE plan_id = 2";
$db->sql($sql);
$res = $db->getResult();
$generatedStandardUserCount = (isset($res[0]['generatedStandardUserCount'])) ? $res[0]['generatedStandardUserCount'] : 0;

$sql = "SELECT COUNT(id) AS generatedAdvancedUserCount  FROM user_plan WHERE plan_id = 4";
$db->sql($sql);
$res = $db->getResult();
$generatedAdvancedUserCount = (isset($res[0]['generatedAdvancedUserCount'])) ? $res[0]['generatedAdvancedUserCount'] : 0;

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
            <h1>Reports</h1>
            <ol class="breadcrumb">
                <li>
                    <a href="reports.php"> <i class="fa fa-home"></i> Home</a>
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
                            echo $generatedUserCount;
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
                            echo $todayTotalAmount;
                             ?></h3>
                            <p>Payment Received</p>
                        </div>
                        <div class="icon"><i class="fa fa-money"></i></div>
                        <a href="payments.php" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>
                    </div>
                </div>
                <div class="col-lg-4 col-xs-6">
                    <div class="small-box bg-yellow">
                        <div class="inner">
                        <h3><?php
                            echo $generatedBasicUserCount;
                             ?></h3>
                            <p>Basic plan activated users</p>
                        </div>
                        <div class="icon"><i class="fa fa-money"></i></div>
                        <a href="payments.php" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>
                    </div>
                </div>
                <div class="col-lg-4 col-xs-6">
                    <div class="small-box bg-teal">
                        <div class="inner">
                        <h3><?php
                            echo $generatedStandardUserCount;
                             ?></h3>
                            <p>Standard Plan Activated Users</p>
                        </div>
                        <div class="icon"><i class="fa fa-money"></i></div>
                        <a href="payments.php" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>
                    </div>
                </div>
                <div class="col-lg-4 col-xs-6">
                    <div class="small-box bg-red">
                        <div class="inner">
                        <h3><?php
                            echo $generatedAdvancedUserCount;
                             ?></h3>
                            <p>Advanced plan activated users</p>
                        </div>
                        <div class="icon"><i class="fa fa-money"></i></div>
                        <a href="payments.php" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>
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