<?php
require "scraping.php";

class Airlines {
	var $username = "";
	var $password = "";
	var $tmp_void;

	public $scraping;

	function __construct($session_id = null) {
		if (is_null($session_id)) 
			$this->scraping = new Scraping();		
		else
			$this->scraping = new Scraping($session_id = $session_id);
	}

	function saveSession() {
		return $this->scraping->saveSession();
	}

	function checkLogin() {
		$response = $this->scraping->request('checklogin.html','https://atris.versatiket.co.id/api/admin/getuserlogin', "");

		if (strlen($response) > 0)
			return true;
		return false; 
		
	}

	function setUserNamePassword($username, $password) {
		$this->username = $username;
		$this->password = $password;
	}

	function login() {
		if(!$this->checkLogin()){
			$response = $this->scraping->request('admin1.html', 'https://atris.versatiket.co.id/api/admin', "user=".$this->username."&password=".$this->password);	
		}		
	}

	function logout() {
		//logout
		$response = $this->scraping->request('logout1.html','https://atris.versatiket.co.id/api/admin/logout', "");
		//unlink($this->scraping->session_file);
		return $response;

	}

	function getAvailability($depart_date, $origin, $destination, $adult, $child, $infant, $date_ret, $flight_id_ret) {
		if($date_ret !="" && $flight_id_ret != ""){
			$return_code = $_POST['berangkat'];
		} else{
			$return_code = "";
		};

		$postAvailability = "adult=". $adult . "&child=". $child . "&infant=". $infant . "&from_code=". $origin ."&to_code=". $destination ."&from_date=". $depart_date ."&to_date=". $date_ret ."&return_code=" .$return_code;

		$response = $this->scraping->request('citilinkapi.html', 'https://atris.versatiket.co.id/api/bookingairlines/citilinkapi', $postAvailability);
		return $response;
	}

	function atrisUrlEncode($params) {
		$params = str_replace("%7E", "~", str_replace("%2A", "*", urlencode($params)));
		return $params;
	}

	function getFare($value, $depart_date, $origin, $flightID, $destination, $class_code, $time_depart, $time_arrive, $seq, $adult_passenger_num, $child_passenger_num, $infant_passenger_num, $from_date, $to_date, $route, $longdate){	
		//$flight_ID = preg_replace(' ', '  ', $flightID);

		$postFare = "id=3400"."&choice=".$this->atrisUrlEncode($value)."&date=".$longdate . "&from_date=". $this->atrisUrlEncode($time_depart) ."&to_date=". $this->atrisUrlEncode($time_arrive) ."&from_code=".$origin."&to_code=".$destination."&adult=". $adult_passenger_num ."&child=". $child_passenger_num ."&infant=". $infant_passenger_num ."&row=3400"."&class_code=". $this->atrisUrlEncode($class_code)."&chkbox=3400"."&seq=".$seq."&defcurr=IDR&info=". $this->atrisUrlEncode("1~CitilinkAPI|3400|" . $flightID . "|" . $route. "|1|" . $time_depart . "|" . $time_arrive . "~" . $class_code . "~" . $value . "~0|0|0");
		
		$response = $this->scraping->request('citilinkapifare.html', 'https://atris.versatiket.co.id/api/bookingairlines/ajaxcitilinkapifare', $postFare);
		return $response;	
	}

	function getFareRet($value_ret, $depart_date_ret, $origin_ret, $flightID_ret, $destination_ret, $class_code_ret, $time_depart_ret, $time_arrive_ret, $seq_ret, $adult_passenger_num_ret, $child_passenger_num_ret, $infant_passenger_num_ret, $from_date, $to_date, $route_ret, $longdate_ret){	

		$postFare_ret = "id=3450"."&choice=".$this->atrisUrlEncode($value_ret)."&date=".$longdate_ret."&from_date=".$this->atrisUrlEncode($time_depart_ret)."&to_date=".$this->atrisUrlEncode($time_arrive_ret)."&from_code=".$origin_ret."&to_code=".$destination_ret."&adult=". $adult_passenger_num_ret ."&child=". $child_passenger_num_ret ."&infant=". $infant_passenger_num_ret ."&row=3450"."&class_code=".$this->atrisUrlEncode($class_code_ret)."&chkbox=3450"."&seq=".$seq_ret."&defcurr=IDR&info=". $this->atrisUrlEncode("1~CitilinkAPI|3450|" . $flightID_ret . "|" . $route_ret . "|1|" . $time_depart_ret . "|" . $time_arrive_ret . "~" . $class_code_ret . "~" . $value_ret . "~0|0|0");

		var_dump($postFare_ret);

		$response = $this->scraping->request('citilinkapifareret.html', 'https://atris.versatiket.co.id/api/bookingairlines/ajaxcitilinkapifare', $postFare_ret);
		return $response;
	}

	function getBooking($data_penumpang, $email, $phone_number0, $value, $depart_date, $origin, $flight_id, $destination, $class_code, $publish, $tax, $total, $from_date, $to_date, $passenger_num, $route, $time_depart, $time_arrive, $return_param){
	
		$depart_date = new DateTime($depart_date);
		$monthFirst = $depart_date->format('m-d-Y');
		$dayFirst = $depart_date->format('d-m-Y');
		$yearFirst = $depart_date->format('Y-m-d');
		$epochdate = $depart_date->format('U');
		$flight_id = str_replace('/\s+/', '  ', $flight_id);
		$passenger_num = ((int)$_POST['adult_passenger_num'] + (int)$_POST['child_passenger_num'] + (int)$_POST['infant_passenger_num']);

		if($return_param != NULL){
			var_dump($return_param);

			$flight_id_ret = $return_param['flight_id_ret'];
			$route_ret = $return_param['route_ret'];
			$date_ret = $return_param['date_ret'];
			$class_code_ret = $return_param['class_code_ret'];
			$value_ret = $return_param['value_ret'];
			$publish_ret = $return_param['publish_ret'];
			$tax_ret = $return_param['tax_ret'];
			$total_ret = $return_param['total_ret'];
			$time_depart_ret = $return_param['time_depart_ret'];
			$time_arrive_ret = $return_param['time_arrive_ret'];
		
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
	
		$postBook = $postBook. "&check_box3400=" . $this->atrisUrlEncode("1~CitilinkAPI|3400|". $flight_id . "|" . $route ."|1|" . $date_arranged .  "~" . $class_code . "~" . $value. "~" . $publish . "|" . $tax .  "|" . $total . "|IDR|IDR") . $chkbox3450;
	
		var_dump( $postBook);

		$response = $this->scraping->request('citilinkbooking.html', 'https://atris.versatiket.co.id/api/bookingairlines/booking', $postBook);
		return $response;
	}
}
?>