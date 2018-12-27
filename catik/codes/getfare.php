<?php 

require "Airlines.php";

$airlines = new Airlines();

	// kuki

	// if (array_key_exists('berangkat', $_POST) && array_key_exists('datang', $_POST) && array_key_exists('adult_passenger_num', $_POST) && array_key_exists('flightID', $_POST)){
	
		

	// 	// var_dump($fwrite_availability);
	// 	// exit;
		
	// }

?>

<?php 	

	if (array_key_exists('session_id', $_POST))
	{
		$airlines = new Airlines($session_id = $_POST['session_id']);

	} else{

		// $airlines->setUserNamePassword("gabon", "Csatversa123");
		// $airlines->setUserNamePassword("ipw", "lordgabon");
		$airlines->setUserNamePassword("Akbaryass", "blokO21");

		echo "sini oyykajdhf";

		$response = $airlines->login();
	} 

	if ( array_key_exists( 'captcha_code', $_POST) && $_POST['captcha_code'] != NULL){
		echo "step 1.5";
		$captcha_code = $_POST['captcha_code'];
		$send_captcha = $airlines->sendCaptcha($captcha_code);

		echo "var_dump captcha ";
		var_dump($send_captcha);
	} else {
		$postencoded = json_encode($_POST);
		$postencoded = base64_encode($postencoded);
		//var_dump($postencoded);

		//setcookie("captcha_availability", $postencoded, time() + (3600 * 24), "/"); 
		$fopen = fopen('getfare_captcha.txt', 'w+');
		$fwrite_availability = fwrite($fopen, $postencoded);
	}

		$fwrite_availability = file_get_contents('getfare_captcha.txt');	
		$fwrite_availability = base64_decode($fwrite_availability);
		$fwrite_availability = json_decode($fwrite_availability);
		$fwrite_availability = $airlines->convert_object_to_array($fwrite_availability);
	
	if (isset($response['status']) && $response['status'] != 'success') {
		echo "Gagal login <br/>";
		echo json_encode($response);
		exit();
	}
	
	$flight_id_multi = explode("##", $fwrite_availability['flightID']);
	$flight_num = sizeof($flight_id_multi);

	//var_dump($flight_num);

	for ($i = 0; $i < $flight_num; $i++) {
		$flight_array[$i] = $flight_id_multi[$i];
	}

	$dateBook = $fwrite_availability['dateFrom'];	
	$date_ret = $fwrite_availability['date_ret'];

	if ($fwrite_availability['flightID_ret'] != "" && $fwrite_availability['date_ret'] ) {
		$return_trip = TRUE;
	} else $return_trip = FALSE;

	//var_dump($return_trip);

	if ( array_key_exists( 'captcha_code', $_POST) && $_POST['captcha_code'] != NULL){
		echo "step 1.5";
		$captcha_code = $_POST['captcha_code'];
		$send_captcha = $airlines->sendCaptcha($captcha_code);

		echo "var_dump captcha ";
		var_dump($send_captcha);
	}	
	
	$schedules = $airlines->getAvailability( $dateBook, $fwrite_availability['berangkat'], $fwrite_availability['datang'], $fwrite_availability['adult_passenger_num'], $fwrite_availability['child_passenger_num'], $fwrite_availability['infant_passenger_num'], $fwrite_availability['date_ret'], $fwrite_availability['flightID_ret'], $fwrite_availability['flightID']);
	
	$captcha_image = $airlines->isCaptchaResponse($schedules);
	
	if ($captcha_image) {

		$session_id = $airlines->saveSession();

		header('Location:getcaptcha.php?captcha=' . $captcha_image . '&session_id='.$session_id . '&origin=getfare', TRUE, 302);
	
	} 


	//var_dump($schedules);

	if($schedules['content'] != NULL && array_key_exists('depart_schedule', $schedules['content'])){
		$schedule = $schedules['content']['depart_schedule'];
		$from_date = $schedules['content']['from_date'];
		$to_date = $schedules['content']['to_date'];
		
	} 
	else {
		echo json_encode("{code: 'error', 'message': 'Array null.'}");
		$airlines->logout();
		die;
	} 

	$origin = $fwrite_availability['berangkat'];
	$destination = $fwrite_availability['datang'];
	$price = $fwrite_availability['harga'];
	$price_ret = $fwrite_availability['harga_ret'];
	
	if ((substr($fwrite_availability['flightID'], 0, 2) == "JT" || substr($fwrite_availability['flightID'], 0, 2) == "ID" || substr($fwrite_availability['flightID'], 0, 2) == "IW") && ($flight_num > 1)){
		echo "masuk pertama -_-";

		$test = $airlines->getBestKeyMultiFlight($schedules, $fwrite_availability['flightID'], $fwrite_availability['berangkat'], $fwrite_availability['datang'], $fwrite_availability['adult_passenger_num'], $fwrite_availability['child_passenger_num'], $fwrite_availability['infant_passenger_num'], $dateBook, $return_trip= FALSE);
		
		$captcha_image = $airlines->isCaptchaResponse($test);
		if ($captcha_image) {
			$session_id = $airlines->saveSession();
			header("Location: " . 'getcaptcha.php?captcha=' . $captcha_image . '&session_id='.$session_id . '&booking_id=' . $booking_id, TRUE, 302);
		} 
		$test = $airlines->getBestKeyMultiFlight($schedules, $fwrite_availability['flightID'], $fwrite_availability['berangkat'], $fwrite_availability['datang'], $fwrite_availability['adult_passenger_num'], $fwrite_availability['child_passenger_num'], $fwrite_availability['infant_passenger_num'], $dateBook, $return_trip= FALSE);

	} else {

		echo "Masuk kedua";
	
		$test = $airlines->getBestKey($fwrite_availability['adult_passenger_num'], $fwrite_availability['child_passenger_num'], $fwrite_availability['infant_passenger_num'], $schedules['content']['depart_schedule'], $fwrite_availability['flightID'], $dateBook, $origin, $destination, $from_date, $to_date, $price, $return_trip= FALSE);

		$captcha_image = $airlines->isCaptchaResponse($test);
		if ($captcha_image) {
			$session_id = $airlines->saveSession();
			header("Location: " . 'getcaptcha.php?captcha=' . $captcha_image . '&session_id='.$session_id . '&booking_id=' . $booking_id, TRUE, 302);
		} 
		
		//$test = $airlines->getBestKey($schedules['content']['depart_schedule'], $fwrite_availability['flightID'], $dateBook, $origin, $destination, $from_date, $to_date, $price, $return_trip= FALSE);

	}
	
	// var_dump($test);
	$all_result = $test["all_result"] ;
	$total = $test["total"];
	$value = $test["value"];
	$publish = $test["publish"] ;
	$tax = $test["tax"];
	$class_code = $test["class_code"];
	$route = $test["route"];
	$time_depart = $test["time_depart"];
	$time_arrive = $test["time_arrive"];
	
	if (preg_replace('/[^\da-z]/i', '', $fwrite_availability['harga']) == preg_replace('/[^\da-z]/i', '', $total) && $total != null ) {
		$response_harga['status'] = "SUCCESS";
		$response_harga['message'] = "Harga pergi ". $total ." udah ok Pak Eko.";
		$response_harga['total'] = $total;
		echo json_encode($response_harga);	
	} else if (preg_replace('/[^\da-z]/i', '', $fwrite_availability['harga']) != preg_replace('/[^\da-z]/i', '', $total ) && $total != null ) {
		$response_harga['status'] = "CONFIRM";
		$response_harga['message'] = "Harga pergi ". $total ." udah ok Pak Eko.";
		$response_harga['total'] = $total;

		$airlines->logout();

		echo json_encode($response_harga);	
	} else {
		$airlines->logout();
		echo '{"status": "DENIED", "message" : "Gak nemu tuh"}';
	}


	if ($fwrite_availability['flightID_ret'] != "" && $fwrite_availability['date_ret'] ){
		echo "masuk if gug ea?";
		if ((substr($fwrite_availability['flightID'], 0, 2) == "JT" || substr($fwrite_availability['flightID'], 0, 2) == "ID" || substr($fwrite_availability['flightID'], 0, 2) == "IW") && $flight_num > 1){
			$test2 = $airlines->getBestKeyMultiFlight($schedules, $fwrite_availability['flightID_ret'], $fwrite_availability['datang'], $fwrite_availability['berangkat'], $fwrite_availability['adult_passenger_num'], $fwrite_availability['child_passenger_num'], $fwrite_availability['infant_passenger_num'], $dateBook, $return_trip = TRUE);
			echo "masuk sini ga?";
		} else {
			$test2 = $airlines->getBestKey($schedules['content']['return_schedule'], $fwrite_availability['flightID_ret'], $dateBook, $destination, $origin, $from_date, $to_date, $price_ret, $return_trip = true);
			echo "masuk ke test2";
		}

		//var_dump($test2);

		$all_result_ret = $test2["all_result"] ;
		$total_ret = $test2["total"];
		$value_ret = $test2["value"];
		$publish_ret = $test2["publish"] ;
		$tax_ret = $test2["tax"];
		$class_code_ret = $test2["class_code"];
		$route_ret = $test2["route"];
		$time_depart_ret = $test2["time_depart"];
		$time_arrive_ret = $test2["time_arrive"];

		// var_dump($fwrite_availability['harga_ret'] . "=" . $total_ret );
		// exit;
		if (preg_replace('/[^\da-z]/i', '', $fwrite_availability['harga_ret']) == preg_replace('/[^\da-z]/i', '', $total_ret)  && $total_ret != null  ) {
			$response_harga_ret['status'] = "SUCCESS";
			$response_harga_ret['message'] = "Harga pulang ". $total_ret ." udah ok Pak Eko.";
			$response_harga_ret['total'] = $total_ret;
			echo json_encode($response_harga_ret);	
		} else if (preg_replace('/[^\da-z]/i', '', $fwrite_availability['harga_ret']) != preg_replace('/[^\da-z]/i', '', $total_ret)  && $total_ret != null  ) {
			$response_harga_ret['status'] = "CONFIRM";
			$response_harga_ret['message'] = "Harga pulang ". $total_ret ." udah ok Pak Eko.";
			$response_harga_ret['total'] = $total_ret;
			echo json_encode($response_harga_ret);	
		} else {
			//$airlines->logout();
			echo '{"status": "DENIED", "message" : "Ret - Gak nemu tuh"}';
		}
	}
	
	
	$session_id = $airlines->saveSession();
	
