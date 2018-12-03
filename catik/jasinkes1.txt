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

	function getAvailability($depart_date, $origin, $destination, $adult, $child, $infant, $date_ret, $flight_id_ret) {
		$fplcitiapi = fopen ('citilinkapi.html', 'w+');
		$urlcitiapi = "https://atris.versatiket.co.id/api/bookingairlines/citilinkapi";

		if($date_ret !="" && $flight_id_ret != ""){
			$return_code = $_POST['berangkat'];
		} else{
			$return_code = "";
		};

		curl_setopt($this->session, CURLOPT_URL, $urlcitiapi);
		$postAvailability = "adult=". $adult . "&child=". $child . "&infant=". $infant . "&from_code=". $origin ."&to_code=". $destination ."&from_date=". $depart_date ."&to_date=". $date_ret ."&return_code=" .$return_code;
		curl_setopt($this->session, CURLOPT_POSTFIELDS, $postAvailability);
		var_dump($postAvailability);

		$datacitiapi = curl_exec($this->session);
		$data_json = json_decode($datacitiapi, true);
		fwrite($fplcitiapi, $datacitiapi);

		return $data_json;

	}

	function atrisUrlEncode($params) {
		$params = str_replace("%7E", "~", str_replace("%2A", "*", urlencode($params)));
		return $params;
	}

	function getFare($value, $depart_date, $origin, $flightID, $destination, $class_code, $time_depart, $time_arrive, $seq, $adult_passenger_num, $child_passenger_num, $infant_passenger_num, $from_date, $to_date, $route, $longdate){	
		//$flight_ID = preg_replace(' ', '  ', $flightID);

		$fplcitilinkapifare = fopen ('citilinkapifare.html', 'w+');
		$urlcitilinkapifare = "https://atris.versatiket.co.id/api/bookingairlines/ajaxcitilinkapifare";

		curl_setopt($this->session, CURLOPT_URL, $urlcitilinkapifare);
		$postFare = "id=3400"."&choice=".$this->atrisUrlEncode($value)."&date=".$longdate . "&from_date=". $this->atrisUrlEncode($time_depart) ."&to_date=". $this->atrisUrlEncode($time_arrive) ."&from_code=".$origin."&to_code=".$destination."&adult=". $adult_passenger_num ."&child=". $child_passenger_num ."&infant=". $infant_passenger_num ."&row=3400"."&class_code=". $this->atrisUrlEncode($class_code)."&chkbox=3400"."&seq=".$seq."&defcurr=IDR&info=". $this->atrisUrlEncode("1~CitilinkAPI|3400|" . $flightID . "|" . $route. "|1|" . $time_depart . "|" . $time_arrive . "~" . $class_code . "~" . $value . "~0|0|0");
		curl_setopt($this->session, CURLOPT_POSTFIELDS, $postFare);
		var_dump($postFare);
		
		$datacitilinkapifare = curl_exec($this->session);
		$data_json = json_decode($datacitilinkapifare, true);
		fwrite($fplcitilinkapifare, $datacitilinkapifare);
		//var_dump($value, $depart_date, $origin, $flightID, $destination, $class_code, $from_date, $to_date);
		return $data_json;

	}

	function getFareRet($value_ret, $depart_date_ret, $origin_ret, $flightID_ret, $destination_ret, $class_code_ret, $time_depart_ret, $time_arrive_ret, $seq_ret, $adult_passenger_num_ret, $child_passenger_num_ret, $infant_passenger_num_ret, $from_date, $to_date, $route_ret, $longdate_ret){	
		//$flight_ID = preg_replace(' ', '  ', $flightID);

		$fplcitilinkapifareret = fopen ('citilinkapifareret.html', 'w+');
		$urlcitilinkapifareret = "https://atris.versatiket.co.id/api/bookingairlines/ajaxcitilinkapifare";

		curl_setopt($this->session, CURLOPT_URL, $urlcitilinkapifareret);
		$postFare_ret = "id=3450"."&choice=".$this->atrisUrlEncode($value_ret)."&date=".$longdate_ret."&from_date=".$this->atrisUrlEncode($time_depart_ret)."&to_date=".$this->atrisUrlEncode($time_arrive_ret)."&from_code=".$origin_ret."&to_code=".$destination_ret."&adult=". $adult_passenger_num_ret ."&child=". $child_passenger_num_ret ."&infant=". $infant_passenger_num_ret ."&row=3450"."&class_code=".$this->atrisUrlEncode($class_code_ret)."&chkbox=3450"."&seq=".$seq_ret."&defcurr=IDR&info=". $this->atrisUrlEncode("1~CitilinkAPI|3450|" . $flightID_ret . "|" . $route_ret . "|1|" . $time_depart_ret . "|" . $time_arrive_ret . "~" . $class_code_ret . "~" . $value_ret . "~0|0|0");
		curl_setopt($this->session, CURLOPT_POSTFIELDS, $postFare_ret);
		var_dump($postFare_ret);
			
		$datacitilinkapifareret = curl_exec($this->session);
		$data_json = json_decode($datacitilinkapifareret, true);
		fwrite($fplcitilinkapifareret, $datacitilinkapifareret);
		//var_dump($value, $depart_date, $origin, $flightID, $destination, $class_code, $from_date, $to_date);
		return $data_json;

	}

	function getBooking($data_penumpang, $email, $phone_number0, $value, $depart_date, $origin, $flight_id, $destination, $class_code, $publish, $tax, $total, $from_date, $to_date, $passenger_num, $route, $flight_id_ret, $route_ret, $date_ret, $class_code_ret, $value_ret, $publish_ret, $tax_ret, $total_ret, $time_depart_ret, $time_arrive_ret, $time_depart, $time_arrive){
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

		if($date_ret !="" && $flight_id_ret != ""){
			$route_rt = "%2C3450";
			$return_code = $_POST['berangkat'];
			$date_ret  = new DateTime($date_ret);
			$to_date = $date_ret->format('d-m-Y');
			$to_date_u_ret = $date_ret->format('U');


			echo "time depart ret: "; var_dump($time_depart_ret);
			echo "time arrive ret: "; var_dump($time_arrive_ret);
			echo "split ret";
			if(strlen($time_depart_ret) > 12 && strlen($time_arrive_ret)>12){
				$time_depart_ret_exp = explode("##", $time_depart_ret);
				$time_depart_ret0 = $time_depart_ret_exp[0];
				$time_depart_ret1 = $time_depart_ret_exp[1];
				$time_arrive_ret_exp = explode("##", $time_arrive_ret);
				$time_arrive_ret0 = $time_arrive_ret_exp[0];
				$time_arrive_ret1 = $time_arrive_ret_exp[1];
		
				$date_arranged_ret =  $time_depart_ret0 ."##". $time_arrive_ret0 . "|" . $time_depart_ret1 . "##" . $time_arrive_ret1; 
			} else{
				$date_arranged_ret = $time_depart_ret . "|" . $time_arrive_ret;
			}

			//var_dump($time_depart_ret0, $time_depart_ret1, $time_arrive_ret0, $time_arrive_ret1); 
			$chkbox3450 = "&check_box3450=" . $this->atrisUrlEncode("1~CitilinkAPI|3450|". $flight_id_ret . "|" . $route_ret ."|1|" . $date_arranged_ret .  "~" . $class_code_ret . "~" . $value_ret. "~" . $publish_ret . "|" . $tax_ret .  "|" . $total_ret . "|IDR|IDR");

		} else {
			$route_rt = "";
			$return_code = "";
			$to_date = $epochdate;
			$chkbox3450 = "";
		};

		$postBook = "route=3400" .$route_rt."&from_code=".$origin."&to_code=". $destination ."&return_code=". $return_code ."&from_date=".$dayFirst."&to_date=". $to_date."&count_passenger=". $passenger_num ."&adult=".(int)$_POST['adult_passenger_num']."&child=". (int)$_POST['child_passenger_num'] ."&infant=". (int)$_POST['infant_passenger_num'];
		
		$parent_quota = (int)$_POST['adult_passenger_num'];
		$parent_iter = 1;

		for ($i=0; $i<$passenger_num; $i++ ) {

			$dob = new DateTime($data_penumpang[$i]['date_of_birth']);
			$now = new DateTime('today');
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

			
			if ($passenger_age < 2 ) {
				$type = "Infant";
			} elseif ($passenger_age <= 12 && $passenger_age >= 2 ) {
				$type = "Child";
			} elseif ($passenger_age > 12) {
				$type = "Adult";	
			} 

			$postBook = $postBook. "&passenger_title".$i. "=" .$data_penumpang[$i]['title']; 
			$postBook = $postBook. "&name".$i. "=" .$data_penumpang[$i]['fname']; 
			$postBook = $postBook. "&surname".$i. "=" .$data_penumpang[$i]['lname'];
			$postBook = $postBook. "&identify".$i. "=-1";
			$postBook = $postBook. "&date".$i. "=".$date_of_birth;
			
			if ($type == "Infant") {	

				if ($parent_quota < 1) 
					return json_encode('"code:":"DENIED", "message":"Jumlah balita lebih banyak daripada dewasa"');
					
				$postBook = $postBook. "&infant_parent". $i . "=". $parent_iter;
				$parent_iter++;
				$parent_quota--;
			} else {
				$postBook = $postBook. "&infant_parent". $i ."=";
			}
			
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
		$postBook = $postBook. "&phone_code0=62";
		$postBook = $postBook. "&phone_area0=".substr($phone_number0, 0, 3);
		$postBook = $postBook. "&phone_number0=".substr($phone_number0, 3, strlen($phone_number0));
		$postBook = $postBook. "&phone_type0=Mobile&phone_code1=62&phone_area1=&phone_number1=&phone_type1=Home";
		echo "time depart : "; var_dump($time_depart);
		echo "time arrive : "; var_dump($time_arrive);
		echo "split";
		if(strlen($time_depart) > 12 && strlen($time_arrive) > 12) {
			$time_depart_exp = explode("##", $time_depart);
			$time_depart0 = $time_depart_exp[0];
			$time_depart1 = $time_depart_exp[1];
			$time_arrive_exp = explode("##", $time_arrive);
			$time_arrive0 = $time_arrive_exp[0];
			$time_arrive1 = $time_arrive_exp[1];

			$date_arranged =  $time_depart0 ."##" . $time_arrive0 . "|" . $time_depart1 . "##" . $time_arrive1; 
		} else {
			$date_arranged = $time_depart . "|" . $time_arrive;
		}
	
		//var_dump($time_depart0, $time_depart1, $time_arrive0, $time_arrive1); 
		
		//."&passenger_title0=Mr"."&name0=".$fname."&surname0=".$lname."&identify0=-1"."&date0=".$dob_default."&infant_parent0="."&passport0="."&expired0="."&passport_issuing0=ID"."&country0=ID"."&baggage0="."&baggage_jetstar0="."&baggage_jetstarapi0="."&baggage_airasia_dep0="."&baggage_firefly_dep0="."&baggage_tigerindonesia0="."&baggage_airasia_ret0="."&baggage_jetstarapi_ret0="."&baggage_firefly_ret0="."&passenger_type0=Adult"."&contact_title=Mr"."&contact_name=".$fname."&contact_surname=".$lname."&email=".$this->atrisUrlEncode($email)."&phone_code0=62"."&phone_area0=".substr($phone_number0, 0, 3)."&phone_number0=".$phone_number0."&phone_type0=Mobile"."&phone_code1=62"."&phone_area1="."&phone_number1="."&phone_type1=Home";

		$postBook = $postBook. "&check_box3400=" . $this->atrisUrlEncode("1~CitilinkAPI|3400|". $flight_id . "|" . $route ."|1|" . $date_arranged .  "~" . $class_code . "~" . $value. "~" . $publish . "|" . $tax .  "|" . $total . "|IDR|IDR") . $chkbox3450;
	
		var_dump( $postBook);

		curl_setopt($this->session, CURLOPT_URL, $urlcitilinkbooking);
		//curl_setopt($this->session, CURLOPT_FILE, $fplcitilinkbooking);

		curl_setopt($this->session, CURLOPT_POSTFIELDS, $postBook);

		$datalab = curl_exec($this->session);
		fwrite($fplcitilinkbooking, $datalab);
		return json_decode($datalab);
	}
}
?>