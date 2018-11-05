<?php

class Airlines {
	var $username = "";
	var $password = "";
	var $tmp_void;


	public $session;

	function __construct($session_id = null) {
		set_time_limit(0);

		if (is_null($session_id)) 
			$this->session_id = date("Y-m-d-H-i-s") . uniqid();		
		else
			$this->session_id = $session_id;

		$this->session_file = dirname(__FILE__) ."/" . $this->session_id ."cookies.txt";
		//$this->session_file = dirname(__FILE__) ."/cookies.txt";

		$this->session = curl_init();
		curl_setopt($this->session, CURLOPT_FOLLOWLOCATION, true);
		curl_setopt($this->session, CURLOPT_COOKIEFILE, $this->session_file);
		curl_setopt($this->session, CURLOPT_COOKIEJAR, $this->session_file);
		curl_setopt($this->session, CURLOPT_RETURNTRANSFER, true);
		echo "session file " . $this->session_file ."<br/>";
		//ob_start();  
		//$this->tmp_void = fopen('php://output', 'w');

		//curl_setopt($this->session, CURLOPT_VERBOSE, true);  
		//curl_setopt($this->session, CURLOPT_STDERR, $this->tmp_void);

	}

	function saveSession() {
		if (gettype($this->session) == 'resource') curl_close($this->session);
		else var_dump($this->session);

		return $this->session_id;
	}

	function setUserNamePassword($username, $password) {
		$this->username = $username;
		$this->password = $password;
	}

	function login() {
		//admin login
		$fp3 = fopen ('admin1.html', 'w+');
		$url3 = "https://atris.versatiket.co.id/api/admin";
		curl_setopt($this->session, CURLOPT_URL, $url3);
		//curl_setopt($this->session, CURLOPT_FILE, $fp3);
		curl_setopt($this->session, CURLOPT_POSTFIELDS,"user=".$this->username."&password=".$this->password);
		
		//echo $url3 . "\n";
		//echo "user=".$this->username."&password=".$this->password. "\n";
		$data3 = curl_exec($this->session);
		$data_json = json_decode($data3, true);
		//var_dump($data3, $data_json);
		fwrite($fp3, $data3);

		return $data_json;
		
	}

	function logout() {
		//logout
		$fplo = fopen ('logout1.html', 'w+');
		$urllo = "https://atris.versatiket.co.id/api/admin/logout";

		curl_setopt($this->session, CURLOPT_URL, $urllo);
		//curl_setopt($this->session, CURLOPT_FILE, $fplo);

		$datalo = curl_exec($this->session);
		$data_json = json_decode($datalo, true);
		curl_close($this->session);
		fwrite($fplo, $datalo);

		unlink($this->session_file);
		return $data_json;

	}

	function getAvailability($depart_date, $origin, $destination, $adult, $child, $infant) {
		$fplcitiapi = fopen ('citilinkapi.html', 'w+');
		$urlcitiapi = "https://atris.versatiket.co.id/api/bookingairlines/citilinkapi";

		curl_setopt($this->session, CURLOPT_URL, $urlcitiapi);
		curl_setopt($this->session, CURLOPT_POSTFIELDS,"adult=". $adult . "&child=". $child . "&infant=". $infant . "&from_code=". $origin ."&to_code=". $destination ."&from_date=". $depart_date ." & to_date=". $depart_date ." & return_code=");

		$datacitiapi = curl_exec($this->session);
		$data_json = json_decode($datacitiapi, true);
		fwrite($fplcitiapi, $datacitiapi);

		return $data_json;

	}

	function atrisUrlEncode($params) {
		$params = str_replace("%7E", "~", str_replace("%2A", "*", urlencode($params)));
		return $params;
	}

