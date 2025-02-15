<?php
session_start();

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

header('Access-Control-Allow-Origin: *');
header("Content-Type: application/json");
header("Expires: 0");
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
header("Cache-Control: no-store, no-cache, must-revalidate");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");


include_once('../includes/custom-functions.php');
$fn = new custom_functions;
include_once('../includes/crud.php');
include_once('../includes/variables.php');
$db = new Database();
$db->connect();
$currentdate = date('Y-m-d');
if (isset($config['system_timezone']) && isset($config['system_timezone_gmt'])) {
    date_default_timezone_set($config['system_timezone']);
    $db->sql("SET `time_zone` = '" . $config['system_timezone_gmt'] . "'");
} else {
    date_default_timezone_set('Asia/Kolkata');
    $db->sql("SET `time_zone` = '+05:30'");
}
if (isset($_GET['table']) && $_GET['table'] == 'users') {
    $offset = 0;
    $limit = 10;
    $where = '';
    $sort = 'id';
    $order = 'DESC';
    if ((isset($_GET['date'])  && $_GET['date'] != '')) {
        $date = $db->escapeString($fn->xss_clean($_GET['date']));
        $where .= "AND joined_date='$date' ";
    }
    if (isset($_GET['offset']))
        $offset = $db->escapeString($fn->xss_clean($_GET['offset']));
    if (isset($_GET['limit']))
        $limit = $db->escapeString($fn->xss_clean($_GET['limit']));

    if (isset($_GET['sort']))
        $sort = $db->escapeString($fn->xss_clean($_GET['sort']));
    if (isset($_GET['order']))
        $order = $db->escapeString($fn->xss_clean($_GET['order']));

    if (isset($_GET['search']) && !empty($_GET['search'])) {
        $search = $db->escapeString($fn->xss_clean($_GET['search']));
        $where .= "AND (name like '%" . $search . "%' OR mobile like '%" . $search . "%' OR city like '%" . $search . "%' OR email like '%" . $search . "%' OR refer_code like '%" . $search . "%'  OR referred_by like '%" . $search . "%')";
    }
    
    if($_SESSION['role'] == 'Super Admin'){
        $join = "WHERE id IS NOT NULL";
    }
    else{
        $refer_code = $_SESSION['refer_code'];
        $join = "WHERE refer_code REGEXP '^$refer_code'";
    }

    $sql = "SELECT COUNT(`id`) as total FROM `users` $join " . $where;
    $db->sql($sql);
    $res = $db->getResult();
    foreach ($res as $row)
        $total = $row['total'];

    // Modified SQL query to include registration count
    $sql = "SELECT *, 
                DATEDIFF('$currentdate', joined_date) AS history, 
                (SELECT COUNT(*) FROM `users` AS u WHERE u.referred_by = users.refer_code) AS registration_count 
            FROM `users` $join " . $where . " 
            ORDER BY " . $sort . " " . $order . " 
            LIMIT " . $offset . "," . $limit;
    $db->sql($sql);
    $res = $db->getResult();

    $bulkData = array();
    $bulkData['total'] = $total;
    $rows = array();
    $tempRow = array();
    foreach ($res as $row) {
        $operate = '<a href="edit-user.php?id=' . $row['id'] . '" class="text text-primary"><i class="fa fa-edit"></i>Edit</a>';
        $operate .= ' <a class="text text-danger" href="delete-user.php?id=' . $row['id'] . '"><i class="fa fa-trash"></i>Delete</a>';
        $tempRow['id'] = $row['id'];
        $tempRow['name'] = $row['name'];
        $tempRow['mobile'] = $row['mobile'];
        $tempRow['password'] = $row['password'];
        $tempRow['dob'] = $row['dob'];
        $tempRow['email'] = $row['email'];
        $tempRow['city'] = $row['city'];
        $tempRow['device_id'] = $row['device_id'];
        $tempRow['refer_code'] = $row['refer_code'];
        $tempRow['referred_by'] = $row['referred_by'];
        $tempRow['c_referred_by'] = $row['c_referred_by'];
        $tempRow['earn'] = $row['earn'];
        $tempRow['total_referrals'] = $row['total_referrals'];
        $tempRow['today_codes'] = $row['today_codes'];
        $tempRow['total_codes'] = $row['total_codes'];
        $tempRow['balance'] = $row['balance'];
        $tempRow['history'] = $row['history'];
        $tempRow['withdrawal'] = $row['withdrawal'];
        $tempRow['recharge'] = $row['recharge'];
        $tempRow['total_recharge'] = $row['total_recharge'];
        $tempRow['joined_date'] = $row['joined_date'];
        $tempRow['registration_count'] = $row['registration_count']; // Add the registration count here
        if($row['status']==0)
            $tempRow['status'] ="<label class='label label-default'>Not Verify</label>";
        elseif($row['status']==1)
            $tempRow['status']="<label class='label label-success'>Verified</label>";        
        else
            $tempRow['status']="<label class='label label-danger'>Blocked</label>";
        if($row['code_generate']==1)
            $tempRow['code_generate'] ="<p class='text text-success'>enabled</p>";
        else
            $tempRow['code_generate']="<p class='text text-danger'>disabled</p>";

        if($row['withdrawal_status']==1)
            $tempRow['withdrawal_status'] ="<p class='text text-success'>enabled</p>";
        else
            $tempRow['withdrawal_status']="<p class='text text-danger'>disabled</p>";
        $tempRow['operate'] = $operate;

        $rows[] = $tempRow;
    }
    $bulkData['rows'] = $rows;
    print_r(json_encode($bulkData));
}

