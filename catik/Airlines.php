<?php

class Airlines {
	var $username = "";
	var $password = "";
	var $tmp_void;


	public $session;

	function __construct() {
		set_time_limit(0);
		
		$this->session = curl_init();
		curl_setopt($this->session, CURLOPT_FOLLOWLOCATION, true);
		curl_setopt($this->session, CURLOPT_COOKIEFILE, "C:\xampp\htdocs\ngecek\atriscurl\cookie.txt");
		curl_setopt($this->session, CURLOPT_COOKIEJAR, "C:\xampp\htdocs\ngecek\atriscurl\cookie.txt");
		curl_setopt($this->session, CURLOPT_RETURNTRANSFER, true);
		//ob_start();  
		//$this->tmp_void = fopen('php://output', 'w');

		//curl_setopt($this->session, CURLOPT_VERBOSE, true);  
		//curl_setopt($this->session, CURLOPT_STDERR, $this->tmp_void);

	}

	function __destruct() {
		//fclose($this->tmp_void);  
		//$debug = ob_get_clean();
		//echo $debug;
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

		return $data_json;
	}

	function getAvailability($depart_date, $origin, $destination) {
		// citilink api
		$fplcitiapi = fopen ('citilinkapi.html', 'w+');
		$urlcitiapi = "https://atris.versatiket.co.id/api/bookingairlines/citilinkapi";

		curl_setopt($this->session, CURLOPT_URL, $urlcitiapi);
		//curl_setopt($this->session, CURLOPT_FILE, $fplcitiapi);
		curl_setopt($this->session, CURLOPT_POSTFIELDS,"adult=". 1 . "&child=". 0 . "&infant=". 0 . "&from_code=". $origin ."&to_code=". $destination ."&from_date=". $depart_date ." & to_date=". $depart_date ." & return_code=");

		$datacitiapi = curl_exec($this->session);
		$data_json = json_decode($datacitiapi, true);
		fwrite($fplcitiapi, $datacitiapi);
		//var_dump($depart_date, $datacitiapi, $data_json);
		return $data_json;

	}

/*
	function searchPrice($harga){
		$tickets = $this->getAvailability();
		$ticket = array_values($tickets);
		$origin = $ticket[2]['from_code']; 	
		$class_listE = $ticket[2][['class_list'][['promo']['economy']]];
		$class_listNC = $ticket[2][['class_list'][['promo']['no_class']]];
		$destination = $ticket[2]['to_code'];
		$depart_date = $ticket[2]['from_date'];

		foreach ($class_listE as $key) {
			$key{
				return $key;
			}
				elseif (foreach ($class_lisNC as $key) {
					if($key == harga){
						return $key;
					}
			}

	}
*/	

	function atrisUrlEncode($params) {
		$params = str_replace("%7E", "~", str_replace("%2A", "*", urlencode($params)));
		return $params;
	}

	function getFare($value, $depart_date, $origin, $flightID, $destination, $class_code, $from_date, $to_date, $seq) {	
		//$flight_ID = preg_replace(' ', '  ', $flightID);

		$fplcitilinkapifare = fopen ('citilinkapifare.html', 'w+');
		$urlcitilinkapifare = "https://atris.versatiket.co.id/api/bookingairlines/ajaxcitilinkapifare";

		curl_setopt($this->session, CURLOPT_URL, $urlcitilinkapifare);
		$postFare = "id=3400"."&choice=".$this->atrisUrlEncode($value)."&date=".$to_date."&from_date=".$from_date."&to_date=".$to_date."&from_code=".$origin."&to_code=".$destination."&adult=". 1 ."&child=". 0 ."&infant=". 0 ."&row=3400"."&class_code=".$class_code."&chkbox=3400"."&seq=".$seq."&defcurr=IDR&info=". $this->atrisUrlEncode("1~CitilinkAPI|3400|" . $flightID . "|" . $origin . "-" . $destination . "|1|" . $from_date . "|" . $to_date . "~" . $class_code . "~" . $value . "~0|0|0");
			//curl_setopt($this->session, CURLOPT_FILE, $fplcitilinkapifare);
			curl_setopt($this->session, CURLOPT_POSTFIELDS, $postFare);
		
		//var_dump($postFare);

		$datacitilinkapifare = curl_exec($this->session);
		$data_json = json_decode($datacitilinkapifare, true);
		fwrite($fplcitilinkapifare, $datacitilinkapifare);
		//var_dump($value, $depart_date, $origin, $flightID, $destination, $class_code, $from_date, $to_date);
		return $data_json;


		
	}

	function getBooking($fname, $lname, $email, $phone_number0, $value, $depart_date, $origin, $flightID, $destination, $class_code, $publish, $tax, $total, $from_date, $to_date){
		//var_dump($flightID);
		$depart_date = new DateTime($depart_date);
		$monthFirst = $depart_date->format('m-d-Y');
		$dayFirst = $depart_date->format('d-m-Y');
		$yearFirst = $depart_date->format('Y-m-d');
		$epochdate = $depart_date->format('U');
		$flight_ID = str_replace('/\s+/', '  ', $flightID);
		//var_dump($flightID, $flight_ID);
		//$tax = number_format ((float) $tax);
		//$publish = number_format ((float) $publish);
		//$total = number_format ((float) $total);

		$dob_default = "29-10-2000";

		$fplcitilinkbooking = fopen ('citilinkbooking.html', 'w+');
		$urlcitilinkbooking = "https://atris.versatiket.co.id/api/bookingairlines/booking";
		$postBook = "route=3400"."&from_code=".$origin."&to_code=". $destination ."&return_code="."&from_date=".$dayFirst."&to_date=". $dayFirst."&count_passenger=". 1 ."&adult=". 1 ."&child=". 0 ."&infant=". 0 ."&passenger_title0=Mr"."&name0=".$fname."&surname0=".$lname."&identify0=-1"."&date0=".$dob_default."&infant_parent0="."&passport0="."&expired0="."&passport_issuing0=ID"."&country0=ID"."&baggage0="."&baggage_jetstar0="."&baggage_jetstarapi0="."&baggage_airasia_dep0="."&baggage_firefly_dep0="."&baggage_tigerindonesia0="."&baggage_airasia_ret0="."&baggage_jetstarapi_ret0="."&baggage_firefly_ret0="."&passenger_type0=Adult"."&contact_title=Mr"."&contact_name=".$fname."&contact_surname=".$lname."&email=".$this->atrisUrlEncode($email)."&phone_code0=62"."&phone_area0=".substr($phone_number0, 0, 3)."&phone_number0=".$phone_number0."&phone_type0=Mobile"."&phone_code1=62"."&phone_area1="."&phone_number1="."&phone_type1=Home"."&check_box3400=" . $this->atrisUrlEncode("1~CitilinkAPI|3400|". $flight_ID . "|" . $origin . "-" . $destination . "|1|" . $from_date . "|" . $to_date .  "~" . $class_code . "~" . $value. "~" . $publish . "|" . $tax .  "|" . $total . "|IDR|IDR")  ;

		curl_setopt($this->session, CURLOPT_URL, $urlcitilinkbooking);
		//curl_setopt($this->session, CURLOPT_FILE, $fplcitilinkbooking);

		curl_setopt($this->session, CURLOPT_POSTFIELDS, $postBook);

		$datalab = curl_exec($this->session);
		fwrite($fplcitilinkbooking, $datalab);

	}
}
?>