	function getFare($value, $depart_date, $origin, $flightID, $destination, $class_code, $from_date, $to_date, $seq, $adult_passenger_num, $child_passenger_num, $infant_passenger_num){	
		//$flight_ID = preg_replace(' ', '  ', $flightID);

		$fplcitilinkapifare = fopen ('citilinkapifare.html', 'w+');
		$urlcitilinkapifare = "https://atris.versatiket.co.id/api/bookingairlines/ajaxcitilinkapifare";

		curl_setopt($this->session, CURLOPT_URL, $urlcitilinkapifare);
		$postFare = "id=3400"."&choice=".$this->atrisUrlEncode($value)."&date=".$to_date."&from_date=".$from_date."&to_date=".$to_date."&from_code=".$origin."&to_code=".$destination."&adult=". $adult_passenger_num ."&child=". $child_passenger_num ."&infant=". $infant_passenger_num ."&row=3400"."&class_code=".$class_code."&chkbox=3400"."&seq=".$seq."&defcurr=IDR&info=". $this->atrisUrlEncode("1~CitilinkAPI|3400|" . $flightID . "|" . $origin . "-" . $destination . "|1|" . $from_date . "|" . $to_date . "~" . $class_code . "~" . $value . "~0|0|0");
			//curl_setopt($this->session, CURLOPT_FILE, $fplcitilinkapifare);
			curl_setopt($this->session, CURLOPT_POSTFIELDS, $postFare);
		
		var_dump($postFare);

		$datacitilinkapifare = curl_exec($this->session);
		$data_json = json_decode($datacitilinkapifare, true);
		fwrite($fplcitilinkapifare, $datacitilinkapifare);
		//var_dump($value, $depart_date, $origin, $flightID, $destination, $class_code, $from_date, $to_date);
		return $data_json;

	}