if (isset($_GET['table']) && $_GET['table'] == 'staffs') {
    $offset = 0;
    $limit = 10;
    $where = '';
    $sort = 'id';
    $order = 'DESC';
    if ((isset($_GET['status'])  && $_GET['status'] != '')) {
        $status = $db->escapeString($fn->xss_clean($_GET['status']));
        $where .= "AND s.status='$status' ";
    }

    if (isset($_GET['offset']))
        $offset = $db->escapeString($fn->xss_clean($_GET['offset']));
    if (isset($_GET['limit']))
        $limit = $db->escapeString($fn->xss_clean($_GET['limit']));

    if (isset($_GET['sort']))
        $sort = $db->escapeString($fn->xss_clean($_GET['sort']));
    if (isset($_GET['order']))
        $order = $db->escapeString($fn->xss_clean($_GET['order']));

        if (isset($_GET['search']) && !empty($_GET['search'])) {
            $search = $db->escapeString($fn->xss_clean($_GET['search']));
            $where .= "AND (s.mobile LIKE '%" . $search . "%' OR s.name LIKE '%" . $search . "%') ";
        }
        
    if (isset($_GET['sort'])) {
        $sort = $db->escapeString($_GET['sort']);
    }
    if (isset($_GET['order'])) {
        $order = $db->escapeString($_GET['order']);
    }
  
    $join = "LEFT JOIN `branches` b ON s.branch_id = b.id WHERE s.id IS NOT NULL ";

    $sql = "SELECT COUNT(s.id) as total FROM `staffs` s $join " . $where . "";
    $db->sql($sql);
    $res = $db->getResult();
    foreach ($res as $row)
        $total = $row['total'];

    $sql = "SELECT s.id AS id,s.*,b.short_code AS branch FROM `staffs` s $join 
    $where ORDER BY $sort $order LIMIT $offset, $limit";
     $db->sql($sql);
    $res = $db->getResult();

    $bulkData = array();
    $bulkData['total'] = $total;
    $rows = array();
    $tempRow = array();
    foreach ($res as $row) {
        $staff_id = $row['id'];
        $operate = '<a href="edit-staff.php?id=' . $row['id'] . '" class="text text-primary"><i class="fa fa-edit"></i>Edit</a>';
        $operate .= ' <a class="text text-danger" href="delete-staff.php?id=' . $row['id'] . '"><i class="fa fa-trash"></i>Delete</a>';
        $tempRow['id'] = $row['id'];
        $tempRow['name'] = $row['name'];
    
        $tempRow['mobile'] = $row['mobile'];
        $tempRow['balance'] = $row['balance'];
        $tempRow['email'] = $row['email'];
        $tempRow['branch'] = $row['branch'];
        $tempRow['operate'] = $operate;
        $rows[] = $tempRow;
    }
    $bulkData['rows'] = $rows;
    print_r(json_encode($bulkData));
}
if (isset($_GET['table']) && $_GET['table'] == 'suspect_codes') {
    $offset = 0;
    $limit = 10;
    $where = '';
    $sort = 'date';
    $order = 'DESC';
    
    if ((isset($_GET['type']) && $_GET['type'] != '')) {
        $type = $db->escapeString($fn->xss_clean($_GET['type']));
        $where .= " AND l.is_scratched = '$type'";
    }
    
    if (isset($_GET['date']) && $_GET['date'] != '') {
        $date = $db->escapeString($fn->xss_clean($_GET['date']));
        $where .= " AND l.date = '$date'";
    }
    if (isset($_GET['offset']))
        $offset = $db->escapeString($fn->xss_clean($_GET['offset']));
    if (isset($_GET['limit']))
        $limit = $db->escapeString($fn->xss_clean($_GET['limit']));

    if (isset($_GET['sort']))
        $sort = $db->escapeString($fn->xss_clean($_GET['sort']));
    if (isset($_GET['order']))
        $order = $db->escapeString($fn->xss_clean($_GET['order']));

        if (isset($_GET['search']) && !empty($_GET['search'])) {
            $search = $db->escapeString($fn->xss_clean($_GET['search']));
            $where .= "AND (u.mobile LIKE '%" . $search . "%' OR u.name LIKE '%" . $search . "%') ";
        }
        
        
    if (isset($_GET['sort'])) {
        $sort = $db->escapeString($_GET['sort']);
    }
    if (isset($_GET['order'])) {
        $order = $db->escapeString($_GET['order']);
    }
   
    $join = "LEFT JOIN `users` u ON l.user_id = u.id WHERE l.id IS NOT NULL " . $where;

    $sql = "SELECT COUNT(l.id) AS total FROM `suspect_codes` l " . $join;
    $db->sql($sql);
    $res = $db->getResult();
    foreach ($res as $row)
        $total = $row['total'];
   
     $sql = "SELECT l.id AS id,l.*,u.name,u.mobile  FROM `suspect_codes` l " . $join . " ORDER BY $sort $order LIMIT $offset, $limit";
     $db->sql($sql);
     $res = $db->getResult();

    $bulkData = array();
    $bulkData['total'] = $total;
    $rows = array();
    $tempRow = array();
    foreach ($res as $row) {

       // $operate = '<a href="edit-scratch_cards.php?id=' . $row['id'] . '" class="text text-primary"><i class="fa fa-edit"></i>Edit</a>';
       // $operate .= ' <a class="text text-danger" href="delete-scratch_cards.php?id=' . $row['id'] . '"><i class="fa fa-trash"></i>Delete</a>';
        $tempRow['id'] = $row['id'];
        $tempRow['name'] = $row['name'];
        $tempRow['mobile'] = $row['mobile'];
        $tempRow['codes'] = $row['codes'];
        $tempRow['total_text'] = $row['total_text'];
        $tempRow['typed_text'] = $row['typed_text'];
        $tempRow['datetime'] = $row['datetime'];
        $rows[] = $tempRow;
    }
    $bulkData['rows'] = $rows;
    print_r(json_encode($bulkData));
}
if (isset($_GET['table']) && $_GET['table'] == 'branches') {
    $offset = 0;
    $limit = 10;
    $where = '';
    $sort = 'id';
    $order = 'DESC';
    if (isset($_GET['offset']))
        $offset = $db->escapeString($fn->xss_clean($_GET['offset']));
    if (isset($_GET['limit']))
        $limit = $db->escapeString($fn->xss_clean($_GET['limit']));

    if (isset($_GET['sort']))
        $sort = $db->escapeString($fn->xss_clean($_GET['sort']));
    if (isset($_GET['order']))
        $order = $db->escapeString($fn->xss_clean($_GET['order']));

    if (isset($_GET['search']) && !empty($_GET['search'])) {
        $search = $db->escapeString($fn->xss_clean($_GET['search']));
        $where .= "WHERE name like '%" . $search . "%' OR short_code like '%" . $search . "%' OR min_withdrawal like '%" . $search . "%'";
    }
    if (isset($_GET['sort'])) {
        $sort = $db->escapeString($_GET['sort']);
    }
    if (isset($_GET['order'])) {
        $order = $db->escapeString($_GET['order']);
    }
    $sql = "SELECT COUNT(`id`) as total FROM `branches`" . $where;
    $db->sql($sql);
    $res = $db->getResult();
    foreach ($res as $row)
        $total = $row['total'];

    $sql = "SELECT * FROM branches " . $where . " ORDER BY " . $sort . " " . $order . " LIMIT " . $offset . "," . $limit;
    $db->sql($sql);
    $res = $db->getResult();

    $bulkData = array();
    $bulkData['total'] = $total;
    $rows = array();
    $tempRow = array();
    foreach ($res as $row) {
        
         $operate = '<a href="edit-branches.php?id=' . $row['id'] . '" class="text text-primary"><i class="fa fa-edit"></i>Edit</a>';
        $operate .= ' <a class="text text-danger" href="delete-branches.php?id=' . $row['id'] . '"><i class="fa fa-trash"></i>Delete</a>';
        $tempRow['id'] = $row['id'];
        $tempRow['name'] = $row['name'];
        $tempRow['mobile'] = $row['mobile'];
        $tempRow['short_code'] = $row['short_code'];
        $tempRow['operate'] = $operate;
        $rows[] = $tempRow;
    }
    $bulkData['rows'] = $rows;
    print_r(json_encode($bulkData));
}
if (isset($_GET['table']) && $_GET['table'] == 'bank_details') {
    $offset = 0;
    $limit = 10;
    $where = '';
    $sort = 'id';
    $order = 'DESC';
    if (isset($_GET['offset']))
        $offset = $db->escapeString($fn->xss_clean($_GET['offset']));
    if (isset($_GET['limit']))
        $limit = $db->escapeString($fn->xss_clean($_GET['limit']));

    if (isset($_GET['sort']))
        $sort = $db->escapeString($fn->xss_clean($_GET['sort']));
    if (isset($_GET['order']))
        $order = $db->escapeString($fn->xss_clean($_GET['order']));

    if (isset($_GET['search']) && !empty($_GET['search'])) {
        $search = $db->escapeString($fn->xss_clean($_GET['search']));
        $where .= "WHERE u.name like '%" . $search . "%' OR b.account_num like '%" . $search . "%' OR b.holder_name like '%" . $search . "%' OR b.bank like '%" . $search . "%' OR b.branch like '%" . $search . "%'";
    }
    if (isset($_GET['sort'])) {
        $sort = $db->escapeString($_GET['sort']);
    }
    if (isset($_GET['order'])) {
        $order = $db->escapeString($_GET['order']);
    }
    $join = "LEFT JOIN `users` u ON b.user_id = u.id";

    $sql = "SELECT COUNT(b.id) as total FROM `bank_details` b $join " . $where . "";
    $db->sql($sql);
    $res = $db->getResult();
    foreach ($res as $row)
        $total = $row['total'];

    $sql = "SELECT b.id AS id,b.*,u.name FROM `bank_details` b $join 
    $where ORDER BY $sort $order LIMIT $offset, $limit";
     $db->sql($sql);
    $res = $db->getResult();

    $bulkData = array();
    $bulkData['total'] = $total;
    $rows = array();
    $tempRow = array();
    foreach ($res as $row) {
        $operate = ' <a href="edit-bank_detail.php?id=' . $row['id'] . '"><i class="fa fa-edit"></i>Edit</a>';
        $operate .= ' <a class="text text-danger" href="delete-bank_detail.php?id=' . $row['id'] . '"><i class="fa fa-trash"></i>Delete</a>';
        $tempRow['id'] = $row['id'];
        $tempRow['name'] = $row['name'];
        $tempRow['account_num'] = $row['account_num'];
        $tempRow['holder_name'] = $row['holder_name'];
        $tempRow['bank'] = $row['bank'];
        $tempRow['branch'] = $row['branch'];
        $tempRow['ifsc'] = $row['ifsc'];
        $tempRow['operate'] = $operate;
        $rows[] = $tempRow;
    }
    $bulkData['rows'] = $rows;
    print_r(json_encode($bulkData));
}
if (isset($_GET['table']) && $_GET['table'] == 'withdrawals') {
    $offset = 0;
    $limit = 10;
    $where = '';
    $sort = 'w.id';
    $order = 'DESC';
    
    if ((isset($_GET['status']) && $_GET['status'] != '')) {
        $status = $db->escapeString($fn->xss_clean($_GET['status']));
        $where .= "AND l.status = '$status' ";
    }
    
    if ((isset($_GET['user_id']) && $_GET['user_id'] != '')) {
        $user_id = $db->escapeString($fn->xss_clean($_GET['user_id']));
        $where .= "AND l.user_id = '$user_id' ";
    }
    
    if (isset($_GET['offset']))
        $offset = $db->escapeString($fn->xss_clean($_GET['offset']));
    
    if (isset($_GET['limit']))
        $limit = $db->escapeString($fn->xss_clean($_GET['limit']));
    
    if (isset($_GET['sort']))
        $sort = $db->escapeString($fn->xss_clean($_GET['sort']));
    
    if (isset($_GET['order']))
        $order = $db->escapeString($fn->xss_clean($_GET['order']));
    
    if (isset($_GET['search']) && !empty($_GET['search'])) {
        $search = $db->escapeString($fn->xss_clean($_GET['search']));
        $where .= "AND (u.mobile LIKE '%" . $search . "%') ";
    }
    
    if (isset($_GET['sort'])) {
        $sort = $db->escapeString($_GET['sort']);
    }
    
    if (isset($_GET['order'])) {
        $order = $db->escapeString($_GET['order']);
    }
    
    if ($_SESSION['role'] == 'Super Admin') {
        $join = "LEFT JOIN users u ON w.user_id = u.id LEFT JOIN bank_details b ON b.user_id = w.user_id WHERE (w.withdrawal_type != 'sa_withdrawal') ";
    } else {
        $refer_code = $_SESSION['refer_code'];
        $join = "LEFT JOIN users u ON w.user_id = u.id LEFT JOIN bank_details b ON b.user_id = w.user_id WHERE (u.refer_code REGEXP '^$refer_code' AND (w.withdrawal_type != 'sa_withdrawal')) ";
    }
  
    $join = "LEFT JOIN `users` u ON l.user_id = u.id LEFT JOIN `bank_details` b ON u.id = b.user_id WHERE l.id IS NOT NULL " . $where;

    $sql = "SELECT COUNT(l.id) AS total FROM `withdrawals` l " . $join;
    $db->sql($sql);
    $res = $db->getResult();
    foreach ($res as $row)
        $total = $row['total'];
    
    $sql = "SELECT l.id AS id, l.*, u.name, u.mobile, u.balance, u.total_codes, u.today_codes, u.total_referrals, u.referred_by, u.refer_code, b.branch, b.bank, b.ifsc, b.account_num, b.holder_name FROM `withdrawals` l " . $join . " ORDER BY $sort $order LIMIT $offset, $limit";
    
    $db->sql($sql);
    $res = $db->getResult();
    
    

    $bulkData = array();
    $bulkData['total'] = $total;
    $rows = array();
    $tempRow = array();
    foreach ($res as $row) {
        // $operate = ' <a class="text text-danger" href="delete-withdrawal.php?id=' . $row['id'] . '"><i class="fa fa-trash"></i>Delete</a>';
        // $operate .= ' <a href="edit-withdrawal.php?id=' . $row['id'] . '"><i class="fa fa-edit"></i>Edit</a>';
        $checkbox = '<input type="checkbox" name="enable[]" value="'.$row['id'].'">';
        $tempRow['id'] = $row['id'];
        $tempRow['name'] = $row['name'];
        $tempRow['balance'] = $row['balance'];
        $tempRow['mobile'] = $row['mobile'];
        $tempRow['amount'] = $row['amount'];
        $tempRow['datetime'] = $row['datetime'];
        $tempRow['total_codes'] = $row['total_codes'];
        $tempRow['today_codes'] = $row['today_codes'];
        $tempRow['total_referrals'] = $row['total_referrals'];
        $tempRow['referred_by'] = $row['referred_by'];
        $tempRow['refer_code'] = $row['refer_code'];
        $tempRow['type'] = $row['type'];
        $tempRow['branch'] = $row['branch'];
        $tempRow['bank'] = $row['bank'];
        $tempRow['ifsc'] = $row['ifsc'];
        $tempRow['account_num'] = ','.$row['account_num'].',';
        $tempRow['holder_name'] = $row['holder_name'];
        $tempRow['column'] = $checkbox;
        if($row['status']==1)
            $tempRow['status'] ="<p class='text text-success'>Paid</p>";
        elseif($row['status']==0)
            $tempRow['status']="<p class='text text-primary'>Unpaid</p>";
        else
            $tempRow['status']="<p class='text text-danger'>Cancelled</p>";
        // $tempRow['operate'] = $operate;
        $rows[] = $tempRow;
    }
    $bulkData['rows'] = $rows;
    print_r(json_encode($bulkData));
}

