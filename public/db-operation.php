<?php
session_start();
// include_once('../api-firebase/send-email.php');
include('../includes/crud.php');
$db = new Database();
$db->connect();
$db->sql("SET NAMES 'utf8'");

include_once('../includes/custom-functions.php');
$fn = new custom_functions;
include_once('../includes/functions.php');
$function = new functions;

if (isset($_POST['bulk_data']) && $_POST['bulk_data'] == 1) {
    $error = false;
    $filename = $_FILES["upload_file"]["tmp_name"];
    
    // Validate that the file is a valid CSV
    if ($_FILES["upload_file"]["size"] > 0) {
        $file = fopen($filename, "r");
        $isFirstRow = true; // Flag to skip the header row
        
        while (($emapData = fgetcsv($file, 10000, ",")) !== FALSE) {
            if ($isFirstRow) {
                $isFirstRow = false; // Skip the header row
                continue;
            }

            // Trim and escape each value
            $college = trim($db->escapeString($emapData[0]));
            $name = trim($db->escapeString($emapData[1]));
            $batch_number = trim($db->escapeString($emapData[2]));
            $date = trim($db->escapeString($emapData[3]));

            // Insert data into the database
            $sql = "INSERT INTO random_data (`college`, `name`, `batch_number`, `date`) VALUES ('$college', '$name', '$batch_number', '$date')";
            $db->sql($sql);
        }
        
        fclose($file);
        echo "<p class='alert alert-success'>CSV file is successfully imported!</p><br>";
    } else {
        echo "<p class='alert alert-danger'>Invalid file format! Please upload data in CSV file!</p><br>";
    }
}

if (isset($_POST['bulk_amount']) && $_POST['bulk_amount'] == 1) {
    $count = 0;
    $count1 = 0;
    $error = false;
    $filename = $_FILES["upload_file"]["tmp_name"];
    $result = $fn->validate_image($_FILES["upload_file"], false);
    
    if (!$result) {
        $error = true;
    }
    
    if ($_FILES["upload_file"]["size"] > 0 && !$error) {
        $file = fopen($filename, "r");
        
        while (($emapData = fgetcsv($file, 10000, ",")) !== FALSE) {
            if ($count1 != 0) {
                $mobile = trim($db->escapeString($emapData[0]));
                $amount = trim($db->escapeString($emapData[1]));
                
                $sql = "SELECT id FROM `users` WHERE mobile = '$mobile'"; 
                $db->sql($sql);
                $res = $db->getResult();
            
                if (!empty($res) && count($res) > 0) {
                    $ID = $res[0]['id'];
                    $datetime = date('Y-m-d H:i:s');
                    $type = 'bonus';
            
                    $sql = "INSERT INTO transactions (`user_id`, `amount`, `datetime`, `type`) VALUES ('$ID', '$amount', '$datetime', '$type')";
                    $db->sql($sql);
                    
                    $sql = "UPDATE users SET balance = balance + $amount  WHERE id = $ID"; // $ID is now guaranteed to be valid
                    $db->sql($sql);
                } else {
                    echo "<p class='alert alert-warning'>No user found for mobile number: $mobile</p><br>";
                }
            }

            $count1++;
        }
        
        fclose($file);
        echo "<p class='alert alert-success'>CSV file is successfully imported!</p><br>";
    } else {
        echo "<p class='alert alert-danger'>Invalid file format! Please upload data in CSV file!</p><br>";
    }
}