	function getBooking($data_penumpang, $email, $phone_number0, $value, $depart_date, $origin, $flight_id, $destination, $class_code, $publish, $tax, $total, $from_date, $to_date, $passenger_num){
		//var_dump($flightID);
		$depart_date = new DateTime($depart_date);
		$monthFirst = $depart_date->format('m-d-Y');
		$dayFirst = $depart_date->format('d-m-Y');
		$yearFirst = $depart_date->format('Y-m-d');
		$epochdate = $depart_date->format('U');
		$flight_id = str_replace('/\s+/', '  ', $flight_id);
		$passenger_num = ((int)$_POST['adult_passenger_num'] + (int)$_POST['child_passenger_num'] + (int)$_POST['infant_passenger_num']);

		//var_dump($flightID, $flight_ID);
		//$tax = number_format ((float) $tax);
		//$publish = number_format ((float) $publish);
		//$total = number_format ((float) $total);

		$fplcitilinkbooking = fopen ('citilinkbooking.html', 'w+');
		$urlcitilinkbooking = "https://atris.versatiket.co.id/api/bookingairlines/booking";
		//$postBook = "route=3400"."&from_code=".$origin."&to_code=". $destination ."&return_code="."&from_date=".$dayFirst."&to_date=". $dayFirst."&count_passenger=". $passenger_num ."&adult=". 1 ."&child=". 0 ."&infant=". 0 ."&passenger_title0=Mr"."&name0=".$fname."&surname0=".$lname."&identify0=-1"."&date0=".$dob_default."&infant_parent0="."&passport0="."&expired0="."&passport_issuing0=ID"."&country0=ID"."&baggage0="."&baggage_jetstar0="."&baggage_jetstarapi0="."&baggage_airasia_dep0="."&baggage_firefly_dep0="."&baggage_tigerindonesia0="."&baggage_airasia_ret0="."&baggage_jetstarapi_ret0="."&baggage_firefly_ret0="."&passenger_type0=Adult"."&contact_title=Mr"."&contact_name=".$fname."&contact_surname=".$lname."&email=".$this->atrisUrlEncode($email)."&phone_code0=62"."&phone_area0=".substr($phone_number0, 0, 3)."&phone_number0=".$phone_number0."&phone_type0=Mobile"."&phone_code1=62"."&phone_area1="."&phone_number1="."&phone_type1=Home"."&check_box3400=" . $this->atrisUrlEncode("1~CitilinkAPI|3400|". $flight_ID . "|" . $origin . "-" . $destination . "|1|" . $from_date . "|" . $to_date .  "~" . $class_code . "~" . $value. "~" . $publish . "|" . $tax .  "|" . $total . "|IDR|IDR")  ;

		$postBook = "route=3400"."&from_code=".$origin."&to_code=". $destination ."&return_code="."&from_date=".$dayFirst."&to_date=". $dayFirst."&count_passenger=". $passenger_num ."&adult=".(int)$_POST['adult_passenger_num']."&child=". (int)$_POST['child_passenger_num'] ."&infant=". (int)$_POST['infant_passenger_num'];
		
		for ($i=0; $i<$passenger_num; $i++ ){

			$dob = new DateTime($data_penumpang[$i]['date_of_birth']);
			$now   = new DateTime('today');
			$passenger_age = $dob->diff($now)->y;
			$date_of_birth = $dob->format('d-m-Y');


			/*$dob = new DateTime($data_penumpang[$i]['date_of_birth']);
			$dob = $dob->format('d-m-Y');
			//$dob = strtotime($dob);
			*/


			/*$now = strtotime(date('d-m-Y'));
			$now = new DateTime("@$now");
			$now =  $now->format('m-d-Y');
			*/


			//$passenger_age_days = $now ->diff($dob); 
			//echo $passenger_age_days->format('%R%a days');

			
			if ($passenger_age <= 12 && $passenger_age >= 2 ){
				$type = "Child";
			} elseif ($passenger_age < 2) {
				$type = "Infant";
			} else $type = "Adult";

			$postBook = $postBook. "&passenger_title".$i. "=" .$data_penumpang[$i]['title']; 
			$postBook = $postBook. "&name".$i. "=" .$data_penumpang[$i]['fname']; 
			$postBook = $postBook. "&surname".$i. "=" .$data_penumpang[$i]['lname'];
			$postBook = $postBook. "&identify".$i. "=-1";
			$postBook = $postBook. "&date".$i. "=".$date_of_birth;
			if($type == "Infant"){
				$postBook = $postBook. "&infant_parent". $i . "=". $i;	
			} else {$postBook = $postBook. "&infant_parent". $i ."=";}
			$postBook = $postBook. "&passport". $i. "=";
			$postBook = $postBook. "&expired". $i. "=";
			$postBook = $postBook. "&passport_issuing". $i. "=ID";
			$postBook = $postBook. "&country". $i. "=ID";
			$postBook = $postBook. "&baggage". $i ."=";
			$postBook = $postBook. "&baggage_jetstar". $i ."=";
			$postBook = $postBook. "&baggage_jetstarapi". $i. "=";
			$postBook = $postBook. "&baggage_airasia_dep". $i. "=";
			$postBook = $postBook. "&baggage_firefly_dep". $i ."=";
			$postBook = $postBook. "&baggage_tigerindonesia". $i. "=";
			$postBook = $postBook. "&baggage_airasia_ret". $i. "=";
			$postBook = $postBook. "&baggage_jetstarapi_ret". $i. "=";
			$postBook = $postBook. "&baggage_firefly_ret". $i. "=";			
			$postBook = $postBook. "&passenger_type". $i. "=" .$type;
		}

		$postBook = $postBook. "&contact_title=Mr&contact_name=".$data_penumpang[0]['fname']."&contact_surname=".$data_penumpang[0]['lname'];
		$postBook = $postBook. "&email=".$this->atrisUrlEncode($email);
		$postBook = $postBook. "&phone_code0"."=62";
		$postBook = $postBook. "&phone_area0=".substr($phone_number0, 0, 3);
		$postBook = $postBook. "&phone_number0=".substr($phone_number0, 3, strlen($phone_number0));
		$postBook = $postBook. "&phone_type0=Mobile&phone_code1=62&phone_area1=&phone_number1=&phone_type1=Home";
		
	//."&passenger_title0=Mr"."&name0=".$fname."&surname0=".$lname."&identify0=-1"."&date0=".$dob_default."&infant_parent0="."&passport0="."&expired0="."&passport_issuing0=ID"."&country0=ID"."&baggage0="."&baggage_jetstar0="."&baggage_jetstarapi0="."&baggage_airasia_dep0="."&baggage_firefly_dep0="."&baggage_tigerindonesia0="."&baggage_airasia_ret0="."&baggage_jetstarapi_ret0="."&baggage_firefly_ret0="."&passenger_type0=Adult"."&contact_title=Mr"."&contact_name=".$fname."&contact_surname=".$lname."&email=".$this->atrisUrlEncode($email)."&phone_code0=62"."&phone_area0=".substr($phone_number0, 0, 3)."&phone_number0=".$phone_number0."&phone_type0=Mobile"."&phone_code1=62"."&phone_area1="."&phone_number1="."&phone_type1=Home";

		$postBook = $postBook. "&check_box3400=" . $this->atrisUrlEncode("1~CitilinkAPI|3400|". $flight_id . "|" . $origin . "-" . $destination . "|1|" . $from_date . "|" . $to_date .  "~" . $class_code . "~" . $value. "~" . $publish . "|" . $tax .  "|" . $total . "|IDR|IDR");
		var_dump($postBook);

		curl_setopt($this->session, CURLOPT_URL, $urlcitilinkbooking);
		//curl_setopt($this->session, CURLOPT_FILE, $fplcitilinkbooking);

		curl_setopt($this->session, CURLOPT_POSTFIELDS, $postBook);

		$datalab = curl_exec($this->session);
		fwrite($fplcitilinkbooking, $datalab);
		return json_decode($datalab);
	}
}
?>