if (isset($_GET['table']) && $_GET['table'] == 'advance_withdrawals') {
    $offset = 0;
    $limit = 10;
    $where = '';
    $sort = 'w.id';
    $order = 'DESC';

    if ((isset($_GET['status']) && $_GET['status'] != '')) {
        $status = $db->escapeString($fn->xss_clean($_GET['status']));
        $where .= "AND w.status = '$status' ";
    }

    if ((isset($_GET['user_id']) && $_GET['user_id'] != '')) {
        $user_id = $db->escapeString($fn->xss_clean($_GET['user_id']));
        $where .= "AND w.user_id = '$user_id'";
    }

    if (isset($_GET['offset']))
        $offset = $db->escapeString($fn->xss_clean($_GET['offset']));

    if (isset($_GET['limit']))
        $limit = $db->escapeString($fn->xss_clean($_GET['limit']));

    if (isset($_GET['sort']))
        $sort = $db->escapeString($fn->xss_clean($_GET['sort']));

    if (isset($_GET['order']))
        $order = $db->escapeString($fn->xss_clean($_GET['order']));

    if ($_SESSION['role'] == 'Super Admin') {
        $join = "LEFT JOIN users u ON w.user_id = u.id LEFT JOIN bank_details b ON b.user_id = w.user_id WHERE w.id IS NOT NULL AND w.withdrawal_type = 'sa_withdrawal'";
    } else {
        $refer_code = $_SESSION['refer_code'];
        $join = "LEFT JOIN users u ON w.user_id = u.id LEFT JOIN bank_details b ON b.user_id = w.user_id WHERE u.refer_code REGEXP '^$refer_code' AND w.withdrawal_type = 'sa_withdrawal'";
    }

    $sql = "SELECT COUNT(w.id) as total FROM `withdrawals` w $join " . $where . "";
    $db->sql($sql);
    $res = $db->getResult();
    foreach ($res as $row)
        $total = $row['total'];

    $sql = "SELECT w.id AS id, w.*, w.type AS type, u.name, u.total_codes, u.total_referrals, u.balance, u.mobile, u.referred_by, u.refer_code, DATEDIFF('$currentdate', u.joined_date) AS history, b.branch, b.bank, b.account_num, b.ifsc, b.holder_name FROM `withdrawals` w $join
    $where ORDER BY $sort $order LIMIT $offset, $limit";
    $db->sql($sql);
    $res = $db->getResult();

    $bulkData = array();
    $bulkData['total'] = $total;
    $rows = array();
    $tempRow = array();
    foreach ($res as $row) {
        // $operate = ' <a class="text text-danger" href="delete-withdrawal.php?id=' . $row['id'] . '"><i class="fa fa-trash"></i>Delete</a>';
        // $operate .= ' <a href="edit-withdrawal.php?id=' . $row['id'] . '"><i class="fa fa-edit"></i>Edit</a>';
        $checkbox = '<input type="checkbox" name="enable[]" value="'.$row['id'].'">';
        $tempRow['id'] = $row['id'];
        $tempRow['name'] = $row['name'];
        $tempRow['amount'] = $row['amount'];
        $tempRow['datetime'] = $row['datetime'];
        $tempRow['account_num'] = ','.$row['account_num'].',';
        $tempRow['holder_name'] = $row['holder_name'];
        $tempRow['bank'] = $row['bank'];
        $tempRow['branch'] = $row['branch'];
        $tempRow['total_codes'] = $row['total_codes'];
        $tempRow['total_referrals'] = $row['total_referrals'];
        $tempRow['mobile'] = $row['mobile'];
        $tempRow['balance'] = $row['balance'];
        $tempRow['referred_by'] = $row['referred_by'];
        $tempRow['refer_code'] = $row['refer_code'];
        $tempRow['history'] = $row['history'];
        $tempRow['type'] = $row['type'];
        $tempRow['ifsc'] = $row['ifsc'];
        $tempRow['column'] = $checkbox;
        if($row['status']==1)
            $tempRow['status'] ="<p class='text text-success'>Paid</p>";
        elseif($row['status']==0)
            $tempRow['status']="<p class='text text-primary'>Unpaid</p>";
        else
            $tempRow['status']="<p class='text text-danger'>Cancelled</p>";
        // $tempRow['operate'] = $operate;
        $rows[] = $tempRow;
    }
    $bulkData['rows'] = $rows;
    print_r(json_encode($bulkData));
}

if (isset($_GET['table']) && $_GET['table'] == 'top_coders') {

    $where = '';  

    $offset = (isset($_GET['offset']) && !empty(trim($_GET['offset'])) && is_numeric($_GET['offset'])) ? $db->escapeString(trim($fn->xss_clean($_GET['offset']))) : 0;
    $limit = (isset($_GET['limit']) && !empty(trim($_GET['limit'])) && is_numeric($_GET['limit'])) ? $db->escapeString(trim($fn->xss_clean($_GET['limit']))) : 5;
    $sort = (isset($_GET['sort']) && !empty(trim($_GET['sort']))) ? $db->escapeString(trim($fn->xss_clean($_GET['sort']))) : 'today_codes';
    $order = (isset($_GET['order']) && !empty(trim($_GET['order']))) ? $db->escapeString(trim($fn->xss_clean($_GET['order']))) : 'DESC';
    
    $currentdate = date('Y-m-d');

    $sql = "SELECT COUNT(users.id) AS total FROM users
    JOIN transactions ON users.id = transactions.user_id WHERE DATE(transactions.datetime) = '$currentdate' AND transactions.type = 'generate'
    GROUP BY users.id ";  
    $db->sql($sql);
    $res = $db->getResult();
    $total = $db->numRows($res);

    $sql = "SELECT * FROM users
    JOIN transactions ON users.id = transactions.user_id WHERE DATE(transactions.datetime) = '$currentdate' AND transactions.type = 'generate'
    GROUP BY users.id ORDER BY today_codes DESC LIMIT " . $offset . "," . $limit;
    $db->sql($sql);
    $res = $db->getResult();

    $data = array();
    $data['total'] = $total;
    $data['rows'] = array();
    $i = 1;
    foreach ($res as $row) {
        $tempRow = array();
        $tempRow['id'] = $i;
        $tempRow['name'] = $row['name'];
        $tempRow['mobile'] = $row['mobile'];
        $tempRow['today_codes'] = $row['today_codes'];
        $tempRow['refer_code'] = $row['refer_code'];
        $tempRow['earn'] = $row['earn'];
        $tempRow['joined_date'] = $row['joined_date'];
        $tempRow['total_referrals'] = $row['total_referrals'];
      
        
        $data['rows'][] = $tempRow;
        $i++;
    }

    header('Content-Type: application/json');  // Set the response content type to JSON
    echo json_encode($data);  // Encode the data as JSON and send it as the response
}



//transactions table goes here
if (isset($_GET['table']) && $_GET['table'] == 'transactions') {
    $offset = 0;
    $limit = 10;
    $where = '';
    $sort = 'id';
    $order = 'DESC';
    if (isset($_GET['offset']))
        $offset = $db->escapeString($fn->xss_clean($_GET['offset']));
    if (isset($_GET['limit']))
        $limit = $db->escapeString($fn->xss_clean($_GET['limit']));

    if (isset($_GET['sort']))
        $sort = $db->escapeString($fn->xss_clean($_GET['sort']));
    if (isset($_GET['order']))
        $order = $db->escapeString($fn->xss_clean($_GET['order']));
    if (isset($_GET['type']) && !empty($_GET['type']))
        $type = $db->escapeString($fn->xss_clean($_GET['type']));
        $where .= "AND t.type = '$type' ";

    if (isset($_GET['search']) && !empty($_GET['search'])) {
        $search = $db->escapeString($fn->xss_clean($_GET['search']));
        $where .= "AND (u.name like '%" . $search . "%' OR t.amount like '%" . $search . "%' OR t.id like '%" . $search . "%'  OR t.type like '%" . $search . "%' OR u.mobile like '%" . $search . "%') ";
    }
    if (isset($_GET['sort'])) {
        $sort = $db->escapeString($_GET['sort']);
    }
    if (isset($_GET['order'])) {
        $order = $db->escapeString($_GET['order']);
    }
    if($_SESSION['role'] == 'Super Admin'){
        $join = "LEFT JOIN `users` u ON t.user_id = u.id WHERE t.id IS NOT NULL ";

    }
    else{
        $refer_code = $_SESSION['refer_code'];
        $join = "LEFT JOIN `users` u ON t.user_id = u.id WHERE u.refer_code REGEXP '^$refer_code' ";
    }
    $sql = "SELECT COUNT(t.id) as total FROM `transactions` t $join " . $where . "";
    $db->sql($sql);
    $res = $db->getResult();
    foreach ($res as $row)
        $total = $row['total'];

    $sql = "SELECT t.id AS id,t.*,u.name,u.mobile FROM `transactions` t $join 
    $where ORDER BY $sort $order LIMIT $offset, $limit";
     $db->sql($sql);
    $res = $db->getResult();

    $bulkData = array();
    $bulkData['total'] = $total;
    $rows = array();
    $tempRow = array();
    foreach ($res as $row) {
        
        $tempRow['id'] = $row['id'];
        $tempRow['name'] = $row['name'];
        $tempRow['mobile'] = $row['mobile'];
        $tempRow['amount'] = $row['amount'];
        $tempRow['codes'] = $row['codes'];
        $tempRow['datetime'] = $row['datetime'];
        $tempRow['type'] = $row['type'];
        $rows[] = $tempRow;
    }
    $bulkData['rows'] = $rows;
    print_r(json_encode($bulkData));
}


