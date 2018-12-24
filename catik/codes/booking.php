<!DOCTYPE html>
<?php

	require "Airlines.php";
	
	$airlines = new Airlines($session_id = $_POST['session_id']);

	// var_dump($_POST);
	// exit;

	if (!array_key_exists('session_id', $_POST))
	{
		echo '{"status" : "DENIED", "message" : "Parameternya session_id bos"}';
		return;
	}

	if ( array_key_exists( 'captcha_code', $_POST) && $_POST['captcha_code'] != NULL){
		echo "step 1.5";
		$captcha_code = $_POST['captcha_code'];
		$send_captcha = $airlines->sendCaptcha($captcha_code);

		echo "var_dump captcha ";
		var_dump($send_captcha);
	} else {
		// if (array_key_exists( 'captcha_booking', $_COOKIE)) {
		// 	unset($_COOKIE['captcha_booking']);
		// }
	
		if (array_key_exists( 'captcha_availability', $_COOKIE)) {
			unset($_COOKIE['captcha_availability']);
		}
	}

	$postencoded = json_encode($_POST);
	$postencoded = base64_encode($postencoded);

	// setcookie("captcha_booking", $postencoded, time() + (60 * 5), "/");
	setcookie($_POST['session_id'], $postencoded, time() + (60 * 5), "/");
?>

<html>
<head>
	<title></title>
</head>
<body>
<?php
	if (array_key_exists( $_POST['session_id'], $_COOKIE)) {
		$cookie_booking = $_COOKIE[ $_POST['session_id'] ];
	} else {
		$cookie_booking = $postencoded;
	}

	$cookie_booking = base64_decode($cookie_booking);
	$cookie_booking = json_decode($cookie_booking);
	$cookie_booking = $airlines->cvf_convert_object_to_array($cookie_booking);

	echo "VAR DUMP COOKIE BOOKING OI!!";
	var_dump($cookie_booking);
	
	
	echo "step 2";

	//$airlines->setUserNamePassword("darwin", "Versa020874");

	var_dump($cookie_booking);
	echo "<br/>";

	if (!array_key_exists("adult_passenger_num", $cookie_booking) || !array_key_exists("child_passenger_num", $cookie_booking) || !array_key_exists("infant_passenger_num", $cookie_booking)) {
		echo "step 2.5";

		//var_dump($cookie_booking['child_passenger_num']);
		
		echo '{"status" : "DENIED", "message" : "Parameternya kurang bos"}';
		return;
	}

	$passenger_num = ((int)$cookie_booking['adult_passenger_num'] + (int)$cookie_booking['child_passenger_num'] + (int)$cookie_booking['infant_passenger_num']);

	/*$data_penumpang = array();
	foreach ($cookie_booking as $key => $value) {
	    echo "Field ".htmlspecialchars($key)." is ".htmlspecialchars($value)."<br>";
	}
	*/
	for ($i=0; $i<$passenger_num; $i++ ) {
		echo "step 3 for";
		if ( !array_key_exists('title'.$i, $cookie_booking) || !array_key_exists('fname'.$i, $cookie_booking) || !array_key_exists('lname'.$i, $cookie_booking)) {
			echo "4";
			
			echo '{"status" : "DENIED", "message" : "Parameter penumpangnya kurang bos"}';
			return;
		}
		
		$data_penumpang[$i]["title"] = $cookie_booking['title'.$i];
		$data_penumpang[$i]["fname"] = $cookie_booking['fname'.$i];
		$data_penumpang[$i]["lname"] = $cookie_booking['lname'.$i];
		$data_penumpang[$i]["date_of_birth"] = $cookie_booking['date_of_birth'.$i];
		
	} 

	if ($cookie_booking['flight_id_ret'] != NULL) {
		
		$return_param = ["flight_id_ret" => $cookie_booking['flight_id_ret'], "route_ret" => $cookie_booking['route_ret'], "date_ret" => $cookie_booking['date_ret'], "class_code_ret" => $cookie_booking['class_code_ret'], "value_ret" => $cookie_booking['value_ret'], "publish_ret" => $cookie_booking['publish_ret'], "tax_ret" => $cookie_booking['tax_ret'], "total_ret" => $cookie_booking['total_ret'],"time_depart_ret" => $cookie_booking['time_depart_ret'], "time_arrive_ret" => $cookie_booking['time_arrive_ret'], "all_result_ret" => $cookie_booking['all_result_ret']];

	} else {
		$return_param = NULL;
	} 	
	
	echo "step 5";
	
	$new_booking = $airlines->getBooking($cookie_booking['adult_passenger_num'],$cookie_booking['child_passenger_num'], $cookie_booking['infant_passenger_num'], $data_penumpang, $cookie_booking['email'], $cookie_booking['phone_number0'], $cookie_booking['value'], $cookie_booking['date_from'], $cookie_booking['berangkat'], $cookie_booking['flight_id'], $cookie_booking['datang'], $cookie_booking['class_code'], $cookie_booking['publish'], $cookie_booking['tax'], $cookie_booking['total'], $cookie_booking['time_depart'], $cookie_booking['time_arrive'], $passenger_num, $cookie_booking['route'], $cookie_booking['time_depart'], $cookie_booking['time_arrive'], $return_param, $all_result = $cookie_booking['all_result'], $cookie_booking['return_trip']);

	$captcha_image = $airlines->isCaptchaResponse($new_booking);

	if ($captcha_image) {

		echo "step 6";

		$session_id = $airlines->saveSession();
		
		$url = 'getcaptcha.php?captcha=' . $captcha_image . '&session_id='. $session_id . '&origin=booking';

		foreach($cookie_booking as $key=>$val) {

			echo "step 7";

			$url = $url . "&" . $key ."=" . $val;
			//var_dump($url);
			header("Location: " . $url, TRUE, 302);
			
		}
			
		$airlines->isCaptchaResponse($new_booking);
		$captcha_image = $airlines->isCaptchaResponse($new_booking);

		if (isset($cookie_booking['captcha_code']) && isset($cookie_booking['session_id']) ){

			echo "step 8";
		
			$new_booking = $airlines->getBooking($data_penumpang, $cookie_booking['email'], $cookie_booking['phone_number0'], $cookie_booking['value'], $cookie_booking['date_from'], $cookie_booking['berangkat'], $cookie_booking['flight_id'], $cookie_booking['datang'], $cookie_booking['class_code'], $cookie_booking['publish'], $cookie_booking['tax'], $cookie_booking['total'], $cookie_booking['time_depart'], $cookie_booking['time_arrive'], $passenger_num, $cookie_booking['route'], $cookie_booking['time_depart'], $cookie_booking['time_arrive'], $return_param, $all_result = $cookie_booking['all_result'], $cookie_booking['return_trip']);

		}
	}

	echo "step 9";

	echo json_encode($new_booking);
	//$airlines->logout();
	
?>
</body>
</html>

