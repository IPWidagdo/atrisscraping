<?php
	require "Airlines.php";?>
<?php	
	if(isset($_POST['cari'])){
		if(isset ($_POST['berangkat'])){
			if(isset ($_POST['datang'])){
				if(isset ($_POST['harga'])){
					if(isset($_POST['dateFrom'])){
						if(isset($_POST['flightID'])){
							if(isset($_POST['fname'])){
								if(isset($_POST['lname'])){
									if(isset($_POST['email'])){
										if(isset($_POST['phone_number0'])){

								$airlines= new Airlines();
								$airlines->setUserNamePassword("darwin", "Versa020874");
								$response = $airlines->login();

								if (isset($response['status']) && $response['status'] != 'success') {
									echo "Gagal login <br/>";
									echo json_encode($response);
									exit();
								}
								$dateBook = $_POST['dateFrom'];
								$schedules = $airlines->getAvailability( $dateBook, $_POST['berangkat'], $_POST['datang']);
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
													$newFare = $airlines->getFare($value, $dateBook, $_POST['berangkat'], $flight_id, $_POST['datang'], $class_code, $time_depart, $time_arrive, $i);
													$total = $newFare['content']['total'];			
													$publish = $newFare['content']['publish'];
													$tax = $newFare['content']['tax'];
													//$all_result = $newFare['content']['all_result'];
													//var_dump($newFare);
													if(preg_replace('/[^\da-z]/i', '', $total) == preg_replace('/[^\da-z]/i', '', $_POST['harga'])){
														$newBooking = $airlines->getBooking($_POST['fname'], $_POST['lname'], $_POST['email'], $_POST['phone_number0'], 
															$value, $dateBook, $_POST['berangkat'], $flight_id, $_POST['datang'], $class_code, $publish, 
															$tax, $total, $time_depart, $time_arrive);
														}else echo("Price not found."); 			

													break;
											} else break;
											$i++;
										}
									}
								}
								$airlines->logout();

								/*var_dump($foundFlight);
								echo "foundFlight" . $foundFlight . "<br/>";

								if (!$foundFlight)
									echo '{message: Tidak ditemukan penerbangan, status: not found}';
								else echo '{message: nemu flight nya, status: ok}';
								$airlines->logout();
								
								

								if($getTotal == $_POST['harga']){
									echo "nemu mas";
								} else{
									echo "ga ada. :((";
								}*/
											// di sini ambil sell key dan value
											// request fare untuk kelas ini
											// bandingkan harga yg didapat dari request fare dengan harga dari form input
										
//&& if(isset($_POST['fname']) && if(isset($_POST['lname']) && if(isset($_POST['email']) && if(isset($_POST['phone_number0'])
									
								//	jika flight_id tidak sama dengan flight id dari inputan form:
								//		lanjutkan

									// di sini flight numbernya udah ditemukan
									// tinggal nyari harga
									// sekarang looping ke semua kelas yang tersedia
									}
								}
								//$airlines->getFare();
								
}}}}}}}}
?>

<!DOCTYPE html>
<html>
<head>
	<title>A cup, a cup, a cup, a cup, boy~</title>
</head>
<body>
<div>
	<form action="" method="post" margin=20%>
		<div><br>Keberangkatan : <input type="text" name="berangkat"></div>
		<div><br>Kedatangan : <input type="text" name="datang"></div>
		<div><br>Tanggal Keberangkatan : <input type="date" name="dateFrom" value="<?php echo date('d-m-y'); ?>" /></div>
		<div><br>Harga : <input type="text" name="harga"></div>
		<div><br>Flight ID : <input type="text" name="flightID"></div>
		<div><br>Nama Depan : <input type="text" name="fname"></div>
		<div><br>Nama Belakang : <input type="text" name="lname"></div>
		<div><br>Email: <input type="text" name="email"></div>
		<div><br>Telefon : <input type="text" name="phone_number0"></div>
		<div><input type="submit" name="cari" value="Cari"></div>
	</form>
</div>
</body>
</html>