if (isset($_GET['table']) && $_GET['table'] == 'notifications') {
    $offset = 0;
    $limit = 10;
    $where = '';
    $sort = 'id';
    $order = 'DESC';
    if (isset($_GET['offset']))
        $offset = $db->escapeString($fn->xss_clean($_GET['offset']));
    if (isset($_GET['limit']))
        $limit = $db->escapeString($fn->xss_clean($_GET['limit']));

    if (isset($_GET['sort']))
        $sort = $db->escapeString($fn->xss_clean($_GET['sort']));
    if (isset($_GET['order']))
        $order = $db->escapeString($fn->xss_clean($_GET['order']));

    if (isset($_GET['search']) && !empty($_GET['search'])) {
        $search = $db->escapeString($fn->xss_clean($_GET['search']));
        $where .= "WHERE title like '%" . $search . "%' OR description like '%" . $search . "%'";
    }
    if (isset($_GET['sort'])) {
        $sort = $db->escapeString($_GET['sort']);
    }
    if (isset($_GET['order'])) {
        $order = $db->escapeString($_GET['order']);
    }
    $sql = "SELECT COUNT(`id`) as total FROM `notifications`" . $where;
    $db->sql($sql);
    $res = $db->getResult();
    foreach ($res as $row)
        $total = $row['total'];

    $sql = "SELECT * FROM notifications " . $where . " ORDER BY " . $sort . " " . $order . " LIMIT " . $offset . "," . $limit;
    $db->sql($sql);
    $res = $db->getResult();

    $bulkData = array();
    $bulkData['total'] = $total;
    $rows = array();
    $tempRow = array();
    foreach ($res as $row) {
        $operate = '<a href="edit-notification.php?id=' . $row['id'] . '" class="text text-primary"><i class="fa fa-edit"></i>Edit</a>';
        $operate .= ' <a class="text text-danger" href="delete-notification.php?id=' . $row['id'] . '"><i class="fa fa-trash"></i>Delete</a>';
        $tempRow['id'] = $row['id'];
        $tempRow['title'] = $row['title'];
        $tempRow['description'] = $row['description'];
        $tempRow['datetime'] = $row['datetime'];
        if(!empty($row['image'])){
            $tempRow['image'] = "<a data-lightbox='category' href='" . $row['image'] . "' data-caption='" . $row['image'] . "'><img src='" . $row['image'] . "' title='" . $row['image'] . "' height='50' /></a>";
        }else{
            $tempRow['image'] = 'No Image';
        }
        $tempRow['operate'] = $operate;
        $rows[] = $tempRow;
    }
    $bulkData['rows'] = $rows;
    print_r(json_encode($bulkData));
}

//admins table goes here
if (isset($_GET['table']) && $_GET['table'] == 'admin') {
    $offset = 0;
    $limit = 10;
    $where = '';
    $sort = 'id';
    $order = 'DESC';
    if (isset($_GET['offset']))
        $offset = $db->escapeString($fn->xss_clean($_GET['offset']));
    if (isset($_GET['limit']))
        $limit = $db->escapeString($fn->xss_clean($_GET['limit']));

    if (isset($_GET['sort']))
        $sort = $db->escapeString($fn->xss_clean($_GET['sort']));
    if (isset($_GET['order']))
        $order = $db->escapeString($fn->xss_clean($_GET['order']));

    if (isset($_GET['search']) && !empty($_GET['search'])) {
        $search = $db->escapeString($fn->xss_clean($_GET['search']));
        $where .= "WHERE name like '%" . $search . "%' OR email like '%" . $search . "%' OR role like '%" . $search . "%' OR status like '%" . $search . "%'";
    }
    if (isset($_GET['sort'])) {
        $sort = $db->escapeString($_GET['sort']);
    }
    if (isset($_GET['order'])) {
        $order = $db->escapeString($_GET['order']);
    }
    $sql = "SELECT COUNT(`id`) as total FROM `admin`" . $where;
    $db->sql($sql);
    $res = $db->getResult();
    foreach ($res as $row)
        $total = $row['total'];

    $sql = "SELECT * FROM admin " . $where . " ORDER BY " . $sort . " " . $order . " LIMIT " . $offset . "," . $limit;
    $db->sql($sql);
    $res = $db->getResult();

    $bulkData = array();
    $bulkData['total'] = $total;
    $rows = array();
    $tempRow = array();
    foreach ($res as $row) {
        $operate = '<a href="edit-admin.php?id=' . $row['id'] . '" class="text text-primary"><i class="fa fa-edit"></i>Edit</a>';
        $operate .= ' <a class="text text-danger" href="delete-admin.php?id=' . $row['id'] . '"><i class="fa fa-trash"></i>Delete</a>';
        $tempRow['id'] = $row['id'];
        $tempRow['name'] = $row['name'];
        $tempRow['password'] = $row['password'];
        $tempRow['role'] = $row['role'];
        $tempRow['email'] = $row['email'];
        $tempRow['refer_code'] = $row['refer_code'];
        if($row['status']==0)
            $tempRow['status'] ="<label class='label label-danger'>Deactive</label>";
        else
            $tempRow['status']="<label class='label label-success'>Active</label>";
        $tempRow['operate'] = $operate;

        $rows[] = $tempRow;
    }
    $bulkData['rows'] = $rows;
    print_r(json_encode($bulkData));
}

if (isset($_GET['table']) && $_GET['table'] == 'manage_devices') {
    $offset = 0;
    $limit = 10;
    $where = '';
    $sort = 'dq.id';
    $order = 'DESC';
    if ((isset($_GET['status'])  && $_GET['status'] != '')) {
        $status = $db->escapeString($fn->xss_clean($_GET['status']));
        $where .= "AND dq.status='$status' ";
    }
    if (isset($_GET['offset']))
        $offset = $db->escapeString($fn->xss_clean($_GET['offset']));
    if (isset($_GET['limit']))
        $limit = $db->escapeString($fn->xss_clean($_GET['limit']));

    if (isset($_GET['sort']))
        $sort = $db->escapeString($fn->xss_clean($_GET['sort']));
    if (isset($_GET['order']))
        $order = $db->escapeString($fn->xss_clean($_GET['order']));

    if (isset($_GET['search']) && !empty($_GET['search'])) {
        $search = $db->escapeString($fn->xss_clean($_GET['search']));
        $where .= "AND u.name like '%" . $search . "%' OR u.mobile like '%" . $search . "%' ";
    }
    if (isset($_GET['sort'])) {
        $sort = $db->escapeString($_GET['sort']);
    }
    if (isset($_GET['order'])) {
        $order = $db->escapeString($_GET['order']);
    }
    $join = "WHERE u.id = dq.user_id ";
    $sql = "SELECT COUNT(*) as total FROM users u,device_requests dq $join " ;
    $db->sql($sql);
    $res = $db->getResult();
    foreach ($res as $row)
        $total = $row['total'];


    $sql = "SELECT *,dq.id AS id,dq.device_id AS device_id,dq.status AS status FROM users u,device_requests dq $join " . $where . " ORDER BY " . $sort . " " . $order . " LIMIT " . $offset . "," . $limit;
    $db->sql($sql);
    $res = $db->getResult();

    $bulkData = array();
    $bulkData['total'] = $total;
    $rows = array();
    $tempRow = array();
    foreach ($res as $row) {
        $operate = ' <a class="btn btn-success" href="verify-device.php?id=' . $row['id'] . '">Verify</a>';

        $tempRow['id'] = $row['id'];
        $tempRow['name'] = $row['name'];
        $tempRow['mobile'] = $row['mobile'];
        $tempRow['device_id'] = $row['device_id'];
        $tempRow['operate'] = $operate;
        if($row['status']==0)
            $tempRow['status'] ="<label class='label label-danger'>Not-verified</label>";
        else
            $tempRow['status']="<label class='label label-success'>Verified</label>";
        $rows[] = $tempRow;
    }
    $bulkData['rows'] = $rows;
    print_r(json_encode($bulkData));
}
if (isset($_GET['table']) && $_GET['table'] == 'manage_users') {
    $offset = 0;
    $limit = 10;
    $where = '';
    $sort = 'id';
    $order = 'DESC';
    if ((isset($_GET['status'])  && $_GET['status'] != '')) {
        $status = $db->escapeString($fn->xss_clean($_GET['status']));
        $where .= "AND status='$status' ";
    }
    if (isset($_GET['offset']))
        $offset = $db->escapeString($fn->xss_clean($_GET['offset']));
    if (isset($_GET['limit']))
        $limit = $db->escapeString($fn->xss_clean($_GET['limit']));

    if (isset($_GET['sort']))
        $sort = $db->escapeString($fn->xss_clean($_GET['sort']));
    if (isset($_GET['order']))
        $order = $db->escapeString($fn->xss_clean($_GET['order']));

    if (isset($_GET['search']) && !empty($_GET['search'])) {
        $search = $db->escapeString($fn->xss_clean($_GET['search']));
        $where .= "AND name like '%" . $search . "%' OR mobile like '%" . $search . "%' OR city like '%" . $search . "%' OR email like '%" . $search . "%' ";
    }
    if (isset($_GET['sort'])) {
        $sort = $db->escapeString($_GET['sort']);
    }
    if (isset($_GET['order'])) {
        $order = $db->escapeString($_GET['order']);
    }


    
    if($_SESSION['role'] == 'Super Admin'){
        $join = "WHERE id IS NOT NULL";
    }
    else{
        $refer_code = $_SESSION['refer_code'];
        $join = "WHERE refer_code REGEXP '^$refer_code'";
    }
    $sql = "SELECT COUNT(`id`) as total FROM `users` $join " . $where;
    $db->sql($sql);
    $res = $db->getResult();
    foreach ($res as $row)
        $total = $row['total'];

        
    $sql = "SELECT *,DATEDIFF( '$currentdate',joined_date) AS history FROM `users` $join " . $where . " ORDER BY " . $sort . " " . $order . " LIMIT " . $offset . "," . $limit;
    $db->sql($sql);
    $res = $db->getResult();

    $bulkData = array();
    $bulkData['total'] = $total;
    $rows = array();
    $tempRow = array();
    foreach ($res as $row) {
        $tempRow['id'] = $row['id'];
        $tempRow['name'] = $row['name'];
        $tempRow['mobile'] = $row['mobile'];
        $tempRow['password'] = $row['password'];
        $tempRow['dob'] = $row['dob'];
        $tempRow['email'] = $row['email'];
        $tempRow['city'] = $row['city'];
        $tempRow['device_id'] = $row['device_id'];
        $tempRow['refer_code'] = $row['refer_code'];
        $tempRow['referred_by'] = $row['referred_by'];
        $tempRow['earn'] = $row['earn'];
        $tempRow['total_referrals'] = $row['total_referrals'];
        $tempRow['today_codes'] = $row['today_codes'];
        $tempRow['total_codes'] = $row['total_codes'];
        $tempRow['balance'] = $row['balance'];
        $tempRow['history'] = $row['history'];
        $tempRow['withdrawal'] = $row['withdrawal'];
        if($row['status']==0)
            $operate = '<a href="verify-user.php?id=' . $row['id'] . '" class="text text-primary">Verify</a>';
        elseif($row['status']==1)
            $operate = "<label class='label label-success'>Verified</label>";   
        else
            $operate = "<label class='label label-danger'>Blocked</label>";
        if($row['code_generate']==1)
            $tempRow['code_generate'] ="<p class='text text-success'>enabled</p>";
        else
            $tempRow['code_generate']="<p class='text text-danger'>disabled</p>";

        if($row['withdrawal_status']==1)
            $tempRow['withdrawal_status'] ="<p class='text text-success'>enabled</p>";
        else
            $tempRow['withdrawal_status']="<p class='text text-danger'>disabled</p>";
        $tempRow['operate'] = $operate;

        $rows[] = $tempRow;
    }
    $bulkData['rows'] = $rows;
    print_r(json_encode($bulkData));
}