?>

<!DOCTYPE html>
<html>
<head>
	<title>Booking Ticket</title>
</head>
<body>
	<form action="booking.php" method="post" margin=20%>
		<div>
			<div><input type="hidden" name="adult_passenger_num" readonly value="<?=$_POST['adult_passenger_num']?>" ></div>
			<div><input type="hidden" name="child_passenger_num" readonly value="<?=$_POST['child_passenger_num']?>" ></div>
			<div><input type="hidden" name="infant_passenger_num" readonly value="<?=$_POST['infant_passenger_num']?>" ></div>
			<div><input type="hidden" name="date_from" readonly value="<?=$_POST['dateFrom']?>"></div>
			<div><input type="hidden" name="date_ret" readonly value="<?=$_POST['date_ret']?>" ></div>
			<div><input type="hidden" name="datang" readonly value="<?=$_POST['datang']?>" ></div>
			<div><input type="hidden" name="berangkat" readonly value="<?=$_POST['berangkat']?>" ></div>
			<div><input type="hidden" name="flight_id" readonly value="<?=$_POST['flightID']?>" ></div>
			<div><input type="hidden" name="harga" readonly value="<?=$_POST['harga']?>" ></div>
			<div><input type="hidden" name="price_ret" readonly value="<?=$_POST['harga_ret']?>" ></div>	
			<div><input type="hidden" name="flight_id_ret" readonly value="<?=$_POST['flightID_ret']?>" ></div>
			<div><input type="hidden" name="all_result" readonly value="<?=$all_result?>" ></div>
			<div><input type="hidden" name="total" readonly value="<?=$total?>" ></div>
			<div><input type="hidden" name="publish" readonly value="<?=$publish?>" ></div>
			<div><input type="hidden" name="tax" readonly value="<?=$tax?>" ></div>
			<div><input type="hidden" name="value" readonly value="<?=$value?>" ></div>
			<div><input type="hidden" name="class_code" readonly value="<?=$class_code?>"></div>
			<div><input type="hidden" name="route" readonly value="<?=$route?>" ></div>
			<div><input type="hidden" name="time_depart" readonly value="<?=$time_depart?>" ></div>
    		<div><input type="hidden" name="time_arrive" readonly value="<?=$time_arrive?>" ></div>
			<div><input type="hidden" name="session_id" readonly value="<?=$session_id?>" ></div>
			<div><input type="hidden" name="return_trip" readonly value="<?=$return_trip?>" ></div>
			<div><input type="hidden" name="flight_num" readonly value="<?=$flight_num?>" ></div>
			
			<?php
			if( $return_trip ){
				echo"
				<div><input type='hidden' name='time_depart_ret' readonly value='$time_depart_ret'></div>
				<div><input type='hidden' name='time_arrive_ret' readonly value='$time_arrive_ret'></div>
				<div><input type='hidden' name='value_ret' readonly value='$value_ret' ></div>
				<div><input type='hidden' name='route_ret' readonly value='$route_ret' ></div> 
				<div><input type='hidden' name='publish_ret' readonly value='$publish_ret' ></div>
				<div><input type='hidden' name='tax_ret' readonly value='$tax_ret' ></div>
				<div><input type='hidden' name='total_ret' readonly value='$total_ret' ></div>	
				<div><input type='hidden' name='class_code_ret' readonly value='$class_code_ret' ></div>
				<div><input type='hidden' name='all_result_ret' readonly value='$all_result_ret' ></div>
				<div><input type='hidden' name='publish_ret' readonly value='$publish_ret' ></div>
				<div><input type='hidden' name='tax_ret' readonly value='$tax_ret' ></div>
				<div><input type='hidden' name='total_ret' readonly value='$total_ret' ></div>";	
				
				} 
			?>
		</div>

<?php
	$passenger_num = ((int)$fwrite_availability['adult_passenger_num'] + (int)$fwrite_availability['child_passenger_num'] + (int)$fwrite_availability['infant_passenger_num']);
	for ($i=0; $i<$passenger_num; $i++ ){
		echo 
		"<div><br>Title : <input type='text' name='title".$i."'></div>
		<div><br>Nama Depan : <input type='text' name='fname".$i."'></div>
		<div><br>Nama Belakang : <input type='text' name='lname".$i."'></div>
		<div><br>Tanggal Lahir: <input type='date' name='date_of_birth".$i."' value='<?php echo date('d-m-y'); ?>'</div>";
	}
?>
			<div><br>Email: <input type='text' name='email'></div>
			<div><br>Telefon : <input type='text' name="phone_number0"></div>
			<div><br><input type="submit" name="book" value="Book"></div>

		</div>
	</form>

</body>
</html>