if (isset($_POST['bulk_refund']) && $_POST['bulk_refund'] == 1) {
    $count = 0;
    $count1 = 0;
    $error = false;
    $filename = $_FILES["upload_file"]["tmp_name"];
    $result = $fn->validate_image($_FILES["upload_file"], false);
    
    if (!$result) {
        $error = true;
    }
    
    if ($_FILES["upload_file"]["size"] > 0 && !$error) {
        $file = fopen($filename, "r");
        
        while (($emapData = fgetcsv($file, 10000, ",")) !== FALSE) {
            if ($count1 != 0) {
                $mobile = trim($db->escapeString($emapData[0]));
                $amount = trim($db->escapeString($emapData[1]));
                
                $sql = "SELECT id FROM `users` WHERE mobile = '$mobile'"; 
                $db->sql($sql);
                $res = $db->getResult();
            
                if (!empty($res) && count($res) > 0) {
                    $ID = $res[0]['id'];
                    $datetime = date('Y-m-d H:i:s');
                    $type = 'refund_wallet';
            
                    $sql = "INSERT INTO transactions (`user_id`, `amount`, `datetime`, `type`) VALUES ('$ID', '$amount', '$datetime', '$type')";
                    $db->sql($sql);
                    
                    $sql = "UPDATE users SET refund_wallet = refund_wallet + $amount  WHERE id = $ID"; // $ID is now guaranteed to be valid
                    $db->sql($sql);
                } else {
                    echo "<p class='alert alert-warning'>No user found for mobile number: $mobile</p><br>";
                }
            }

            $count1++;
        }
        
        fclose($file);
        echo "<p class='alert alert-success'>CSV file is successfully imported!</p><br>";
    } else {
        echo "<p class='alert alert-danger'>Invalid file format! Please upload data in CSV file!</p><br>";
    }
}


if (isset($_POST['bulk_upload']) && $_POST['bulk_upload'] == 1) {
    $count = 0;
    $count1 = 0;
    $error = false;
    $filename = $_FILES["upload_file"]["tmp_name"];
    $result = $fn->validate_image($_FILES["upload_file"], false);
    if (!$result) {
        $error = true;
    }
    if ($_FILES["upload_file"]["size"] > 0  && $error == false) {
        $file = fopen($filename, "r");
        while (($emapData = fgetcsv($file, 10000, ",")) !== FALSE) {
            // print_r($emapData);
            if ($count1 != 0) {
                $emapData[0] = trim($db->escapeString($emapData[0]));
                $emapData[1] = trim($db->escapeString($emapData[1]));          
                $emapData[2] = trim($db->escapeString($emapData[2]));
                $emapData[3] = trim($db->escapeString($emapData[3]));
                $emapData[4] = trim($db->escapeString($emapData[4]));
                $emapData[5] = trim($db->escapeString($emapData[5]));
                $emapData[6] = trim($db->escapeString($emapData[6]));
                $emapData[7] = trim($db->escapeString($emapData[7]));
                $emapData[8] = trim($db->escapeString($emapData[8]));
                $emapData[9] = trim($db->escapeString($emapData[9]));
                $emapData[10] = trim($db->escapeString($emapData[10]));
                $emapData[11] = trim($db->escapeString($emapData[11]));
                $emapData[12] = trim($db->escapeString($emapData[12]));
                $emapData[13] = trim($db->escapeString($emapData[13]));
                $emapData[14] = trim($db->escapeString($emapData[14]));
                $emapData[15] = trim($db->escapeString($emapData[15]));
                $emapData[16] = trim($db->escapeString($emapData[16]));
                $emapData[17] = trim($db->escapeString($emapData[17]));
                $emapData[18] = trim($db->escapeString($emapData[18]));
                $emapData[19] = trim($db->escapeString($emapData[19]));
                $emapData[20] = trim($db->escapeString($emapData[20]));
                $emapData[21] = trim($db->escapeString($emapData[21]));   
                
                $sql = "SELECT id FROM users WHERE mobile = '$emapData[1]'";
                $db->sql($sql);
                $res = $db->getResult();
                $num = $db->numRows($res);
                if ($num >= 1) {
                    echo "<p class='alert alert-danger'>Mobile Number Already Exist</p><br>";
                    return false;

                }
            }

            $count1++;
        }
        fclose($file);
        $file = fopen($filename, "r");
        while (($emapData = fgetcsv($file, 10000, ",")) !== FALSE) {
            // print_r($emapData);
            if ($count1 != 0) {
                $emapData[0] = trim($db->escapeString($emapData[0]));
                $emapData[1] = trim($db->escapeString($emapData[1]));          
                $emapData[2] = trim($db->escapeString($emapData[2]));
                $emapData[3] = trim($db->escapeString($emapData[3]));
                $emapData[4] = trim($db->escapeString($emapData[4]));
                $emapData[5] = trim($db->escapeString($emapData[5]));
                $emapData[6] = trim($db->escapeString($emapData[6]));
                $emapData[7] = trim($db->escapeString($emapData[7]));
                $emapData[8] = trim($db->escapeString($emapData[8]));
                $emapData[9] = trim($db->escapeString($emapData[9]));
                $emapData[10] = trim($db->escapeString($emapData[10]));
                $emapData[11] = trim($db->escapeString($emapData[11]));
                $emapData[12] = trim($db->escapeString($emapData[12]));
                $emapData[13] = trim($db->escapeString($emapData[13]));
                $emapData[14] = trim($db->escapeString($emapData[14]));
                $emapData[15] = trim($db->escapeString($emapData[15]));
                $emapData[16] = trim($db->escapeString($emapData[16]));
                $emapData[17] = trim($db->escapeString($emapData[17]));
                $emapData[18] = trim($db->escapeString($emapData[18]));
                $emapData[19] = trim($db->escapeString($emapData[19]));
                $emapData[20] = trim($db->escapeString($emapData[20]));
                $emapData[21] = trim($db->escapeString($emapData[21])); 
                $emapData[22] = trim($db->escapeString($emapData[22]));  
                $emapData[23] = trim($db->escapeString($emapData[23]));    
                $data = array(
                    'name'=>$emapData[0],
                    'mobile'=>$emapData[1],
                    'password' => $emapData[2],
                    'dob' => $emapData[3],
                    'email' => $emapData[4],
                    'city' => $emapData[5],
                    'referred_by' => $emapData[6],
                    'earn' => $emapData[7],
                    'withdrawal' => $emapData[8],
                    'withdrawal_status' => $emapData[9],
                    'total_referrals' => $emapData[10],
                    'today_codes' => $emapData[11],
                    'total_codes' => $emapData[12],
                    'balance' => $emapData[13],
                     'device_id' => $emapData[14],
                     'status' => $emapData[15],
                     'refer_code' => $emapData[16],
                     'refer_bonus_sent' => $emapData[17],
                     'register_bonus_sent'=> $emapData[18],
                     'code_generate' => $emapData[19],
                    'code_generate_time' => $emapData[20],
                    'fcm_id' => $emapData[21],
                    'last_updated' => $emapData[22],
                    'joined_date' => $emapData[23],
                  
                                 
                );
                $db->insert('users', $data);

            }

            $count1++;
        }
        fclose($file);

        echo "<p class='alert alert-success'>CSV file is successfully imported!</p><br>";
    } else {
        echo "<p class='alert alert-danger'>Invalid file format! Please upload data in CSV file!</p><br>";
    }
}