//search withdrawals
if (isset($_GET['table']) && $_GET['table'] == 'search_withdrawals') {
    $offset = 0;
    $limit = 10;
    $where = '';
    $sort = 'w.id';
    $order = 'DESC';
    if ((isset($_GET['mobile']))) {
        $mobile = $db->escapeString($fn->xss_clean($_GET['mobile']));
        $where .= "AND u.mobile='$mobile' ";
    }
    if (isset($_GET['offset']))
        $offset = $db->escapeString($fn->xss_clean($_GET['offset']));
    if (isset($_GET['limit']))
        $limit = $db->escapeString($fn->xss_clean($_GET['limit']));

    if (isset($_GET['sort']))
        $sort = $db->escapeString($fn->xss_clean($_GET['sort']));
    if (isset($_GET['order']))
        $order = $db->escapeString($fn->xss_clean($_GET['order']));

    if (isset($_GET['search']) && !empty($_GET['search'])) {
        $search = $db->escapeString($fn->xss_clean($_GET['search']));
        $where .= "AND u.mobile like '%" . $search . "%'";
    }
    if (isset($_GET['sort'])) {
        $sort = $db->escapeString($_GET['sort']);
    }
    if (isset($_GET['order'])) {
        $order = $db->escapeString($_GET['order']);
    }
    $join = "WHERE w.user_id = u.id AND w.user_id = b.user_id AND w.status = 0 ";

    $sql = "SELECT COUNT(w.id) as total FROM `withdrawals` w,`users` u,`bank_details` b $join ". $where ."";
    $db->sql($sql);
    $res = $db->getResult();
    foreach ($res as $row)
        $total = $row['total'];

    $sql = "SELECT w.id AS id,w.*,u.name,u.total_codes,u.total_referrals,u.balance,u.mobile,u.referred_by,u.refer_code,DATEDIFF( '$currentdate',u.joined_date) AS history,b.branch,b.bank,b.account_num,b.ifsc,b.holder_name FROM `withdrawals` w,`users` u,`bank_details` b $join
    $where ORDER BY $sort $order LIMIT $offset, $limit";
     $db->sql($sql);
    $res = $db->getResult();

    $bulkData = array();
    $bulkData['total'] = $total;
    $rows = array();
    $tempRow = array();
    foreach ($res as $row) {
        // $operate = ' <a class="text text-danger" href="delete-withdrawal.php?id=' . $row['id'] . '"><i class="fa fa-trash"></i>Delete</a>';
        // $operate .= ' <a href="edit-withdrawal.php?id=' . $row['id'] . '"><i class="fa fa-edit"></i>Edit</a>';
        $checkbox = '<input type="checkbox" name="enable[]" value="'.$row['id'].'">';
        $tempRow['id'] = $row['id'];
        $tempRow['name'] = $row['name'];
        $tempRow['amount'] = $row['amount'];
        $tempRow['datetime'] = $row['datetime'];
        $tempRow['account_num'] = ','.$row['account_num'].',';
        $tempRow['holder_name'] = $row['holder_name'];
        $tempRow['bank'] = $row['bank'];
        $tempRow['branch'] = $row['branch'];
        $tempRow['total_codes'] = $row['total_codes'];
        $tempRow['total_referrals'] = $row['total_referrals'];
        $tempRow['mobile'] = $row['mobile'];
        $tempRow['balance'] = $row['balance'];
        $tempRow['referred_by'] = $row['referred_by'];
        $tempRow['refer_code'] = $row['refer_code'];
        $tempRow['history'] = $row['history'];
        $tempRow['ifsc'] = $row['ifsc'];
        $tempRow['column'] = $checkbox;
        if($row['status']==1)
            $tempRow['status'] ="<p class='text text-success'>Paid</p>";
        elseif($row['status']==0)
            $tempRow['status']="<p class='text text-primary'>Unpaid</p>";
        else
            $tempRow['status']="<p class='text text-danger'>Cancelled</p>";
        // $tempRow['operate'] = $operate;
        $rows[] = $tempRow;
    }
    $bulkData['rows'] = $rows;
    print_r(json_encode($bulkData));
}


//salary advance Transactions table goes here
if (isset($_GET['table']) && $_GET['table'] == 'salary_advance_transactions') {
    $offset = 0;
    $limit = 10;
    $where = '';
    $sort = 'id';
    $order = 'DESC';
    // if (isset($_GET['type']) && !empty($_GET['type'])){
    //     $type = $db->escapeString($fn->xss_clean($_GET['type']));
    //     $where .= "AND t.type = '$type' ";
    // }
      
    if (isset($_GET['offset']))
        $offset = $db->escapeString($fn->xss_clean($_GET['offset']));
    if (isset($_GET['limit']))
        $limit = $db->escapeString($fn->xss_clean($_GET['limit']));

    if (isset($_GET['sort']))
        $sort = $db->escapeString($fn->xss_clean($_GET['sort']));
    if (isset($_GET['order']))
        $order = $db->escapeString($fn->xss_clean($_GET['order']));

    if (isset($_GET['search']) && !empty($_GET['search'])) {
        $search = $db->escapeString($fn->xss_clean($_GET['search']));
        $where .= "AND u.name like '%" . $search . "%' OR t.amount like '%" . $search . "%' OR t.id like '%" . $search . "%'  OR t.type like '%" . $search . "%' OR u.mobile like '%" . $search . "%' ";
    }
    if (isset($_GET['sort'])) {
        $sort = $db->escapeString($_GET['sort']);
    }
    if (isset($_GET['order'])) {
        $order = $db->escapeString($_GET['order']);
    }
    $join = "LEFT JOIN `users` u ON t.refer_user_id = u.id WHERE t.id IS NOT NULL ";

    $sql = "SELECT COUNT(t.id) as total FROM `salary_advance_trans` t $join " . $where . "";
    $db->sql($sql);
    $res = $db->getResult();
    foreach ($res as $row)
        $total = $row['total'];

    $sql = "SELECT t.id AS id,t.*,u.name,u.mobile FROM `salary_advance_trans` t $join 
    $where ORDER BY $sort $order LIMIT $offset, $limit";
     $db->sql($sql);
    $res = $db->getResult();

    $bulkData = array();
    $bulkData['total'] = $total;
    $rows = array();
    $tempRow = array();
    foreach ($res as $row) {
        
        $tempRow['id'] = $row['id'];
        $tempRow['name'] = $row['name'];
        $tempRow['mobile'] = $row['mobile'];
        $tempRow['amount'] = $row['amount'];
        $tempRow['datetime'] = $row['datetime'];
        $tempRow['type'] = $row['type'];
        $rows[] = $tempRow;
    }
    $bulkData['rows'] = $rows;
    print_r(json_encode($bulkData));
}

if (isset($_GET['table']) && $_GET['table'] == 'payments') {

    $offset = 0;
    $limit = 10;
    $where = '';
    $sort = 'id';
    $order = 'DESC';
    if (isset($_GET['offset']))
        $offset = $db->escapeString($_GET['offset']);
    if (isset($_GET['limit']))
        $limit = $db->escapeString($_GET['limit']);
    if (isset($_GET['sort']))
        $sort = $db->escapeString($_GET['sort']);
    if (isset($_GET['order']))
        $order = $db->escapeString($_GET['order']);

        if (isset($_GET['search']) && !empty($_GET['search'])) {
            $search = $db->escapeString($_GET['search']);
            $where .= "WHERE id like '%" . $search . "%' OR mobile like '%" . $search . "%' OR order_id like '%" . $search . "%'";
        }
    if (isset($_GET['sort'])){
        $sort = $db->escapeString($_GET['sort']);
    }
    if (isset($_GET['order'])){
        $order = $db->escapeString($_GET['order']);
    }
    $sql = "SELECT COUNT(`id`) as total FROM `payments` ";
    $db->sql($sql);
    $res = $db->getResult();
    foreach ($res as $row)
        $total = $row['total'];
   
    $sql = "SELECT * FROM payments " . $where . " ORDER BY " . $sort . " " . $order . " LIMIT " . $offset . ", " . $limit;
    $db->sql($sql);
    $res = $db->getResult();

    $bulkData = array();
    $bulkData['total'] = $total;
    
    $rows = array();
    $tempRow = array();

    foreach ($res as $row) {

        
        //$operate = ' <a href="edit-payments.php?id=' . $row['id'] . '"><i class="fa fa-edit"></i>Edit</a>';
        $operate = ' <a class="text text-danger" href="delete-payments.php?id=' . $row['id'] . '"><i class="fa fa-trash"></i>Delete</a>';
        $tempRow['id'] = $row['id'];
        $tempRow['mobile'] = $row['mobile'];
        $tempRow['order_id'] = $row['order_id'];
        $tempRow['product_id'] = $row['product_id'];
        $tempRow['amount'] = $row['amount'];
        $tempRow['claim'] = $row['claim'];
        $tempRow['datetime'] = $row['datetime'];
        $tempRow['operate'] = $operate;
        $rows[] = $tempRow;
    }
    $bulkData['rows'] = $rows;
    print_r(json_encode($bulkData));
}

