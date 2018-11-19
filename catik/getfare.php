<?php 
require "Airlines.php";

//var_dump($_POST);
?>

<?php 
	$airlines = new Airlines();
	$airlines->setUserNamePassword("darwin", "Versa020874");
	$response = $airlines->login();

	if (isset($response['status']) && $response['status'] != 'success') {
		echo "Gagal login <br/>";
		echo json_encode($response);
		exit();
		}
		
	$dateBook = $_POST['dateFrom'];	
	$date_ret = $_POST['date_ret'];		
	// var_dump($date_ret);
	$schedules = $airlines->getAvailability( $dateBook, $_POST['berangkat'], $_POST['datang'], $_POST['adult_passenger_num'], $_POST['child_passenger_num'], $_POST['infant_passenger_num'], $_POST['date_ret'], $_POST['flightID_ret']);
	$schedule = $schedules['content']['depart_schedule'];
	//$flight = $schedules['content']['depart_schedule']['flight']
	$from_date = $schedules['content']['from_date'];
	$to_date = $schedules['content']['to_date'];

	$foundFlight = false;
	$foundFlight_ret = false;

	if($_POST['flightID_ret'] != "") {
		$schedule_ret = $schedules['content']['return_schedule'];
    	if (is_array($schedule_ret) || is_object($schedule_ret)){  
    		foreach($schedule_ret as $flightdata1) {
    			$flight_id_ret = $flightdata1['flight'];
    			if (preg_replace('/[^\da-z]/i', '', $flight_id_ret) != preg_replace('/[^\da-z]/i', '', $_POST['flightID_ret']))
    				continue;
    			$iter1 = 0;
    			while ($iter1 < 50) {
    				if (array_key_exists((string)$iter1, $flightdata1)) {
    					$value_ret = $flightdata1[(string)$iter1]['value']; 
						$class_code_ret = $flightdata1[(string)$iter1]['class']; 
						
					 	if ($class_code_ret == "BCLP"){
    						$iter1++;
    						continue;
						} 
						
						$time_depart_ret = $flightdata1['time_depart'];
						$time_arrive_ret = $flightdata1['time_arrive'];

						$longdate_ret = $flightdata1['longdate']; 
						$route_ret = $flightdata1['route'];
						$newFare_ret = $airlines->getFareRet($value_ret, $date_ret, $_POST['datang'], $flight_id_ret, $_POST['berangkat'], $class_code_ret, $time_depart_ret, $time_arrive_ret, $iter1, $_POST['adult_passenger_num'], $_POST['child_passenger_num'], $_POST['infant_passenger_num'], $from_date, $to_date, $route_ret,$longdate_ret);
    					$publish_ret = $newFare_ret['content']['publish'];
    					$tax_ret = $newFare_ret['content']['tax'];
						$total_ret = $newFare_ret['content']['total'];
						//$all_result_ret = $newFare_ret['content']['all_result'];

						

						if (preg_replace('/[^\da-z]/i', '', $total_ret) == preg_replace('/[^\da-z]/i', '', $_POST['harga_ret'])){
							$response_harga_ret['status'] = "RET SUCCESS";
							$response_harga_ret['message'] = "Harga pulang ". $total_ret ." udah ok Pak Eko.";
							$response_harga_ret['total'] = $total_ret;
							echo json_encode($response_harga_ret);
							$foundFlight_ret = true;
							break;
						} else if (  abs ( (int)preg_replace('/[^\da-z]/i', '', $total_ret) - (int)preg_replace('/[^\da-z]/i', '', $_POST['harga_ret']) ) <= 50000  ){
						//} else { 
							$response_harga_ret['status'] = "CONFIRM RET";
							$response_harga_ret['message'] = "Ditemukan pulang harga lain sebesar ". $total_ret .". ";
							$response_harga_ret['total'] = $total_ret;
							echo json_encode($response_harga_ret);
							$foundFlight_ret = true;
							break;
						} else { /*echo("Price ret not found.");*/} 
    				}
    				$iter1++;
    			}
    			if ($foundFlight_ret) break;
					else {
					//$airlines->logout();
					echo '{"status": "DENIED", "message" : "Gak nemu tuh"}';}
   			}
   		}
	}
		    
   	if (is_array($schedule) || is_object($schedule)) {  
			foreach($schedule as $flightdata) {
			$flight_id = $flightdata['flight'];
							
			if (preg_replace('/[^\da-z]/i', '', $flight_id) != preg_replace('/[^\da-z]/i', '', $_POST['flightID']))
				continue;
			$iter = 0;
			while ($iter < 50) {
				if (array_key_exists((string)$iter, $flightdata)) {
					$value = $flightdata[(string)$iter]['value']; 
					$class_code = $flightdata[(string)$iter]['class'];
				
					if ($class_code == "BCLP"){
						$iter++;
						continue;
					}
					$time_depart = $flightdata['time_depart'];
					$time_arrive = $flightdata['time_arrive'];
					$longdate = $flightdata['longdate'];
					$route = $flightdata['route'];
					$newFare = $airlines->getFare($value, $dateBook, $_POST['berangkat'], $flight_id, $_POST['datang'], $class_code, $time_depart, $time_arrive, $iter, $_POST['adult_passenger_num'], $_POST['child_passenger_num'], $_POST['infant_passenger_num'], $from_date, $to_date, $route, $longdate);
					$total = $newFare['content']['total'];			
					$publish = $newFare['content']['publish'];
					$tax = $newFare['content']['tax'];
					//$all_result = $newFare['content']['all_result'];					

					if (preg_replace('/[^\da-z]/i', '', $total) == preg_replace('/[^\da-z]/i', '', $_POST['harga'])){
						$response_harga['status'] = "SUCCESS";
						$response_harga['message'] = "Harga ". $total ." udah ok Pak Eko.";
						$response_harga['total'] = $total;
						echo json_encode($response_harga);
						$foundFlight = true;
						break;
					} else if (  abs ((int) preg_replace('/[^\da-z]/i', '', $total) - (int)preg_replace('/[^\da-z]/i', '', $_POST['harga']) ) <= 50000  ){
					//} else {
						$response_harga['status'] = "CONFIRM";
						$response_harga['message'] = "Ditemukan harga lain sebesar ". $total .". ";
						$response_harga['total'] = $total;
						echo json_encode($response_harga);
						$foundFlight = true;
						break;
					} else { /*echo("Price ret not found.");*/}			    

				}
				
				$iter++;
			}

			if ($foundFlight) break;
			else {
				$airlines->logout();
				echo '{"status": "DENIED", "message" : "Gak nemu tuh"}';
			}
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
			<div><input type="hidden" name="total" readonly value="<?=$total?>" ></div>
			<div><input type="hidden" name="publish" readonly value="<?=$publish?>" ></div>
			<div><input type="hidden" name="tax" readonly value="<?=$tax?>" ></div>
			<div><input type="hidden" name="value" readonly value="<?=$value?>" ></div>
			<div><input type="hidden" name="class_code" readonly value="<?=$class_code?>"></div>
			<div><input type="hidden" name="route" readonly value="<?=$route?>" ></div>
			<div><input type="hidden" name="time_depart" readonly value="<?=$time_depart?>" ></div>
    		<div><input type="hidden" name="time_arrive" readonly value="<?=$time_arrive?>" ></div>
			<div><input type="hidden" name="session_id" readonly value="<?=$session_id?>" ></div>
			<div><input type='hidden' name='time_depart_ret' readonly value='<?=$time_depart_ret?>' ></div>
			<div><input type='hidden' name='time_arrive_ret' readonly value='<?=$time_arrive_ret?>'></div>
			<div><input type='hidden' name='value_ret' readonly value='<?=$value_ret?>' ></div>
			<div><input type='hidden' name='route_ret' readonly value='<?=$route_ret?>' ></div> 
			<div><input type='hidden' name='publish_ret' readonly value='<?=$publish_ret?>' ></div>
			<div><input type='hidden' name='tax_ret' readonly value='<?=$tax_ret?>' ></div>
			<div><input type='hidden' name='total_ret' readonly value='<?=$total_ret?>' ></div>	
			<div><input type='hidden' name='class_code_ret' readonly value='<?=$class_code_ret?>' ></div>;

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
