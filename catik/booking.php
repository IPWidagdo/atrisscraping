<!DOCTYPE html>
<html>
<head>
	<title></title>
</head>
<body>
<?php
	require "Airlines.php";
	//var_dump($_POST);

	if (!array_key_exists('session_id', $_POST))
	{
		echo '{"status" : "DENIED", "message" : "Parameternya session_id bos"}';
		return;
	}

	$airlines = new Airlines($session_id = $_POST['session_id']);
	$airlines->setUserNamePassword("darwin", "Versa020874");

	if (!array_key_exists("adult_passenger_num", $_POST) || !array_key_exists("child_passenger_num", $_POST) && !array_key_exists("infant_passenger_num", $_POST)) {
		echo '{"status" : "DENIED", "message" : "Parameternya kurang bos"}';
		return;
	}

	$passenger_num = ((int)$_POST['adult_passenger_num'] + (int)$_POST['child_passenger_num'] + (int)$_POST['infant_passenger_num']);

	/*$data_penumpang = array();
	foreach ($_POST as $key => $value) {
	    echo "Field ".htmlspecialchars($key)." is ".htmlspecialchars($value)."<br>";
	}
	*/
	for ($i=0; $i<$passenger_num; $i++ ) {
		if ( !array_key_exists('title'.$i, $_POST) || !array_key_exists('fname'.$i, $_POST) || !array_key_exists('lname'.$i, $_POST)) {
			echo '{"status" : "DENIED", "message" : "Parameter penumpangnya kurang bos"}';
			return;
		}
		
		$data_penumpang[$i]["title"] = $_POST['title'.$i];
		$data_penumpang[$i]["fname"] = $_POST['fname'.$i];
		$data_penumpang[$i]["lname"] = $_POST['lname'.$i];
		$data_penumpang[$i]["date_of_birth"] = $_POST['date_of_birth'.$i];
		
	} 
		
	$newBooking = $airlines->getBooking($data_penumpang, $_POST['email'], $_POST['phone_number0'], $_POST['value'], $_POST['date_from'], $_POST['berangkat'], $_POST['flight_id'], $_POST['datang'], $_POST['class_code'], $_POST['publish'], $_POST['tax'], $_POST['total'], $_POST['time_depart'], $_POST['time_arrive'], $passenger_num);
	echo json_encode($newBooking);
	$airlines->logout();
	
?>
</body>
</html>