//repayments table goes here
if (isset($_GET['table']) && $_GET['table'] == 'repayments') {
    $offset = 0;
    $limit = 10;
    $where = '';
    $sort = 'id';
    $order = 'DESC';
    if ((isset($_GET['user_id']) && $_GET['user_id'] != '')) {
        $user_id = $db->escapeString($fn->xss_clean($_GET['user_id']));
        $where .= "AND r.user_id = '$user_id'";
    }
      
    if (isset($_GET['offset']))
        $offset = $db->escapeString($fn->xss_clean($_GET['offset']));
    if (isset($_GET['limit']))
        $limit = $db->escapeString($fn->xss_clean($_GET['limit']));

    if (isset($_GET['sort']))
        $sort = $db->escapeString($fn->xss_clean($_GET['sort']));
    if (isset($_GET['order']))
        $order = $db->escapeString($fn->xss_clean($_GET['order']));

    if (isset($_GET['search']) && !empty($_GET['search'])) {
        $search = $db->escapeString($fn->xss_clean($_GET['search']));
        $where .= "AND u.name like '%" . $search . "%' OR r.amount like '%" . $search . "%' OR r.id like '%" . $search . "%'  OR r.due_date like '%" . $search . "%' OR u.mobile like '%" . $search . "%' ";
    }
    if (isset($_GET['sort'])) {
        $sort = $db->escapeString($_GET['sort']);
    }
    if (isset($_GET['order'])) {
        $order = $db->escapeString($_GET['order']);
    }
    $join = "LEFT JOIN `users` u ON r.user_id = u.id WHERE r.id IS NOT NULL ";

    $sql = "SELECT COUNT(r.id) as total FROM `repayments` r $join " . $where . "";
    $db->sql($sql);
    $res = $db->getResult();
    foreach ($res as $row)
        $total = $row['total'];

    $sql = "SELECT r.id AS id,r.*,u.name,u.mobile,r.status AS status FROM `repayments` r $join 
    $where ORDER BY $sort $order LIMIT $offset, $limit";
     $db->sql($sql);
    $res = $db->getResult();

    $bulkData = array();
    $bulkData['total'] = $total;
    $rows = array();
    $tempRow = array();
    foreach ($res as $row) {
        if($row['status']==0){
            $checkbox = '<input type="checkbox" name="enable[]" value="'.$row['id'].'">';
        }
        else{
            $checkbox = '';
        }
        $tempRow['id'] = $row['id'];
        $tempRow['name'] = $row['name'];
        $tempRow['mobile'] = $row['mobile'];
        $tempRow['amount'] = $row['amount'];
        $tempRow['due_date'] = $row['due_date'];
        if($row['status']==1)
              $tempRow['status']="<p class='text text-success'>Paid</p>";        
       else
             $tempRow['status']="<p class='text text-danger'>Unpaid</p>";
        $tempRow['column'] = $checkbox;
        $rows[] = $tempRow;
    }
    $bulkData['rows'] = $rows;
    print_r(json_encode($bulkData));
}

//employees table goes here
if (isset($_GET['table']) && $_GET['table'] == 'employees') {
    $offset = 0;
    $limit = 10;
    $where = '';
    $sort = 'id';
    $order = 'DESC';
    if (isset($_GET['offset']))
        $offset = $db->escapeString($fn->xss_clean($_GET['offset']));
    if (isset($_GET['limit']))
        $limit = $db->escapeString($fn->xss_clean($_GET['limit']));

    if (isset($_GET['sort']))
        $sort = $db->escapeString($fn->xss_clean($_GET['sort']));
    if (isset($_GET['order']))
        $order = $db->escapeString($fn->xss_clean($_GET['order']));

    if (isset($_GET['search']) && !empty($_GET['search'])) {
        $search = $db->escapeString($fn->xss_clean($_GET['search']));
        $where .= "WHERE name like '%" . $search . "%' OR id like '%" . $search . "%' OR mobile like '%" . $search . "%' OR email like '%" . $search . "%'";
    }
    if (isset($_GET['sort'])) {
        $sort = $db->escapeString($_GET['sort']);
    }
    if (isset($_GET['order'])) {
        $order = $db->escapeString($_GET['order']);
    }
    $sql = "SELECT COUNT(`id`) as total FROM `employees`" . $where;
    $db->sql($sql);
    $res = $db->getResult();
    foreach ($res as $row)
        $total = $row['total'];

    $sql = "SELECT * FROM employees " . $where . " ORDER BY " . $sort . " " . $order . " LIMIT " . $offset . "," . $limit;
    $db->sql($sql);
    $res = $db->getResult();

    $bulkData = array();
    $bulkData['total'] = $total;
    $rows = array();
    $tempRow = array();
    foreach ($res as $row) {
        $operate = '<a href="edit-employee.php?id=' . $row['id'] . '" class="text text-primary"><i class="fa fa-edit"></i>Edit</a>';
        $tempRow['id'] = $row['id'];
        $tempRow['name'] = $row['name'];
        $tempRow['mobile'] = $row['mobile'];
        $tempRow['email'] = $row['email'];
        $tempRow['password'] =$row['password'];
        if($row['status']==1)
            $tempRow['status']="<label class='label label-success'>Active</label>";        
        else
            $tempRow['status']="<label class='label label-danger'>Inactive</label>";
        $tempRow['operate'] = $operate;
        $rows[] = $tempRow;
    }
    $bulkData['rows'] = $rows;
    print_r(json_encode($bulkData));
}

//faq table goes here
if (isset($_GET['table']) && $_GET['table'] == 'faq') {
    $offset = 0;
    $limit = 10;
    $where = '';
    $sort = 'id';
    $order = 'DESC';
    if (isset($_GET['offset']))
        $offset = $db->escapeString($fn->xss_clean($_GET['offset']));
    if (isset($_GET['limit']))
        $limit = $db->escapeString($fn->xss_clean($_GET['limit']));

    if (isset($_GET['sort']))
        $sort = $db->escapeString($fn->xss_clean($_GET['sort']));
    if (isset($_GET['order']))
        $order = $db->escapeString($fn->xss_clean($_GET['order']));

    if (isset($_GET['search']) && !empty($_GET['search'])) {
        $search = $db->escapeString($fn->xss_clean($_GET['search']));
        $where .= "WHERE question like '%" . $search . "%' OR id like '%" . $search . "%' OR answer like '%" . $search . "%'";
    }
    if (isset($_GET['sort'])) {
        $sort = $db->escapeString($_GET['sort']);
    }
    if (isset($_GET['order'])) {
        $order = $db->escapeString($_GET['order']);
    }
    $sql = "SELECT COUNT(`id`) as total FROM `faq`" . $where;
    $db->sql($sql);
    $res = $db->getResult();
    foreach ($res as $row)
        $total = $row['total'];

    $sql = "SELECT * FROM faq " . $where . " ORDER BY " . $sort . " " . $order . " LIMIT " . $offset . "," . $limit;
    $db->sql($sql);
    $res = $db->getResult();

    $bulkData = array();
    $bulkData['total'] = $total;
    $rows = array();
    $tempRow = array();
    foreach ($res as $row) {
        $operate = '<a href="edit-faq.php?id=' . $row['id'] . '" class="text text-primary"><i class="fa fa-edit"></i>Edit</a>';
        $operate .= ' <a class="text text-danger" href="delete-faq.php?id=' . $row['id'] . '"><i class="fa fa-trash"></i>Delete</a>';
        $tempRow['id'] = $row['id'];
        $tempRow['question'] = $row['question'];
        $tempRow['answer'] = $row['answer'];
        $tempRow['operate'] = $operate;
        $rows[] = $tempRow;
    }
    $bulkData['rows'] = $rows;
    print_r(json_encode($bulkData));
}

//youtube links table goes here
if (isset($_GET['table']) && $_GET['table'] == 'youtube_links') {
    $offset = 0;
    $limit = 10;
    $where = '';
    $sort = 'id';
    $order = 'DESC';
    if (isset($_GET['offset']))
        $offset = $db->escapeString($fn->xss_clean($_GET['offset']));
    if (isset($_GET['limit']))
        $limit = $db->escapeString($fn->xss_clean($_GET['limit']));

    if (isset($_GET['sort']))
        $sort = $db->escapeString($fn->xss_clean($_GET['sort']));
    if (isset($_GET['order']))
        $order = $db->escapeString($fn->xss_clean($_GET['order']));

    if (isset($_GET['search']) && !empty($_GET['search'])) {
        $search = $db->escapeString($fn->xss_clean($_GET['search']));
        $where .= "WHERE name like '%" . $search . "%' OR id like '%" . $search . "%' OR link like '%" . $search . "%'";
    }
    if (isset($_GET['sort'])) {
        $sort = $db->escapeString($_GET['sort']);
    }
    if (isset($_GET['order'])) {
        $order = $db->escapeString($_GET['order']);
    }
    $sql = "SELECT COUNT(`id`) as total FROM `youtube_link`" . $where;
    $db->sql($sql);
    $res = $db->getResult();
    foreach ($res as $row)
        $total = $row['total'];

    $sql = "SELECT * FROM youtube_link " . $where . " ORDER BY " . $sort . " " . $order . " LIMIT " . $offset . "," . $limit;
    $db->sql($sql);
    $res = $db->getResult();

    $bulkData = array();
    $bulkData['total'] = $total;
    $rows = array();
    $tempRow = array();
    foreach ($res as $row) {
        $operate = '<a href="edit-youtube_links.php?id=' . $row['id'] . '" class="text text-primary"><i class="fa fa-edit"></i>Edit</a>';
        $operate .= ' <a class="text text-danger" href="delete-youtube_links.php?id=' . $row['id'] . '"><i class="fa fa-trash"></i>Delete</a>';
        $tempRow['id'] = $row['id'];
        $tempRow['name'] = $row['name'];
        $tempRow['link'] = $row['link'];
        $tempRow['operate'] = $operate;
        $rows[] = $tempRow;
    }
    $bulkData['rows'] = $rows;
    print_r(json_encode($bulkData));
}

