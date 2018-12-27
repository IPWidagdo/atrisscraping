<!DOCTYPE html>
<?php

	var_dump($_POST);
	// exit;

	require "Airlines.php";
	
	$airlines = new Airlines($session_id = $_POST['session_id']);

	if (!array_key_exists('session_id', $_POST)){

		echo '{"status" : "DENIED", "message" : "Parameternya session_id bos"}';
		return;
	}

	if ( array_key_exists( 'captcha_code', $_POST) && $_POST['captcha_code'] != NULL){
		echo "step 1.5";
		$captcha_code = $_POST['captcha_code'];
		$send_captcha = $airlines->sendCaptcha($captcha_code);
	} else {
		$postencoded = json_encode($_POST);
		$postencoded = base64_encode($postencoded);
		
		$fopen = fopen('booking_captcha.txt', 'w+');
		$postencoded = fwrite($fopen, $postencoded);	
	}
	
	$fwrite_booking = file_get_contents('booking_captcha.txt');
	$fwrite_booking = base64_decode($fwrite_booking);
	$fwrite_booking = json_decode($fwrite_booking);
	$fwrite_booking = $airlines->convert_object_to_array($fwrite_booking);



?>

<html>
<head>
	<title></title>
</head>
<body>
<?php

	echo "step 2";
	echo "<br/>";

	if (!array_key_exists("adult_passenger_num", $fwrite_booking) || !array_key_exists("child_passenger_num", $fwrite_booking) || !array_key_exists("infant_passenger_num", $fwrite_booking)) {
		echo "step 2.5";
		
		echo '{"status" : "DENIED", "message" : "Parameternya kurang bos"}';
		return;
	}

	$passenger_num = ((int)$fwrite_booking['adult_passenger_num'] + (int)$fwrite_booking['child_passenger_num'] + (int)$fwrite_booking['infant_passenger_num']);

	for ($i=0; $i<$passenger_num; $i++ ) {
		echo "step 3 for";
		if ( !array_key_exists('title'.$i, $fwrite_booking) || !array_key_exists('fname'.$i, $fwrite_booking) || !array_key_exists('lname'.$i, $fwrite_booking)) {
			echo "4";
			
			echo '{"status" : "DENIED", "message" : "Parameter penumpangnya kurang bos"}';
			return;
		}
		
		$data_penumpang[$i]["title"] = $fwrite_booking['title'.$i];
		$data_penumpang[$i]["fname"] = $fwrite_booking['fname'.$i];
		$data_penumpang[$i]["lname"] = $fwrite_booking['lname'.$i];
		$data_penumpang[$i]["date_of_birth"] = $fwrite_booking['date_of_birth'.$i];
		
	} 

	if ($fwrite_booking['flight_id_ret'] != NULL) {
		
		$return_param = ["flight_id_ret" => $fwrite_booking['flight_id_ret'], "route_ret" => $fwrite_booking['route_ret'], "date_ret" => $fwrite_booking['date_ret'], "class_code_ret" => $fwrite_booking['class_code_ret'], "value_ret" => $fwrite_booking['value_ret'], "publish_ret" => $fwrite_booking['publish_ret'], "tax_ret" => $fwrite_booking['tax_ret'], "total_ret" => $fwrite_booking['total_ret'],"time_depart_ret" => $fwrite_booking['time_depart_ret'], "time_arrive_ret" => $fwrite_booking['time_arrive_ret'], "all_result_ret" => $fwrite_booking['all_result_ret']];

	} else {
		$return_param = NULL;
	} 	
	
	echo "step 5";

	$new_booking = $airlines->getBooking($fwrite_booking['adult_passenger_num'],$fwrite_booking['child_passenger_num'], $fwrite_booking['infant_passenger_num'], $data_penumpang, $fwrite_booking['email'], $fwrite_booking['phone_number0'], $fwrite_booking['value'], $fwrite_booking['date_from'], $fwrite_booking['berangkat'], $fwrite_booking['flight_id'], $fwrite_booking['datang'], $fwrite_booking['class_code'], $fwrite_booking['publish'], $fwrite_booking['tax'], $fwrite_booking['total'], $fwrite_booking['time_depart'], $fwrite_booking['time_arrive'], $passenger_num, $fwrite_booking['route'], $fwrite_booking['time_depart'], $fwrite_booking['time_arrive'], $return_param, $all_result = $fwrite_booking['all_result'], $fwrite_booking['return_trip']);

	$captcha_image = $airlines->isCaptchaResponse($new_booking);

	if ($captcha_image) {

		echo "step 6";

		$session_id = $airlines->saveSession();
		
		$url = 'getcaptcha.php?captcha=' . $captcha_image . '&session_id='. $session_id . '&origin=booking';
		header("Location: " . $url, TRUE, 302);
			
		//$captcha_image = $airlines->isCaptchaResponse($new_booking);

		// if (isset($fwrite_booking['captcha_code']) && isset($fwrite_booking['session_id']) ){

		// 	echo "step 8";
		
		// 	$new_booking = $airlines->getBooking($data_penumpang, $fwrite_booking['email'], $fwrite_booking['phone_number0'], $fwrite_booking['value'], $fwrite_booking['date_from'], $fwrite_booking['berangkat'], $fwrite_booking['flight_id'], $fwrite_booking['datang'], $fwrite_booking['class_code'], $fwrite_booking['publish'], $fwrite_booking['tax'], $fwrite_booking['total'], $fwrite_booking['time_depart'], $fwrite_booking['time_arrive'], $passenger_num, $fwrite_booking['route'], $fwrite_booking['time_depart'], $fwrite_booking['time_arrive'], $return_param, $all_result = $fwrite_booking['all_result'], $fwrite_booking['return_trip']);

		// }
	}

	echo "step 9";

	echo json_encode($new_booking);
	//$airlines->logout();
	
?>
</body>
</html>