if (isset($_POST['bulk_update']) && $_POST['bulk_update'] == 1) {
    $count = 0;
    $count1 = 0;
    $error = false;
    $filename = $_FILES["upload_file"]["tmp_name"];
    $result = $fn->validate_image($_FILES["upload_file"], false);
    if (!$result) {
        $error = true;
    }
    if ($_FILES["upload_file"]["size"] > 0  && $error == false) {
        $file = fopen($filename, "r");
        while (($emapData = fgetcsv($file, 10000, ",")) !== FALSE) {
            // print_r($emapData);
            if ($count1 != 0) {
                $emapData[0] = trim($db->escapeString($emapData[0]));
                $emapData[1] = trim($db->escapeString($emapData[1]));          
                $emapData[2] = trim($db->escapeString($emapData[2]));
                $emapData[3] = trim($db->escapeString($emapData[3]));
                $emapData[4] = trim($db->escapeString($emapData[4]));
                $emapData[5] = trim($db->escapeString($emapData[5]));
                $emapData[6] = trim($db->escapeString($emapData[6]));
                $emapData[7] = trim($db->escapeString($emapData[7]));
                $emapData[8] = trim($db->escapeString($emapData[8]));
                $emapData[9] = trim($db->escapeString($emapData[9]));
                $emapData[10] = trim($db->escapeString($emapData[10]));
                $emapData[11] = trim($db->escapeString($emapData[11]));
                $emapData[12] = trim($db->escapeString($emapData[12]));
                $emapData[13] = trim($db->escapeString($emapData[13]));
                $emapData[14] = trim($db->escapeString($emapData[14]));
                $emapData[15] = trim($db->escapeString($emapData[15]));
                $emapData[16] = trim($db->escapeString($emapData[16]));
                $emapData[17] = trim($db->escapeString($emapData[17]));
                $emapData[18] = trim($db->escapeString($emapData[18]));
                $emapData[19] = trim($db->escapeString($emapData[19]));
                $emapData[20] = trim($db->escapeString($emapData[20]));
                $emapData[21] = trim($db->escapeString($emapData[21]));   
                $emapData[22] = trim($db->escapeString($emapData[22]));  
                $emapData[23] = trim($db->escapeString($emapData[23]));   
                // $data = array(
                //     'name'=>$emapData[0],
                //     'mobile'=>$emapData[1],
                //     'password' => $emapData[2],
                //     'dob' => $emapData[3],
                //     'email' => $emapData[4],
                //     'city' => $emapData[5],
                //     'referred_by' => $emapData[6],
                //     'earn' => $emapData[7],
                //     'withdrawal' => $emapData[8],
                //     'withdrawal_status' => $emapData[9],
                //     'total_referrals' => $emapData[10],
                //     'today_codes' => $emapData[11],
                //     'total_codes' => $emapData[12],
                //     'balance' => $emapData[13],
                //      'device_id' => $emapData[14],
                //      'status' => $emapData[15],
                //      'refer_code' => $emapData[16],
                //      'refer_bonus_sent' => $emapData[17],
                //      'register_bonus_sent'=> $emapData[18],
                //      'code_generate' => $emapData[19],
                //     'code_generate_time' => $emapData[20],
                //     'fcm_id' => $emapData[21],
                //     'last_updated' => $emapData[22],
                //     'joined_date' => $emapData[23],
                  
                                 
                // );
                if (preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/",$emapData[23])) {
                    $sql = "UPDATE users SET `joined_date`='$emapData[23]' WHERE mobile= '$emapData[1]'";
                    $db->sql($sql);
                }


            }

            $count1++;
        }
        fclose($file);

        echo "<p class='alert alert-success'>CSV file is updated successfully!</p><br>";
    } else {
        echo "<p class='alert alert-danger'>Invalid file format! Please upload data in CSV file!</p><br>";
    }
}