if (isset($_GET['table']) && $_GET['table'] == 'system-users') {

    $offset = 0;
    $limit = 10;
    $sort = 'id';
    $order = 'ASC';
    $where = '';
    $condition = '';
    if (isset($_GET['offset']))
        $offset = $db->escapeString($fn->xss_clean($_GET['offset']));
    if (isset($_GET['limit']))
        $limit = $db->escapeString($fn->xss_clean($_GET['limit']));

    if (isset($_GET['sort']))
        $sort = $db->escapeString($fn->xss_clean($_GET['sort']));
    if (isset($_GET['order']))
        $order = $db->escapeString($fn->xss_clean($_GET['order']));

    if (isset($_GET['search']) && $_GET['search'] != '') {
        $search = $db->escapeString($fn->xss_clean($_GET['search']));
        $where = " Where `id` like '%" . $search . "%' OR `username` like '%" . $search . "%' OR `email` like '%" . $search . "%' OR `role` like '%" . $search . "%' OR `date_created` like '%" . $search . "%'";
    }
    if ($_SESSION['role'] != 'Super Admin') {
        if (empty($where)) {
            $condition .= ' where created_by=' . $_SESSION['id'];
        } else {
            $condition .= ' and created_by=' . $_SESSION['id'];
        }
    }

    $sql = "SELECT COUNT(id) as total FROM `admin`" . $where . "" . $condition;
    $db->sql($sql);
    $res = $db->getResult();
    foreach ($res as $row)
        $total = $row['total'];

    $sql = "SELECT * FROM `admin`" . $where . "" . $condition . " ORDER BY " . $sort . " " . $order . " LIMIT " . $offset . ", " . $limit;
    $db->sql($sql);
    $res = $db->getResult();

    $bulkData = array();
    $bulkData['total'] = $total;
    $rows = array();
    $tempRow = array();

    foreach ($res as $row) {
        if ($row['created_by'] != 0) {
            $sql = "SELECT username FROM admin WHERE id=" . $row['created_by'];
            $db->sql($sql);
            $created_by = $db->getResult();
        }

        if ($row['role'] != 'Super Admin') {
            $operate = "<a class='btn btn-xs btn-primary edit-system-user' data-id='" . $row['id'] . "' data-toggle='modal' data-target='#editSystemUserModal' title='Edit'><i class='fa fa-pencil-square-o'></i></a>";
            $operate .= " <a class='btn btn-xs btn-danger delete-system-user' data-id='" . $row['id'] . "' title='Delete'><i class='fa fa-trash-o'></i></a>";
        } else {
            $operate = '';
        }
        if ($row['role'] == 'Super Admin') {
            $role = '<span class="label label-success">Super Admin</span>';
        }
        if ($row['role'] == 'Admin') {
            $role = '<span class="label label-primary">Admin</span>';
        }
        if ($row['role'] == 'editor') {
            $role = '<span class="label label-warning">Editor</span>';
        }
        $tempRow['id'] = $row['id'];
        $tempRow['username'] = $row['username'];
        $tempRow['email'] = $row['email'];
        $tempRow['password'] = $row['password'];
        $tempRow['refer_code'] = $row['refer_code'];
        $tempRow['permissions'] = $row['permissions'];
        $tempRow['role'] = $role;
        $tempRow['created_by_id'] = $row['created_by'] != 0 ? $row['created_by'] : '-';
        $tempRow['created_by'] = $row['created_by'] != 0 ? $created_by[0]['username'] : '-';
        $tempRow['date_created'] = date('d-m-Y h:i:sa', strtotime($row['date_created']));
        $tempRow['operate'] = $operate;

        $rows[] = $tempRow;
    }
    $bulkData['rows'] = $rows;
    print_r(json_encode($bulkData));
}
   //plan
   if (isset($_GET['table']) && $_GET['table'] == 'leaves') {

    $offset = 0;
    $limit = 10;
    $where = '';
    $sort = 'id';
    $order = 'DESC';
    if (isset($_GET['offset']))
        $offset = $db->escapeString($_GET['offset']);
    if (isset($_GET['limit']))
        $limit = $db->escapeString($_GET['limit']);
    if (isset($_GET['sort']))
        $sort = $db->escapeString($_GET['sort']);
    if (isset($_GET['order']))
        $order = $db->escapeString($_GET['order']);

    if (isset($_GET['search']) && !empty($_GET['search'])) {
        $search = $db->escapeString($_GET['search']);
        $where .= "WHERE id like '%" . $search . "%' OR name like '%" . $search . "%'";
    }
    if (isset($_GET['sort'])){
        $sort = $db->escapeString($_GET['sort']);
    }
    if (isset($_GET['order'])){
        $order = $db->escapeString($_GET['order']);
    }
    $sql = "SELECT COUNT(`id`) as total FROM `leaves` ";
    $db->sql($sql);
    $res = $db->getResult();
    foreach ($res as $row)
        $total = $row['total'];
   
    $sql = "SELECT * FROM leaves " . $where . " ORDER BY " . $sort . " " . $order . " LIMIT " . $offset . ", " . $limit;
    $db->sql($sql);
    $res = $db->getResult();

    $bulkData = array();
    $bulkData['total'] = $total;
    
    $rows = array();
    $tempRow = array();

    foreach ($res as $row) {

        
        $operate = ' <a href="edit-leave.php?id=' . $row['id'] . '"><i class="fa fa-edit"></i>Edit</a>';
        $operate .= ' <a class="text text-danger" href="delete-leave.php?id=' . $row['id'] . '"><i class="fa fa-trash"></i>Delete</a>';
        $tempRow['id'] = $row['id'];
        $tempRow['date'] = $row['date'];
        $tempRow['reason'] = $row['reason'];
        $tempRow['operate'] = $operate;
        $rows[] = $tempRow;
    }
    $bulkData['rows'] = $rows;
    print_r(json_encode($bulkData));
}
   //plan
   if (isset($_GET['table']) && $_GET['table'] == 'plan') {

    $offset = 0;
    $limit = 10;
    $where = '';
    $sort = 'id';
    $order = 'DESC';
    if (isset($_GET['offset']))
        $offset = $db->escapeString($_GET['offset']);
    if (isset($_GET['limit']))
        $limit = $db->escapeString($_GET['limit']);
    if (isset($_GET['sort']))
        $sort = $db->escapeString($_GET['sort']);
    if (isset($_GET['order']))
        $order = $db->escapeString($_GET['order']);

    if (isset($_GET['search']) && !empty($_GET['search'])) {
        $search = $db->escapeString($_GET['search']);
        $where .= "WHERE id like '%" . $search . "%' OR name like '%" . $search . "%'";
    }
    if (isset($_GET['sort'])){
        $sort = $db->escapeString($_GET['sort']);
    }
    if (isset($_GET['order'])){
        $order = $db->escapeString($_GET['order']);
    }
    $sql = "SELECT COUNT(`id`) as total FROM `plan` ";
    $db->sql($sql);
    $res = $db->getResult();
    foreach ($res as $row)
        $total = $row['total'];
   
    $sql = "SELECT * FROM plan " . $where . " ORDER BY " . $sort . " " . $order . " LIMIT " . $offset . ", " . $limit;
    $db->sql($sql);
    $res = $db->getResult();

    $bulkData = array();
    $bulkData['total'] = $total;
    
    $rows = array();
    $tempRow = array();

    foreach ($res as $row) {

        
        $operate = ' <a href="edit-plan.php?id=' . $row['id'] . '"><i class="fa fa-edit"></i>Edit</a>';
        $operate .= ' <a class="text text-danger" href="delete-plan.php?id=' . $row['id'] . '"><i class="fa fa-trash"></i>Delete</a>';
        $tempRow['id'] = $row['id'];
        $tempRow['name'] = $row['name'];
        $tempRow['description'] = $row['description'];
        $tempRow['demo_video'] = $row['demo_video'];
        $tempRow['per_code_cost'] = $row['per_code_cost'];
        $tempRow['monthly_codes'] = $row['monthly_codes'];
        $tempRow['monthly_earnings'] = $row['monthly_earnings'];
        $tempRow['invite_bonus'] = $row['invite_bonus'];
        $tempRow['price'] = $row['price'];
        $tempRow['type'] = $row['type'];
        $tempRow['min_refers'] = $row['min_refers'];
        $tempRow['num_sync'] = $row['num_sync'];
        $tempRow['sub_description'] = $row['sub_description'];
        $tempRow['active_link'] = $row['active_link'];
        $tempRow['refund'] = $row['refund'];
        if(!empty($row['image'])){
            $tempRow['image'] = "<a data-lightbox='category' href='" . $row['image'] . "' data-caption='" . $row['image'] . "'><img src='" . $row['image'] . "' title='" . $row['image'] . "' height='50' /></a>";

        }else{
            $tempRow['image'] = 'No Image';

        }
        $tempRow['operate'] = $operate;
        $rows[] = $tempRow;
    }
    $bulkData['rows'] = $rows;
    print_r(json_encode($bulkData));
}

