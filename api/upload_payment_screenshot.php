<?php
header('Access-Control-Allow-Origin: *');
header("Content-Type: application/json");
header("Expires: 0");
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
header("Cache-Control: no-store, no-cache, must-revalidate");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

include_once('../includes/crud.php');

$db = new Database();
$db->connect();
include_once('../includes/functions.php');
$fn = new functions;

if (empty($_POST['user_id'])) {
    $response['success'] = false;
    $response['message'] = "User Id is Empty";
    print_r(json_encode($response));
    return false;
}

$user_id = $db->escapeString($_POST['user_id']);

// Fetch the user details from the database
$user_sql = "SELECT id,name, mobile FROM users WHERE id = '$user_id'"; // Assuming user data is stored in 'users' table
$db->sql($user_sql);
$user_res = $db->getResult();

if (!empty($user_res)) {
    // User found, fetch the name and mobile
    $user_id = $user_res[0]['id'];
    $user_name = $user_res[0]['name'];
    $user_mobile = $user_res[0]['mobile'];
} else {
    $response['success'] = false;
    $response['message'] = "User not found";
    print_r(json_encode($response));
    return false;
}

if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
    $target_dir = "../uploads/"; // Directory to save the uploaded image
    if (!is_dir($target_dir)) {
        mkdir($target_dir, 0777, true); // Create directory if it doesn't exist
    }

    $image_name = time() . "_" . basename($_FILES["image"]["name"]);
    $target_file = $target_dir . $image_name;

    if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
        $image_url = "uploads/" . $image_name;
    
        $current_datetime = date('Y-m-d H:i:s');

        $insert_sql = "INSERT INTO payment_screenshot (user_id, image, status, datetime) VALUES ('$user_id', '$image_url','0', '$current_datetime')";
        $db->sql($insert_sql);

        $response['success'] = true;
        $response['message'] = "Payment Screenshot Image uploaded successfully";

        // Include user information in the response
        $response['user'] = array(
            'id' => $id,
            'user_id' => $user_id,
            'name' => $user_name,
            'mobile' => $user_mobile,
            'image' => DOMAIN_URL . $image_url
        );
    } else {
        $response['success'] = false;
        $response['message'] = "Failed to upload image";
    }
} else {
    $response['success'] = false;
    $response['message'] = "No image file uploaded or an error occurred";
}

print_r(json_encode($response));
?>
