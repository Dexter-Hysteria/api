<?php
date_default_timezone_set('Asia/Manila');
ini_set('max_execution_time', 150);
error_reporting(E_ERROR | E_PARSE);
ini_set('display_errors', '1');

$DB_host = "185.61.137.174";
$DB_user = "firenetv_mtknew";
$DB_pass = "mtknew00";
$DB_name = "firenetv_mtknew";


$mysqli = new MySQLi($DB_host,$DB_user,$DB_pass,$DB_name);
if ($mysqli->connect_error) {
    die('Error : ('. $mysqli->connect_errno .') '. $mysqli->connect_error);
}

function calc_time($seconds) {
		$hours=0;
		$minutes=0;
		$days = (int)($seconds / 86400);
		$seconds -= ($days * 86400);
		if ($seconds) {
			$hours = (int)($seconds / 3600);
			$seconds -= ($hours * 3600);
		}
		if ($seconds) {
			$minutes = (int)($seconds / 60);
			$seconds -= ($minutes * 60);
		}
		$time = array('days'=>(int)$days,
				'hours'=>(int)$hours,
				'minutes'=>(int)$minutes,
				'seconds'=>(int)$seconds);
		return $time;
	}

$values = array();
$username = $_GET['username'];
$username = $mysqli->real_escape_string($username);
$password = $_GET['password'];
$password = $mysqli->real_escape_string($password);

$deviceid = $_GET['device_id'];
$deviceid = $mysqli->real_escape_string($deviceid);

$device_model = $_GET['device_model'];
$device_model = $mysqli->real_escape_string($device_model);


$sql = "SELECT * FROM users WHERE user_name='".$username."' AND auth_vpn='".md5($password)."' AND (duration > 0 OR vip_duration > 0 OR private_duration > 0) AND is_freeze=0 AND is_ban=0 AND status='live' LIMIT 1";
	
$query = $mysqli->query($sql);


if($query->num_rows > 0)
{
    $row = $query->fetch_assoc();
                    $dur = calc_time($row['duration']);
                    $vip_duration = "". $dur['days'] . "Days," . $dur1['hours'] . " Hours," . $dur1['minutes'] . " Minutes Left.";
            		$pdays = $dur['days'] . " days";
            		$phours = $dur['hours'] . " hours";
            		$pminutes = $dur['minutes'] . " minutes";
            		$pseconds = $dur['seconds'] . " seconds";
            		
            		if($row['duration'] == 0){
            			$premuim_duration = "No Subscription";
            		}else{
            			$premuim_duration = strtotime($pdays . $phours . $pminutes . $pseconds);
            			$premuim_duration = date('Y-m-d h:i:s', $premuim_duration);
            		}
        
            		$dur1 = calc_time($row['vip_duration']);
                    $vip_duration1 = "". $dur1['days'] . "Days," . $dur1['hours'] . " Hours," . $dur1['minutes'] . " Minutes Left.";
            		$pdays1 = $dur1['days'] . " days";
            		$phours1 = $dur1['hours'] . " hours";
            		$pminutes1 = $dur1['minutes'] . " minutes";
            		$pseconds1 = $dur1['seconds'] . " seconds";
            		if($row['vip_duration'] == 0){
            			$vip_duration = "No Subscription";
            		}else{
            			$vip_duration = strtotime($pdays1 . $phours1 . $pminutes1 . $pseconds1);
            			$vip_duration = date('Y-m-d h:i:s', $vip_duration);
            		}
            		$dur2 = calc_time($row['private_duration']);
                    $private_duration1 = "". $dur2['days'] . "Days," . $dur2['hours'] . " Hours," . $dur2['minutes'] . " Minutes Left.";
            		$pdays2 = $dur2['days'] . " days";
            		$phours2 = $dur2['hours'] . " hours";
            		$pminutes2 = $dur2['minutes'] . " minutes";
            		$pseconds2 = $dur2['seconds'] . " seconds";
            		if($row['private_duration'] == 0){
            			$private_duration = "No Subscription";
            		}else{
            			$private_duration = strtotime($pdays2 . $phours2 . $pminutes2 . $pseconds2);
            			$private_duration = date('Y-m-d h:i:s', $private_duration);
            		}
            		
            		
    if($premuim_duration != "No Subscription"){
        $expiry = "$premuim_duration";
    }else{
        if($vip_duration != "No Subscription"){
            $expiry = "$vip_duration";
        }else{
            if($private_duration != "No Subscription"){
                $expiry = "$private_duration";
            }
        }
    }
    
    $upline = $mysqli->query("SELECT * FROM users WHERE user_id='".$row['upline']."' LIMIT 1");
    $upline = $upline->fetch_assoc();
    $upline = $upline['is_udp'];
    
    if($upline == 0){
        $message="";
    }else{
        $message="";
    }
    
    
    
    
    if($row['device_model']==""){
    	$update = "UPDATE users SET device_model = '".$device_model."' WHERE user_name = '".$username."'";
    	$mysqli->query($update);
    	$devicestatus = true;
    }elseif($row['device_model']==$device_model){
    	$devicestatus = true; 
    }else{
    	 $devicestatus = false; 
    }
    
	$status = "true";
}else{
	$status = "false";
	$devicestatus = "false"; 
}

 $data =array(
    		'auth' =>$status,
    		'expiry' => $expiry,
    		'message' => $message,
    		'device_match' => $devicestatus
    		);
echo json_encode($data);
$mysqli -> close();
?>