//user plan
if (isset($_GET['table']) && $_GET['table'] == 'user_plan') {

    $offset = 0;
    $limit = 10;
    $where = '';
    $sort = 'id';
    $order = 'DESC';

    if (isset($_GET['name']) && $_GET['name'] != '') {
        $name = $db->escapeString($fn->xss_clean($_GET['name']));
        $where .= " AND p.name = '$name'";
    }
    if ((isset($_GET['joined_date']) && $_GET['joined_date'] != '')) {
        $joined_date = $db->escapeString($fn->xss_clean($_GET['joined_date']));
        $where .= " AND l.joined_date = '$joined_date'";
    }
    if (isset($_GET['offset']))
        $offset = $db->escapeString($_GET['offset']);
    if (isset($_GET['limit']))
        $limit = $db->escapeString($_GET['limit']);
    if (isset($_GET['sort']))
        $sort = $db->escapeString($_GET['sort']);
    if (isset($_GET['order']))
        $order = $db->escapeString($_GET['order']);

        if (isset($_GET['search']) && !empty($_GET['search'])) {
            $search = $db->escapeString($_GET['search']);
            $where .= " AND (u.id LIKE '%" . $search . "%' OR u.name LIKE '%" . $search ."%'  OR u.mobile LIKE '%" . $search . "%')";
        }
        $join = "LEFT JOIN `users` u ON l.user_id = u.id LEFT JOIN `plan` p ON l.plan_id = p.id WHERE l.id IS NOT NULL " . $where;

        $sql = "SELECT COUNT(l.id) AS total FROM `user_plan` l " . $join;
        $db->sql($sql);
        $res = $db->getResult();
        foreach ($res as $row) {
            $total = $row['total'];
        }
        
        $sql = "SELECT l.id AS id, l.*, u.name AS user_name, u.mobile AS user_mobile, u.referred_by AS user_referred_by, p.name AS plan_name, p.price AS plan_price, p.monthly_codes AS plan_monthly_codes, p.per_code_cost AS plan_per_code_cost, p.monthly_earnings AS plan_monthly_earnings, p.invite_bonus AS plan_invite_bonus FROM `user_plan` l " . $join . " ORDER BY $sort $order LIMIT $offset, $limit";
        $db->sql($sql);
        $res = $db->getResult();
        

    $bulkData = array();
    $bulkData['total'] = $total;
    $rows = array();
    $tempRow = array();
    foreach ($res as $row) {


        
        //$operate = ' <a href="edit-user_plan.php?id=' . $row['id'] . '"><i class="fa fa-edit"></i>Edit</a>';
        $operate = ' <a class="text text-danger" href="delete-user_plan.php?id=' . $row['id'] . '"><i class="fa fa-trash"></i>Delete</a>';
        $tempRow['id'] = $row['id'];
        $tempRow['user_name'] = $row['user_name'];
        $tempRow['user_mobile'] = $row['user_mobile'];
        $tempRow['user_referred_by'] = $row['user_referred_by'];
        $tempRow['plan_name'] = $row['plan_name'];
        $tempRow['plan_price'] = $row['plan_price'];
        $tempRow['plan_monthly_codes'] = $row['plan_monthly_codes'];
        $tempRow['plan_per_code_cost'] = $row['plan_per_code_cost'];
        $tempRow['plan_invite_bonus'] = $row['plan_invite_bonus'];
        $tempRow['plan_monthly_earnings'] = $row['plan_monthly_earnings'];
        $tempRow['income'] = $row['income'];
        $tempRow['joined_date'] = $row['joined_date'];
        $tempRow['operate'] = $operate;
        $rows[] = $tempRow;
    }
    $bulkData['rows'] = $rows;
    print_r(json_encode($bulkData));
}

   //Outsource plan
   if (isset($_GET['table']) && $_GET['table'] == 'outsource_plan') {

    $offset = 0;
    $limit = 10;
    $where = '';
    $sort = 'id';
    $order = 'DESC';
    if (isset($_GET['offset']))
        $offset = $db->escapeString($_GET['offset']);
    if (isset($_GET['limit']))
        $limit = $db->escapeString($_GET['limit']);
    if (isset($_GET['sort']))
        $sort = $db->escapeString($_GET['sort']);
    if (isset($_GET['order']))
        $order = $db->escapeString($_GET['order']);

    if (isset($_GET['search']) && !empty($_GET['search'])) {
        $search = $db->escapeString($_GET['search']);
        $where .= "WHERE id like '%" . $search . "%' OR name like '%" . $search . "%'";
    }
    if (isset($_GET['sort'])){
        $sort = $db->escapeString($_GET['sort']);
    }
    if (isset($_GET['order'])){
        $order = $db->escapeString($_GET['order']);
    }
    $sql = "SELECT COUNT(`id`) as total FROM `outsource_plan` ";
    $db->sql($sql);
    $res = $db->getResult();
    foreach ($res as $row)
        $total = $row['total'];
   
    $sql = "SELECT * FROM outsource_plan " . $where . " ORDER BY " . $sort . " " . $order . " LIMIT " . $offset . ", " . $limit;
    $db->sql($sql);
    $res = $db->getResult();

    $bulkData = array();
    $bulkData['total'] = $total;
    
    $rows = array();
    $tempRow = array();

    foreach ($res as $row) {

        
        $operate = ' <a href="edit-outsource_plan.php?id=' . $row['id'] . '"><i class="fa fa-edit"></i>Edit</a>';
        $operate .= ' <a class="text text-danger" href="delete-outsource_plan.php?id=' . $row['id'] . '"><i class="fa fa-trash"></i>Delete</a>';
        $tempRow['id'] = $row['id'];
        $tempRow['name'] = $row['name'];
        $tempRow['description'] = $row['description'];
        $tempRow['demo_video'] = $row['demo_video'];
        $tempRow['per_code_cost'] = $row['per_code_cost'];
        $tempRow['monthly_codes'] = $row['monthly_codes'];
        $tempRow['monthly_earnings'] = $row['monthly_earnings'];
        $tempRow['yearly_earnings'] = $row['yearly_earnings'];
        $tempRow['invite_bonus'] = $row['invite_bonus'];
        $tempRow['price'] = $row['price'];
        $tempRow['type'] = $row['type'];
        $tempRow['min_refers'] = $row['min_refers'];
        $tempRow['num_sync'] = $row['num_sync'];
        $tempRow['sub_description'] = $row['sub_description'];
        $tempRow['active_link'] = $row['active_link'];
        $tempRow['sync_cost'] = $row['sync_cost'];
        $tempRow['refund'] = $row['refund'];
        if(!empty($row['image'])){
            $tempRow['image'] = "<a data-lightbox='category' href='" . $row['image'] . "' data-caption='" . $row['image'] . "'><img src='" . $row['image'] . "' title='" . $row['image'] . "' height='50' /></a>";

        }else{
            $tempRow['image'] = 'No Image';

        }
        $tempRow['operate'] = $operate;
        $rows[] = $tempRow;
    }
    $bulkData['rows'] = $rows;
    print_r(json_encode($bulkData));
}

//Outsource user plan
if (isset($_GET['table']) && $_GET['table'] == 'outsource_user_plan') {

    $offset = 0;
    $limit = 10;
    $where = '';
    $sort = 'id';
    $order = 'DESC';

    if (isset($_GET['name']) && $_GET['name'] != '') {
        $name = $db->escapeString($fn->xss_clean($_GET['name']));
        $where .= " AND op.name = '$name'";
    }
    if ((isset($_GET['joined_date']) && $_GET['joined_date'] != '')) {
        $joined_date = $db->escapeString($fn->xss_clean($_GET['joined_date']));
        $where .= " AND l.joined_date = '$joined_date'";
    }
    if (isset($_GET['offset']))
        $offset = $db->escapeString($_GET['offset']);
    if (isset($_GET['limit']))
        $limit = $db->escapeString($_GET['limit']);
    if (isset($_GET['sort']))
        $sort = $db->escapeString($_GET['sort']);
    if (isset($_GET['order']))
        $order = $db->escapeString($_GET['order']);

        if (isset($_GET['search']) && !empty($_GET['search'])) {
            $search = $db->escapeString($_GET['search']);
            $where .= " AND (u.id LIKE '%" . $search . "%' OR u.name LIKE '%" . $search ."%'  OR u.mobile LIKE '%" . $search . "%')";
        }
       
    $join = "LEFT JOIN `users` u ON l.user_id = u.id 
            LEFT JOIN `outsource_plan` op ON l.plan_id = op.id 
            WHERE l.id IS NOT NULL $where";

        $sql = "SELECT COUNT(l.id) AS total FROM `outsource_user_plan` l " . $join;
        $db->sql($sql);
        $res = $db->getResult();
        foreach ($res as $row) {
            $total = $row['total'];
        }
        
        $sql = "SELECT l.id AS id, l.*, u.name AS user_name, u.mobile AS user_mobile, u.referred_by AS user_referred_by, op.name AS plan_name, op.price AS plan_price, op.monthly_codes AS plan_monthly_codes, op.per_code_cost AS plan_per_code_cost, op.monthly_earnings AS plan_monthly_earnings, op.yearly_earnings AS plan_yearly_earnings FROM `outsource_user_plan` l " . $join . " ORDER BY $sort $order LIMIT $offset, $limit";
        $db->sql($sql);
        $res = $db->getResult();
        

    $bulkData = array();
    $bulkData['total'] = $total;
    $rows = array();
    $tempRow = array();
    foreach ($res as $row) {


        
        //$operate = ' <a href="edit-user_plan.php?id=' . $row['id'] . '"><i class="fa fa-edit"></i>Edit</a>';
        $operate = ' <a class="text text-danger" href="delete-outsource_user_plan.php?id=' . $row['id'] . '"><i class="fa fa-trash"></i>Delete</a>';
        $tempRow['id'] = $row['id'];
        $tempRow['user_name'] = $row['user_name'];
        $tempRow['user_mobile'] = $row['user_mobile'];
        $tempRow['user_referred_by'] = $row['user_referred_by'];
        $tempRow['plan_name'] = $row['plan_name'];
        $tempRow['plan_price'] = $row['plan_price'];
        $tempRow['plan_monthly_codes'] = $row['plan_monthly_codes'];
        $tempRow['plan_per_code_cost'] = $row['plan_per_code_cost'];
        $tempRow['plan_yearly_earnings'] = $row['plan_yearly_earnings'];
        $tempRow['plan_monthly_earnings'] = $row['plan_monthly_earnings'];
        $tempRow['income'] = $row['income'];
        $tempRow['joined_date'] = $row['joined_date'];
        $tempRow['operate'] = $operate;
        $rows[] = $tempRow;
    }
    $bulkData['rows'] = $rows;
    print_r(json_encode($bulkData));
}
if (isset($_GET['table']) && $_GET['table'] == 'referral_users') {
    $offset = 0;
    $limit = 1; // Change limit to 1 as per the requirement
    $where = 'AND total_referrals > 1 '; // Filter users with total_referrals > 1
    $sort = 'total_referrals'; // Default sorting by total_referrals
    $order = 'DESC'; // Highest total_referrals first

    if ((isset($_GET['date'])  && $_GET['date'] != '')) {
        $date = $db->escapeString($fn->xss_clean($_GET['date']));
        $where .= "AND joined_date='$date' ";
    }
    if (isset($_GET['offset']))
        $offset = $db->escapeString($fn->xss_clean($_GET['offset']));
    if (isset($_GET['limit']))
        $limit = $db->escapeString($fn->xss_clean($_GET['limit']));

    if (isset($_GET['sort']))
        $sort = $db->escapeString($fn->xss_clean($_GET['sort']));
    if (isset($_GET['order']))
        $order = $db->escapeString($fn->xss_clean($_GET['order']));

    if (isset($_GET['search']) && !empty($_GET['search'])) {
        $search = $db->escapeString($fn->xss_clean($_GET['search']));
        $where .= "AND (name like '%" . $search . "%' OR mobile like '%" . $search . "%' OR city like '%" . $search . "%' OR email like '%" . $search . "%' OR refer_code like '%" . $search . "%'  OR referred_by like '%" . $search . "%')";
    }

    if ($_SESSION['role'] == 'Super Admin') {
        $join = "WHERE id IS NOT NULL";
    } else {
        $refer_code = $_SESSION['refer_code'];
        $join = "WHERE refer_code REGEXP '^$refer_code'";
    }

    // Include the total_referrals filter in the query
    $sql = "SELECT COUNT(`id`) as total FROM `users` $join " . $where;
    $db->sql($sql);
    $res = $db->getResult();
    foreach ($res as $row)
        $total = $row['total'];

    // Order by total_referrals DESC and limit to 1 record
    $sql = "SELECT *,DATEDIFF( '$currentdate',joined_date) AS history 
            FROM `users` $join " . $where . " 
            ORDER BY total_referrals DESC, " . $sort . " " . $order . " 
            LIMIT " . $offset . "," . $limit;
    $db->sql($sql);
    $res = $db->getResult();

    $bulkData = array();
    $bulkData['total'] = $total;
    $rows = array();
    $tempRow = array();
    foreach ($res as $row) {
        $tempRow['id'] = $row['id'];
        $tempRow['name'] = $row['name'];
        $tempRow['mobile'] = $row['mobile'];
        $tempRow['refer_code'] = $row['refer_code'];
        $tempRow['total_referrals'] = $row['total_referrals'];
        $rows[] = $tempRow;
    }
    $bulkData['rows'] = $rows;
    print_r(json_encode($bulkData));
}

$db->disconnect();