if (isset($_POST['delete_variant'])) {
    $v_id = $db->escapeString(($_POST['id']));
    $sql = "DELETE FROM product_variant WHERE id = $v_id";
    $db->sql($sql);
    $result = $db->getResult();
    if ($result) {
        echo 1;
    } else {
        echo 0;
    }
}
if (isset($_POST['referred_by_code_change'])) {
    $user_id = $db->escapeString($fn->xss_clean($_POST['user_id']));
    $sql = "SELECT * FROM users WHERE id=" . $user_id;
    $db->sql($sql);
    $res = $db->getResult();
    if (!empty($res)) {
        $referred_by = $res[0]['referred_by'];
        echo $referred_by;
    } else {
        echo "";
    }

}

if (isset($_POST['system_configurations'])) {
    $date = $db->escapeString(date('Y-m-d'));
    $currency = empty($_POST['currency']) ? '₹' : $db->escapeString($fn->xss_clean($_POST['currency']));
    $sql = "UPDATE `settings` SET `value`='" . $currency . "' WHERE `variable`='currency'";
    $db->sql($sql);
    $message = "<div class='alert alert-success'> Settings updated successfully!</div>";
    $_POST['system_timezone_gmt'] = (trim($_POST['system_timezone_gmt']) == '00:00') ? "+" . trim($db->escapeString($fn->xss_clean($_POST['system_timezone_gmt']))) : $db->escapeString($fn->xss_clean($_POST['system_timezone_gmt']));

    if (preg_match("/[a-z]/i", $db->escapeString($fn->xss_clean($_POST['current_version'])))) {
        $_POST['current_version'] = 0;
    }
    if (preg_match("/[a-z]/i", $db->escapeString($fn->xss_clean($_POST['minimum_version_required'])))) {
        $_POST['minimum_version_required'] = 0;
    }
    if (preg_match("/[a-z]/i", $db->escapeString($fn->xss_clean($_POST['delivery_charge'])))) {
        $_POST['delivery_charge'] = 0;
    }
    if (preg_match("/[a-z]/i", $db->escapeString($fn->xss_clean($_POST['min-refer-earn-order-amount'])))) {
        $_POST['min-refer-earn-order-amount'] = 0;
    }
    if (preg_match("/[a-z]/i", $db->escapeString($fn->xss_clean($_POST['min_amount'])))) {
        $_POST['min_amount'] = 0;
    }
    if (preg_match("/[a-z]/i", $db->escapeString($fn->xss_clean($_POST['max-refer-earn-amount'])))) {
        $_POST['max-refer-earn-amount'] = 0;
    }
    if (preg_match("/[a-z]/i", $db->escapeString($fn->xss_clean($_POST['minimum-withdrawal-amount'])))) {
        $_POST['minimum-withdrawal-amount'] = 0;
    }
    if (preg_match("/[a-z]/i", $db->escapeString($fn->xss_clean($_POST['refer-earn-bonus'])))) {
        $_POST['refer-earn-bonus'] = 0;
    }
    // if (preg_match("/[a-z]/i", $db->escapeString($fn->xss_clean($_POST['tax'])))) {
    //     $_POST['tax'] = 0;
    // }
    $_POST['store_address'] = (!empty(trim($_POST['store_address']))) ? preg_replace("/[\r\n]{2,}/", "<br>", $_POST['store_address']) : "";

    $settings_value = json_encode($fn->xss_clean_array($_POST));

    $sql = "UPDATE ssystem_ettings SET value='" . $settings_value . "' WHERE variable='system_timezone'";
    $db->sql($sql);
    $res = $db->getResult();
    $sql_logo = "select value from `ssystem_ettings` where variable='Logo' OR variable='logo'";
    $db->sql($sql_logo);
    $res_logo = $db->getResult();
    $file_name = $_FILES['logo']['name'];

    if (!empty($_FILES["logo"]["tmp_name"]) && $_FILES["logo"]["size"] > 0) {
        $tmp = explode('.', $file_name);
        $ext = end($tmp);
        // $mimetype = mime_content_type($_FILES["logo"]["tmp_name"]);
        // if (!in_array($mimetype, array('image/jpg', 'image/jpeg', 'image/gif', 'image/png'))) {
        //     echo " <span class='label label-danger'>Logo Image type must jpg, jpeg, gif, or png!</span>";
        //     return false;
        // } else {
        $result = $fn->validate_image($fn->xss_clean_array($_FILES["logo"]));
        if (!$result) {
            echo " <span class='label label-danger'>Logo Image type must jpg, jpeg, gif, or png!</span>";
            return false;
        } else {
            $old_image = '../dist/img/' . $res_logo[0]['value'];
            if (file_exists($old_image)) {
                unlink($old_image);
            }

            $target_path = '../dist/img/';
            $filename = "logo." . strtolower($ext);
            $full_path = $target_path . '' . $filename;
            if (!move_uploaded_file($_FILES["logo"]["tmp_name"], $full_path)) {
                $message = "Image could not be uploaded<br/>";
            } else {
                //Update Logo - id = 5
                $sql = "UPDATE `ssystem_ettings` SET `value`='" . $filename . "' WHERE `variable` = 'logo'";
                $db->sql($sql);
            }
        }
    }

    echo "<p class='alert alert-success'>Settings Saved!</p>";
}
if (isset($_POST['add_system_user']) && $_POST['add_system_user'] == 1) {
    $id = $_SESSION['id'];
    $username = $db->escapeString($fn->xss_clean($_POST['username']));
    $email = $db->escapeString($fn->xss_clean($_POST['email']));
    $refer_code = $db->escapeString($fn->xss_clean($_POST['refer_code']));
    if (empty($email)) {
        echo " <label class='alert alert-danger'>Email required!</label>";
        return false;
    }
    $valid_mail = "/^[_\.0-9a-zA-Z-]+@([0-9a-zA-Z][0-9a-zA-Z-]+\.)+[a-zA-Z]{2,6}$/i";
    if (!preg_match($valid_mail, $email)) {
        echo " <label class='alert alert-danger'>Wrong email format!</label>";
        return false;
    }

    $password = $db->escapeString($fn->xss_clean($_POST['password']));
    $role = $db->escapeString($fn->xss_clean($_POST['role']));


    $sql = "SELECT id FROM admin WHERE username='" . $username . "'";
    $db->sql($sql);
    $res = $db->getResult();
    $count = $db->numRows($res);
    if ($count > 0) {
        echo '<label class="alert alert-danger">Username Already Exists!</label>';
        return false;
    }

    $sql = "SELECT id FROM admin WHERE email='" . $email . "'";
    $db->sql($sql);
    $res = $db->getResult();
    $count = $db->numRows($res);
    if ($count > 0) {
        echo '<label class="alert alert-danger">Email Already Exists!</label>';
        return false;
    }
    $permissions['user'] = array("create" => $db->escapeString($fn->xss_clean($_POST['is-create-user'])), "read" => $db->escapeString($fn->xss_clean($_POST['is-read-user'])), "update" => $db->escapeString($fn->xss_clean($_POST['is-update-user'])), "delete" => $db->escapeString($fn->xss_clean($_POST['is-delete-user'])));

    $permissions['transaction'] = array("create" => $db->escapeString($fn->xss_clean($_POST['is-create-transaction'])), "read" => $db->escapeString($fn->xss_clean($_POST['is-read-transaction'])), "update" => $db->escapeString($fn->xss_clean($_POST['is-update-transaction'])), "delete" => $db->escapeString($fn->xss_clean($_POST['is-delete-transaction'])));

    $permissions['withdrawal'] = array("create" => $db->escapeString($fn->xss_clean($_POST['is-create-withdrawal'])), "read" => $db->escapeString($fn->xss_clean($_POST['is-read-withdrawal'])), "update" => $db->escapeString($fn->xss_clean($_POST['is-update-withdrawal'])), "delete" => $db->escapeString($fn->xss_clean($_POST['is-delete-withdrawal'])));

    

    $encoded_permissions = json_encode($permissions);
    $sql = "INSERT INTO admin (username,email,refer_code,password,role,permissions,created_by)
                        VALUES('$username', '$email', '$refer_code','$password', '$role','$encoded_permissions','$id')";
    if ($db->sql($sql)) {
        echo '<label class="alert alert-success">' . $role . ' Added Successfully!</label>';
    } else {
        echo '<label class="alert alert-danger">Some Error Occrred! please try again.</label>';
    }
}
if (isset($_GET['delete_system_user']) && $_GET['delete_system_user'] == 1) {
    $id = $db->escapeString($fn->xss_clean($_GET['id']));
    $sql = "DELETE FROM `admin` WHERE id=" . $id;
    if ($db->sql($sql)) {
        echo 0;
    } else {
        echo 1;
    }
}
if (isset($_POST['update_system_user']) && $_POST['update_system_user'] == 1) {
  
    $id = $db->escapeString($fn->xss_clean($_POST['system_user_id']));
    $permissions['user'] = array("create" => $db->escapeString($fn->xss_clean($_POST['permission-is-create-user'])), "read" => $db->escapeString($fn->xss_clean($_POST['permission-is-read-user'])), "update" => $db->escapeString($fn->xss_clean($_POST['permission-is-update-user'])), "delete" => $db->escapeString($fn->xss_clean($_POST['permission-is-delete-user'])));

    $permissions['transaction'] = array("create" => $db->escapeString($fn->xss_clean($_POST['permission-is-create-transaction'])), "read" => $db->escapeString($fn->xss_clean($_POST['permission-is-read-transaction'])), "update" => $db->escapeString($fn->xss_clean($_POST['permission-is-update-transaction'])), "delete" => $db->escapeString($fn->xss_clean($_POST['permission-is-delete-transaction'])));

    $permissions['withdrawal'] = array("create" => $db->escapeString($fn->xss_clean($_POST['permission-is-create-withdrawal'])), "read" => $db->escapeString($fn->xss_clean($_POST['permission-is-read-withdrawal'])), "update" => $db->escapeString($fn->xss_clean($_POST['permission-is-update-withdrawal'])), "delete" => $db->escapeString($fn->xss_clean($_POST['permission-is-delete-withdrawal'])));

   

    $permissions = json_encode($permissions);
    $sql = "UPDATE admin SET permissions='" . $permissions . "' WHERE id=" . $id;
    if ($db->sql($sql)) {
        echo '<label class="alert alert-success">Updated Successfully!</label>';
    } else {
        echo '<label class="alert alert-danger">Some Error Occrred! please try again.</label>';
    }
}