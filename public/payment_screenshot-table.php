<?php
$currentdate = date('Y-m-d');

if (isset($_POST['btnPaid']) && isset($_POST['enable'])) {
    $plan_id = $db->escapeString($fn->xss_clean($_POST['plan_id']));
    $skippedIds = [];

    foreach ($_POST['enable'] as $id) {
        $id = $db->escapeString($fn->xss_clean($id));

        $sql = "SELECT user_id, status FROM payment_screenshot WHERE id = $id";
        $db->sql($sql);
        $res = $db->getResult();

        if (!empty($res)) {
            $status = $res[0]['status'];
            $user_id = $res[0]['user_id'];

            if ($status == 0) {
                // Proceed with activation
                $apiUrl = "https://qrcode.enlightapp.in/api/activate_plan.php";
                $data = ['user_id' => $user_id, 'plan_id' => $plan_id];

                $curl = curl_init($apiUrl);
                curl_setopt($curl, CURLOPT_POST, true);
                curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
                curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

                $response = curl_exec($curl);
                curl_close($curl);

                $responseData = json_decode($response, true);

                if ($responseData !== null && $responseData["success"]) {
                    $updateSql = "UPDATE payment_screenshot SET status = 1 WHERE id = $id";
                    $db->sql($updateSql);
                } else {
                    echo "<script>alert('API Error for ID $id: " . $responseData["message"] . "');</script>";
                }
            } else {
                $skippedIds[] = $id;
            }
        }
    }

    // Show alert for skipped IDs
    if (!empty($skippedIds)) {
        $skippedList = implode(", ", $skippedIds);
        echo "<script>alert('The following IDs were already Paid : $skippedList');</script>";
    }
}

if (isset($_POST['btnCancel']) && isset($_POST['enable'])) {
    $skippedIds = [];

    foreach ($_POST['enable'] as $id) {
        $id = $db->escapeString($fn->xss_clean($id));

        $sql = "SELECT status FROM payment_screenshot WHERE id = $id";
        $db->sql($sql);
        $res = $db->getResult();

        if (!empty($res)) {
            $status = $res[0]['status'];

            if ($status == 0) {
                $sql = "UPDATE payment_screenshot SET status = 2 WHERE id = $id";
                $db->sql($sql);
            } else {
                $skippedIds[] = $id;
            }
        }
    }

    if (!empty($skippedIds)) {
        $skippedList = implode(", ", $skippedIds);
        echo "<script>alert('The following IDs were already Cancelled : $skippedList');</script>";
    }
}

?>

<section class="content-header">
    <h1>Payment Screenshot <small><a href="reports.php"><i class="fa fa-home"></i> Home</a></small></h1>
</section>

<section class="content">
    <form name="withdrawal_form" method="post" enctype="multipart/form-data">
        <div class="row">
            <div class="col-12">
                <div class="box">
                    <div class="box-header">
                        <div class="row">
                            <div class="form-group col-md-3">
                                <h4 class="box-title">Filter by Status</h4>
                                <select id='status' name="status" class='form-control'>
                                    <option value="">All</option>
                                    <option value="0">Unpaid</option>
                                    <option value="1">Paid</option>
                                    <option value="2">Cancelled</option>
                                </select>
                            </div>

                            <div class="form-group col-md-3">
                                <label for="plan_id">Select Plan</label>
                                <select name="plan_id" class="form-control" required id="plan_id">
                                    <option value="">Select Plan</option>
                                    <?php
                                    $sql = "SELECT id, name FROM plan";
                                    $db->sql($sql);
                                    $plans = $db->getResult();
                                    foreach ($plans as $plan) {
                                        echo "<option value='{$plan['id']}'>{$plan['name']}</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="box-body table-responsive">
                        <div class="row">
                            <div class="text-left col-md-2">
                                <input type="checkbox" onchange="checkAll(this)"> Select All
                            </div>
                            <div class="col-md-3">
                                <button type="submit" class="btn btn-success" name="btnPaid" onclick="enablePlanField()">Paid</button>
                                <button type="submit" class="btn btn-danger" name="btnCancel" onclick="disablePlanField()">Cancelled</button>
                            </div>
                        </div>

                        <table id='users_table' class="table table-hover"
                               data-toggle="table"
                               data-url="api-firebase/get-bootstrap-table-data.php?table=payment_screenshot"
                               data-page-list="[5, 10, 20, 50, 100, 200]"
                               data-show-refresh="true"
                               data-show-columns="true"
                               data-side-pagination="server"
                               data-pagination="true"
                               data-search="true"
                               data-trim-on-search="false"
                               data-filter-control="true"
                               data-query-params="queryParams"
                               data-sort-name="id"
                               data-sort-order="desc"
                               data-show-export="true"
                               data-export-types='["txt","csv"]'
                               data-export-options='{
                                   "fileName": "Yellow app-notifications-list-<?= date('d-m-Y') ?>",
                                   "ignoreColumn": ["operate"] 
                               }'>
                            <thead>
                            <tr>
                            <th data-field="column"> All</th>
                                <th data-field="id" data-sortable="true">ID</th>
                                <th data-field="user_id" data-visible="false">User ID</th>
                                <th data-field="user_name" data-sortable="true">User Name</th>
                                <th data-field="status" data-sortable="true">Status</th>
                                <th data-field="image">Payment Screenshot</th>
                                <th data-field="datetime" data-sortable="true">Date</th>
                            </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </form>
</section>

<script>
// Select All checkboxes function
function checkAll(ele) {
    const checkboxes = document.querySelectorAll('#users_table input[type="checkbox"]');
    checkboxes.forEach(checkbox => {
        checkbox.checked = ele.checked;
    });
}

// Disable the plan selection field when Cancel button is clicked
function disablePlanField() {
    document.getElementById('plan_id').disabled = true;
}

// Enable the plan selection field when Paid button is clicked
function enablePlanField() {
    document.getElementById('plan_id').disabled = false;
}

// Filter the table based on status selection
$('#status').on('change', function () {
    $('#users_table').bootstrapTable('refresh');
});

// Query parameters for Bootstrap Table (for pagination, sorting, etc.)
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

// Ensure the page doesn't reload after submission
$(document).ready(function () {
    // Block page refresh when using the form submission
    if (window.history.replaceState) {
        window.history.replaceState(null, null, window.location.href);
    }
});
</script>
