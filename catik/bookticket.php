<?php 
require "Airlines.php";
var_dump($_POST);
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
	$schedules = $airlines->getAvailability( $dateBook, $_POST['berangkat'], $_POST['datang'], $_POST['adult_passenger_num'], $_POST['child_passenger_num'], $_POST['infant_passenger_num']);
	$schedule = $schedules['content']['depart_schedule'];
	//$flight = $schedules['content']['depart_schedule']['flight']
	$from_date = $schedules['content']['from_date'];
	$to_date = $schedules['content']['to_date'];
	$sellkey = $schedule[0]['journey_sell_key'];
	$foundFlight = false;
	

		if (is_array($schedule) || is_object($schedule)){  
			foreach($schedule as $flightdata){
				$flight_id = $flightdata['flight'];
								
				if (preg_replace('/[^\da-z]/i', '', $flight_id) != preg_replace('/[^\da-z]/i', '', $_POST['flightID'])) {
					continue;
				} else $foundFlight = true;
								
				$i = 0;

				while ($i < 100) {
					if (array_key_exists((string)$i, $flightdata)) {
						$value = $flightdata[(string)$i]['value'];
						$class_code = $flightdata[(string)$i]['class'];
						$time_depart = $flightdata['time_depart'];
						$time_arrive = $flightdata['time_arrive'];
						$newFare = $airlines->getFare($value, $dateBook, $_POST['berangkat'], $flight_id, $_POST['datang'], $class_code, $time_depart, $time_arrive, $i, $_POST['adult_passenger_num'], $_POST['child_passenger_num'], $_POST['infant_passenger_num']);

						$data_penumpang =[''] ;
						$passenger_num = ((int)$_POST['adult_passenger_num'] + (int)$_POST['child_passenger_num'] + (int)$_POST['infant_passenger_num']);
						
						for ($i=0; $i<=$passenger_num; $i++ ){
								$data_penumpang[$i]['title'] = $_POST['title'.$i];
								$data_penumpang[$i]['fname'] = $_POST['fname'.$i];
								$data_penumpang[$i]['lname'] = $_POST['lname'.$i];
						}

						$total = $newFare['content']['total'];			
						$publish = $newFare['content']['publish'];
						$tax = $newFare['content']['tax'];
						//$all_result = $newFare['content']['all_result'];
						//var_dump($newFare);					
							if (preg_replace('/[^\da-z]/i', '', $total) == preg_replace('/[^\da-z]/i', '', $_POST['harga'])){
								if(isset($_POST['book']) && isset($_POST['title']) && $fname && $lname && $email && isset($_POST['phone_number0'])){
									for ($i=0; $i<=$passenger_num; $i++ ){
										$title = $_POST['title'.$i];
										$fname = $_POST['fname'.$i];
										$lname = $_POST['lname'.$i];
									} 

								}
							$newBooking = $airlines->getBooking($data_penumpang, $_POST['email'], $_POST['phone_number0'], $value, $dateBook, $_POST['berangkat'], $flight_id, $_POST['datang'], $class_code, $publish, $tax, $total, $time_depart, $time_arrive, $passenger_num);
								break;
							} else if (  abs ( preg_replace('/[^\da-z]/i', '', $total) - (int)preg_replace('/[^\da-z]/i', '', $_POST['harga']) ) <= 50000  ){
								$response_harga['status'] = "CONFIRM";
								$response_harga['message'] = "Ditemukan harga lain sebesar ". $total .". ";
								$response_harga['total'] = $total;
								//$airlines->logout();
								echo json_encode($response_harga);
									break;
								} 		
					} else { echo("Price not found."); break; }
					
					$i++;
				}
		$airlines->logout();
			
	}
?>


<!DOCTYPE html>
<html>
<head>
	<title>Booking Ticket</title>
</head>
<body>
	<form action="" method="post" margin=20%>
		<div>
			<div><input type="hidden" name="adult_passenger_num" readonly value="<?=$_POST['adult_passenger_num']?>" ></div>
			<div><input type="hidden" name="child_passenger_num" readonly value="<?=$_POST['child_passenger_num']?>" ></div>
			<div><input type="hidden" name="infant_passenger_num" readonly value="<?=$_POST['infant_passenger_num']?>" ></div>
			<div><input type="hidden" name="dateFrom" readonly value="<?=$_POST['dateFrom']?>" ></div>
			<div><input type="hidden" name="datang" readonly value="<?=$_POST['datang']?>" ></div>
			<div><input type="hidden" name="berangkat" readonly value="<?=$_POST['berangkat']?>" ></div>
			<div><input type="hidden" name="flightID" readonly value="<?=$_POST['flightID']?>" ></div>
			<div><input type="hidden" name="harga" readonly value="<?=$_POST['harga']?>" ></div>
<?php
		$passenger_num = ((int)$_POST['adult_passenger_num'] + (int)$_POST['child_passenger_num'] + (int)$_POST['infant_passenger_num']);
		for ($i=0; $i<$passenger_num; $i++ ){
			var_dump($passenger_num, $i);
			echo "<div><br>Title : <input type='text' name='title".$i."'></div>
			<div><br>Nama Depan : <input type='text' name='fname".$i."'></div>
			<div><br>Nama Belakang : <input type='text' name='lname".$i."'></div>";
			}
?>
			<div><br>Email: <input type='text' name='email'></div>
			<div><br>Telefon : <input type="text" name="phone_number0"></div>
			<div><br><input type="submit" name="book" value="Book"></div>

		</div>
	</form>

</body>
</html>