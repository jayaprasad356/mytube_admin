<div id="content" class="container col-md-12">
	<?php
	include_once('includes/custom-functions.php');
	$fn = new custom_functions;
    include_once('includes/functions.php');
    $function = new functions;
	date_default_timezone_set('Asia/Kolkata');

	if (isset($_POST['btnUpdate'])) {
		if (defined('ALLOW_MODIFICATION') && ALLOW_MODIFICATION == 0) {
			echo '<label class="alert alert-danger">This operation is not allowed in demo panel!.</label>';
			return false;
		}

		// $ID = (isset($_GET['id'])) ? $db->escapeString($fn->xss_clean($_GET['id'])) : "";
        if (isset($_GET['id']) && !empty($_GET['id'])) {
            $ID = $db->escapeString($fn->xss_clean($_GET['id']));
        } else { ?>
            <script>
                alert("Something went wrong, No data available.");
                window.location.href = "manage_users.php";
            </script>
        <?php
        }
		$datetime = date('Y-m-d H:i:s');
		$date = date('Y-m-d');
		$sql_query = "SELECT * FROM users WHERE id =" . $ID;
		$db->sql($sql_query);
		$res = $db->getResult();
		$status = $db->escapeString(($_POST['status']));
		$referred_by = $res[0]['referred_by'];
		$refer_bonus_sent = $res[0]['refer_bonus_sent'];
		$earn = $res[0]['earn'];
		$register_bonus_sent = $res[0]['register_bonus_sent'];
		$total_codes = $res[0]['total_codes'];
		$today_codes = $res[0]['today_codes'];
		$balance = $res[0]['balance'];

        if($status == 1 && !empty($referred_by) && $refer_bonus_sent != 1){
            $code_bonus = 1000 * COST_PER_CODE;
            $referral_bonus = REFER_BONUS;
			$refer_sa_balance=200;
            $sql_query = "UPDATE users SET `total_referrals` = total_referrals + 1,`earn` = earn + $referral_bonus,`balance` = balance + $referral_bonus  WHERE refer_code =  '$referred_by' AND status = 1";
            $db->sql($sql_query);
            $res = $db->getResult();
            if (empty($res)) {

                $sql_query = "SELECT * FROM users WHERE refer_code =  '$referred_by'";
                $db->sql($sql_query);
                $res = $db->getResult();
                $num = $db->numRows($res);
                if ($num == 1){
                    $user_id = $res[0]['id'];
                    $sql_query = "INSERT INTO transactions (user_id,amount,datetime,type)VALUES($user_id,$referral_bonus,'$datetime','refer_bonus')";
                    $db->sql($sql_query);
					$sql_query = "INSERT INTO salary_advance_trans (user_id,refer_user_id,amount,datetime,type)VALUES($ID,$user_id,'$refer_sa_balance','$datetime','credit')";
					$db->sql($sql_query);
                    $code_generate = $res[0]['code_generate'];
                    if($code_generate == 1){
                        $sql_query = "UPDATE users SET `earn` = earn + $code_bonus,`balance` = balance + $code_bonus,`today_codes` = today_codes + 1000,`total_codes` = total_codes + 1000 WHERE refer_code =  '$referred_by' AND code_generate = 1";
                        $db->sql($sql_query);
                        $sql_query = "INSERT INTO transactions (user_id,amount,codes,datetime,type)VALUES($user_id,$code_bonus,1000,'$datetime','code_bonus')";
                        $db->sql($sql_query);
                    }
                    $sql_query = "UPDATE users SET refer_bonus_sent = 1 WHERE id =  $ID";
                    $db->sql($sql_query);

                }


            }


        }
        if($status == 1 && $register_bonus_sent != 1){
            $register_bonus = 1000 * COST_PER_CODE;
            $total_codes = $total_codes + 1000;
            $today_codes = $today_codes + 1000;
            $earn = $earn + $register_bonus;
            $balance = $balance + $register_bonus;
            $sql_query = "UPDATE users SET register_bonus_sent = 1 WHERE id =  $ID";
            $db->sql($sql_query);
            $sql_query = "INSERT INTO transactions (user_id,amount,codes,datetime,type)VALUES($ID,$register_bonus,1000,'$datetime','register_bonus')";
            $db->sql($sql_query);
            
        }


		$sql_query = "UPDATE users SET earn='$earn',balance='$balance',total_codes=$total_codes, today_codes=$today_codes,status = $status,joined_date = '$date' WHERE id =  $ID";
        $db->sql($sql_query);
		$verify_result = $db->getResult();
		if (!empty($verify_result)) {
			$verify_result = 0;
		} else {
			$verify_result = 1;
		}
		// if delete data success back to reservation page
		if ($verify_result == 1) {
			header("location: manage_users.php");
		}
	}

	if (isset($_POST['btnNo'])) {
		header("location: manage_users.php");
	}
	if (isset($_POST['btncancel'])) {
		header("location: manage_users.php");
	}

	?>
		<h1>Confirm Action</h1>
		<hr />
		<form method="post">
			<p>Are you sure want to Verify this User?</p>
			<input type="hidden" class="btn btn-success" value="1" name="status" />
			   <input type="submit" class="btn btn-success" value="Verify" name="btnUpdate" />
			<input type="submit" class="btn btn-danger" value="Cancel" name="btnNo" />
		</form>
		<div class="separator"> </div>
</div>

<?php $db->disconnect(); ?>