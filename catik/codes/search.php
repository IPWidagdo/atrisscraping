<?php 
require "Airlines.php";
?>

<?php	
    if(isset($_POST['cari']) && isset($_POST['lastname']) && isset($_POST['book_code']) && isset($_POST['begin_date']) && isset($_POST['end_date'])){    
		
		//var_dump($_POST['cari'], $_POST['lastname'], $_POST['book_code'], $_POST['begin_date'], $_POST['end_date']);
		
		$airlines = new Airlines();
		$airlines->setUserNamePassword("gabon", "Csatversa123");
		
		// $airlines->setUserNamePassword("darwin", "Versa020874");		
		
		$login = $airlines->login();
		$findbook = $airlines->searchBook($_POST['begin_date'], $_POST['end_date'], $_POST['lastname'], $_POST['book_code']);
		
		$list= $findbook['content']['list'];

		foreach ($list as $list_loop){
			$booking_id = $list_loop['booking_id'];
		}
		// var_dump($booking_id);
		$book_detail = $airlines->infoBook($booking_id); 
		$reservation_detail = $book_detail['content']['reservation_detail'];
		$schedule_detail = $book_detail['content']['schedule_detail'];
		//foreach($reservation_detail as $reservation_detail){
		$booking_status = $reservation_detail['booking_status'];
		// 	var_dump($booking_status);
		// 	exit;
		//}

		foreach($schedule_detail as $schedule_detail){
			$schedule_flight_id = $schedule_detail['schedule_flight_id'];
		}

		if ($booking_status == 'S'){
			$post_var = FALSE;
			$post_issued = $airlines->printIssued($booking_id, $post_var, $schedule_flight_id);
			exit(0);
		} elseif ($booking_status == 'A') {
			$response_issued = $airlines->issuePayment($booking_id);
			$captcha_image = $airlines->isCaptchaResponse($response_issued);

			if ($captcha_image) {
				$session_id = $airlines->saveSession();
				header("Location: " . 'getcaptcha.php?captcha=' . $captcha_image . '&session_id='.$session_id . '&booking_id=' . $booking_id, TRUE, 302);
			}

			$post_info_issue = $this->infoBook($booking_id);
			$airlines->logout();
		}
		
    } elseif (isset($_POST['captcha_code']) && isset($_POST['session_id']) && isset($_POST['booking_id']) ){
		$captcha_code = $_POST['captcha_code'];
		
		$airlines = new Airlines($session_id = $_POST['session_id']);
		$airlines->setUserNamePassword("gabon", "Csatversa123");
		$response_issued = $airlines->issuePayment($_POST['booking_id'], $captcha_code);

		$airlines->isCaptchaResponse($response_issued);
		$post_info_issue = $airlines->infoBook($_POST['booking_id']);
		$airlines->logout();
		
	}
?>

<!DOCTYPE html>
<html>
<head>
	<title>Madosi tiket</title>
</head>
<body>
<div>
	<form  method="post" margin=20%>
		<div>
			<div><br>Nama Belakang : <input type="text" name="lastname"></div>
			<div><br>Kode Booking  : <input type="text" name="book_code"></div>
			<div><br>Tanggal Awal  : <input type="date" name="begin_date" value="<?php echo date('d-m-y'); ?>" /></div>
			<div><br>Tanggal Akhir : <input type="date" name="end_date" value="<?php echo date('d-m-y'); ?>" /></div>
        </div>
        <div><br><input type="submit" name="cari" value="Cari" ></div>
	</form>
</div>
</body>
</html>