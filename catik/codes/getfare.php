<?php 
require "Airlines.php";

//var_dump($_POST);
?>

<?php 
	$airlines = new Airlines();
	// $airlines->setUserNamePassword("gabon", "Csatversa123");
	$airlines->setUserNamePassword("ipw", "Vespatia3@");
	$response = $airlines->login();

	if (isset($response['status']) && $response['status'] != 'success') {
		echo "Gagal login <br/>";
		echo json_encode($response);
		exit();
		}
	
	$flight_id_multi = explode("##", $_POST['flightID']);
	$flight_num = sizeof($flight_id_multi);

	for ($i = 0; $i < $flight_num; $i++) {
		$flight_array[$i] = $flight_id_multi[$i];
	}

	$dateBook = $_POST['dateFrom'];	
	$date_ret = $_POST['date_ret'];

	$schedules = $airlines->getAvailability( $dateBook, $_POST['berangkat'], $_POST['datang'], $_POST['adult_passenger_num'], $_POST['child_passenger_num'], $_POST['infant_passenger_num'], $_POST['date_ret'], $_POST['flightID_ret'], $_POST['flightID']);
	
	$foundFlight = false;
	$foundFlight_ret = false;
	
	if(array_key_exists('depart_schedule', $schedules['content'])){
		$schedule = $schedules['content']['depart_schedule'];
		$from_date = $schedules['content']['from_date'];
		$to_date = $schedules['content']['to_date'];
		
	} else {
		echo json_encode("{code:'error', 'message':'Your login name is inuse.'}");
		$airlines->logout();
		die;
	} 

	$origin = $_POST['berangkat'];
	$destination = $_POST['datang'];
	$price = $_POST['harga'];
	$price_ret = $_POST['harga_ret'];
	

	if ($_POST['flightID_ret'] != "" && $_POST['date_ret'] ) {
		$return_trip = TRUE;
	} else $return_trip = FALSE;

	function getBestKey ($airlines, $schedule, $flight_id_search, $dateBook, $origin, $destination, $from_date, $to_date, $price, $return_flight = false) {
		
		$adult_num = $_POST['adult_passenger_num'];
		$child_num = $_POST['child_passenger_num'];
		$infant_num =  $_POST['infant_passenger_num'];

		$foundFlight = false;

		if (is_array($schedule) || is_object($schedule)) {  
			// per schedule
			foreach($schedule as $flightdata) {
				$flight_id = $flightdata['flight'];
								
				if (preg_replace('/[^\da-z]/i', '', $flight_id) != preg_replace('/[^\da-z]/i', '', $flight_id_search))
					continue;
				
				$iter = 0;
				// per class
				while ($iter < 50) {
					if (array_key_exists((string)$iter, $flightdata)) {
						echo "step 5 <br/>";
						$value = $flightdata[(string)$iter]['value']; 
						echo($value . "<br/>");
						$class_code = $flightdata[(string)$iter]['class'];
						$seat = $flightdata[(string)$iter]['seat'];


					
						if ($class_code == "BCLP" || $seat == '0'){

							echo("Masuk sini Mhanx" . "<br/>");
							$iter++;
							continue;
						}

						$time_depart = $flightdata['time_depart'];
						$time_arrive = $flightdata['time_arrive'];
						$longdate = $flightdata['longdate'];
						$route = $flightdata['route'];
						
						if (!$return_flight) {
							$newFare = $airlines->getFare($value, $dateBook, $origin, $flight_id, $destination, $class_code, $time_depart, $time_arrive, $iter, $adult_num, $child_num, $infant_num, $from_date, $to_date, $route, $longdate);
						} else {
							$newFare = $airlines->getFareRet($value, $dateBook, $origin, $flight_id, $destination, $class_code, $time_depart, $time_arrive, $iter, $adult_num, $child_num, $infant_num, $from_date, $to_date, $route, $longdate);
						}

						if(!array_key_exists('total', $newFare['content']) || !array_key_exists('publish', $newFare['content']) || !array_key_exists('tax', $newFare['content'])){
							$iter++;
							continue;
						}

						$all_result = $newFare['content']['all_result'];		
						$total = $newFare['content']['total'];			
						$publish = $newFare['content']['publish'];
						$tax = $newFare['content']['tax'];	

						$best_key["all_result"] = $all_result;
						$best_key["total"] = $total;
						$best_key["value"] = $value;
						$best_key["publish"] = $publish;
						$best_key["tax"] = $tax;
						$best_key["class_code"] = $class_code;
						$best_key["route"] = $route;
						$best_key["time_depart"] = $time_depart;
						$best_key["time_arrive"] = $time_arrive;


						return $best_key;
					}
					
					$iter++;
				}
			}
		}

		if (!$foundFlight) {
			echo("Price ret not found.");
		}
	}
	
	$test = getBestKey($airlines, $schedules['content']['depart_schedule'], $_POST['flightID'], $dateBook, $origin, $destination, $from_date, $to_date, $price);
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
	
	if (preg_replace('/[^\da-z]/i', '', $_POST['harga']) == preg_replace('/[^\da-z]/i', '', $total) ) {
		$response_harga['status'] = "SUCCESS";
		$response_harga['message'] = "Harga pergi ". $total ." udah ok Pak Eko.";
		$response_harga['total'] = $total;
		echo json_encode($response_harga);	
	} else if (!$foundFlight) {
		//$airlines->logout();
		echo '{"status": "DENIED", "message" : "Gak nemu tuh"}';
	}

	if (!$return_trip)
		exit();
	
	$test2 = getBestKey($airlines, $schedules['content']['return_schedule'], $_POST['flightID_ret'], $dateBook, $destination, $origin, $from_date, $to_date, $price, $return_flight = true);
	// var_dump($test2);
	$all_result_ret = $test2["all_result"] ;
	$total_ret = $test2["total"];
	$value_ret = $test2["value"];
	$publish_ret = $test2["publish"] ;
	$tax_ret = $test2["tax"];
	$class_code_ret = $test2["class_code"];
	$route_ret = $test2["route"];
	$time_depart_ret = $test2["time_depart"];
	$time_arrive_ret = $test2["time_arrive"];

	
	if (preg_replace('/[^\da-z]/i', '', $_POST['harga_ret']) == preg_replace('/[^\da-z]/i', '', $total_ret) ) {
		$response_harga_ret['status'] = "SUCCESS";
		$response_harga_ret['message'] = "Harga pulang ". $total_ret ." udah ok Pak Eko.";
		$response_harga_ret['total'] = $total_ret;
		echo json_encode($response_harga_ret);	
	} else if (!$foundFlight) {
		//$airlines->logout();
		echo '{"status": "DENIED", "message" : "Ret - Gak nemu tuh"}';
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
	$passenger_num = ((int)$_POST['adult_passenger_num'] + (int)$_POST['child_passenger_num'] + (int)$_POST['infant_passenger_num']);
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
