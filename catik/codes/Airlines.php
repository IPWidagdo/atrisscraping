<?php
require "scraping.php";

class Airlines {
	var $username = "";
	var $password = "";
	var $tmp_void;
	var $url = 'https://atris.versatech.co.id';

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
		$response = $this->scraping->request('checklogin.html', $this->url . '/api/admin/getuserlogin', "");
		
		if ($response == NULL)
			return false;
		return true; 
		
	}

	function setUserNamePassword($username, $password) {
		$this->username = $username;
		$this->password = $password;
	}

	function login() {
		if(!$this->checkLogin()){
			$response = $this->scraping->request('login.html', $this->url . '/api/admin', "user=".$this->username."&password=".$this->password);	
		}		
	}

	function logout() {
		//logout
		$response = $this->scraping->request('logout.html', $this->url . '/api/admin/logout', "");
		//unlink($this->scraping->session_file);
		return $response;

	}

	function getAvailability($depart_date, $origin, $destination, $adult, $child, $infant, $date_ret, $flight_id_ret, $flightID) {
		if($date_ret !="" && $flight_id_ret != ""){
			$return_code = $_POST['berangkat'];
		} elseif($date_ret =="" && $flight_id_ret == ""){
			$return_code = "";
			$date_ret = $depart_date;
		};

		$postAvailability = "adult=". $adult . "&child=". $child . "&infant=". $infant . "&from_code=". $origin ."&to_code=". $destination ."&from_date=". $depart_date ."&to_date=". $date_ret ."&return_code=" .$return_code;
		// var_dump($postAvailability);
		if(substr($flightID, 0, 2) == "QG"){
			$response = $this->scraping->request('citilinkapi.html', $this->url . '/api/bookingairlines/citilinkapi', $postAvailability);
			return $response;
		} elseif (substr($flightID, 0, 2) == "JT" || substr($flightID, 0, 2) == "ID" || substr($flightID, 0, 2) == "OW" || substr($flightID, 0, 2) == "IW"){
			$response = $this->scraping->request('lionh2h.html', $this->url . '/api/bookingairlines/lionh2h', $postAvailability);
			return $response;
		}  elseif (substr($flightID, 0, 2) == "SJ" || substr($flightID, 0, 2) == "IN"){
			$response = $this->scraping->request('sriwijayaapi.html', $this->url . '/api/bookingairlines/sriwijayaapi', $postAvailability);
			return $response;
		} elseif (substr($flightID, 0, 2) == "GA"){
			$response = $this->scraping->request('garudaapi.html', $this->url . '/api/bookingairlines/garudaapi', $postAvailability);
			return $response;
		}
		//  elseif (substr($flightID, 0, 2) == "GA"){
		// 	$response = $this->scraping->request('garudaapialtea.html', '$this->url . '/api/bookingairlines/ajaxkonsorsiumaltea', $postAvailability);
		// 	return $response;
		// }
	}

	function atrisUrlEncode($params) {
		$params = str_replace("%7E", "~", str_replace("%2A", "*", urlencode($params)));
		return $params;
	}

	function getFare($value, $depart_date, $origin, $flightID, $destination, $class_code, $time_depart, $time_arrive, $seq, $adult_passenger_num, $child_passenger_num, $infant_passenger_num, $from_date, $to_date, $route, $longdate){	
		if(substr($flightID, 0, 2) == "QG"){
			$postFare = "id=3400"."&choice=".$this->atrisUrlEncode($value)."&date=".$longdate . "&from_date=". $this->atrisUrlEncode($time_depart) ."&to_date=". $this->atrisUrlEncode($time_arrive) ."&from_code=".$origin."&to_code=".$destination."&adult=". $adult_passenger_num ."&child=". $child_passenger_num ."&infant=". $infant_passenger_num ."&row=3400"."&class_code=". $this->atrisUrlEncode($class_code)."&chkbox=3400"."&seq=".$seq."&defcurr=IDR&info=". $this->atrisUrlEncode("1~CitilinkAPI|3400|" . $flightID . "|" . $route. "|1|" . $time_depart . "|" . $time_arrive . "~" . $class_code . "~" . $value . "~0|0|0");
			// var_dump($postFare);
			$response = $this->scraping->request('citilinkgetfare.html', $this->url . '/api/bookingairlines/ajaxcitilinkapifare', $postFare);
			return $response;

		} elseif (substr($flightID, 0, 2) == "JT" || substr($flightID, 0, 2) == "ID" || substr($flightID, 0, 2) == "OW" || substr($flightID, 0, 2) == "IW"){
			$postFare = "id=3000"."&choice=".$this->atrisUrlEncode($value)."&date=".$longdate . "&from_date=". $this->atrisUrlEncode($time_depart) ."&to_date=". $this->atrisUrlEncode($time_arrive) ."&from_code=".$origin."&to_code=".$destination."&adult=". $adult_passenger_num ."&child=". $child_passenger_num ."&infant=". $infant_passenger_num ."&row=3000"."&class_code=". $this->atrisUrlEncode($class_code)."&chkbox=3000"."&seq=".$seq."&defcurr=IDR&info=". $this->atrisUrlEncode("1~LionH2H|3000|" . $flightID . "|" . $route. "|1|" . $time_depart . "|" . $time_arrive . "~" . $class_code . "~" . $value . "~0|0|0");
			$response = $this->scraping->request('liongetfare.html', $this->url . '/api/bookingairlines/ajaxlionh2hfare', $postFare);
			// var_dump($postFare);
			return $response;
		}  elseif (substr($flightID, 0, 2) == "SJ" || substr($flightID, 0, 2) == "IN"){
			$postFare = "id=3300"."&choice=".$this->atrisUrlEncode($value)."&date=".$longdate . "&from_date=". $this->atrisUrlEncode($time_depart) ."&to_date=". $this->atrisUrlEncode($time_arrive) ."&from_code=".$origin."&to_code=".$destination."&adult=". $adult_passenger_num ."&child=". $child_passenger_num ."&infant=". $infant_passenger_num ."&row=3300"."&class_code=". $this->atrisUrlEncode($class_code)."&chkbox=3300"."&seq=".$seq."&defcurr=IDR&info=". $this->atrisUrlEncode("1~SriwijayaAPI|3300|" . $flightID . "|" . $route. "|1|" . $time_depart . "|" . $time_arrive . "~" . $class_code . "~" . $value . "~0|0|0");
			$response = $this->scraping->request('sriwijayagetfare.html', $this->url . '/api/bookingairlines/ajaxsriwijayaapifare', $postFare);
			// var_dump($postFare);
			return $response;
		} elseif (substr($flightID, 0, 2) == "GA" ){
			$postFare = "id=2200"."&choice=".$this->atrisUrlEncode($value)."&date=".$longdate . "&from_date=". $this->atrisUrlEncode($time_depart) ."&to_date=". $this->atrisUrlEncode($time_arrive) ."&from_code=".$origin."&to_code=".$destination."&adult=". $adult_passenger_num ."&child=". $child_passenger_num ."&infant=". $infant_passenger_num ."&row=2200"."&class_code=". $this->atrisUrlEncode($class_code)."&chkbox=2200"."&seq=".$seq."&defcurr=IDR&info=". $this->atrisUrlEncode("1~GarudaAPI|2200|" . $flightID . "|" . $route. "|1|" . $time_depart . "|" . $time_arrive . "~" . $class_code . "~" . $value . "~0|0|0");
			$response = $this->scraping->request('garudagetfare.html', $this->url . '/api/bookingairlines/ajaxgarudaapifare', $postFare);
			// var_dump($postFare);
			return $response;
		}
		//  elseif (substr($flightID, 0, 2) == "GA" ){
		// 	$postFare = "id=2800"."&choice=".$this->atrisUrlEncode($value)."&date=".$longdate . "&from_date=". $this->atrisUrlEncode($time_depart) ."&to_date=". $this->atrisUrlEncode($time_arrive) ."&from_code=".$origin."&to_code=".$destination."&adult=". $adult_passenger_num ."&child=". $child_passenger_num ."&infant=". $infant_passenger_num ."&row=2800"."&class_code=". $this->atrisUrlEncode($class_code)."&chkbox=2800"."&seq=".$seq."&defcurr=IDR&info=". $this->atrisUrlEncode("1~KonsorsiumAltea|2800|" . $flightID . "|" . $route. "|1|" . $time_depart . "|" . $time_arrive . "~" . $class_code . "~" . $value . "~0|0|0");
		// 	$response = $this->scraping->request('garudagetfarealtea.html', '$this->url . '/api/bookingairlines/ajaxkonsorsiumalteafare', $postFare);
		// 	var_dump($postFare);
		// 	return $response;
		//} 
		else echo "Salah parameter getfare kali ya? :/";
	}


	function getFareRet($value_ret, $depart_date_ret, $origin_ret, $flightID_ret, $destination_ret, $class_code_ret, $time_depart_ret, $time_arrive_ret, $seq_ret, $adult_passenger_num_ret, $child_passenger_num_ret, $infant_passenger_num_ret, $from_date, $to_date, $route_ret, $longdate_ret){	

		if(substr($flightID_ret, 0, 2) == "QG"){
			$postFare_ret = "id=3450"."&choice=".$this->atrisUrlEncode($value_ret)."&date=".$longdate_ret."&from_date=".$this->atrisUrlEncode($time_depart_ret)."&to_date=".$this->atrisUrlEncode($time_arrive_ret)."&from_code=".$origin_ret."&to_code=".$destination_ret."&adult=". $adult_passenger_num_ret ."&child=". $child_passenger_num_ret ."&infant=". $infant_passenger_num_ret ."&row=3450"."&class_code=".$this->atrisUrlEncode($class_code_ret)."&chkbox=3450"."&seq=".$seq_ret."&defcurr=IDR&info=". $this->atrisUrlEncode("1~CitilinkAPI|3450|" . $flightID_ret . "|" . $route_ret . "|1|" . $time_depart_ret . "|" . $time_arrive_ret . "~" . $class_code_ret . "~" . $value_ret . "~0|0|0");
			// var_dump($postFare_ret);
			$response = $this->scraping->request('citilinkapifareret.html', $this->url . '/api/bookingairlines/ajaxcitilinkapifare', $postFare_ret);
			return $response;
		
		} elseif (substr($flightID_ret, 0, 2) == "JT" || substr($flightID_ret, 0, 2) == "ID" || substr($flightID_ret, 0, 2) == "OW" || substr($flightID_ret, 0, 2) == "IW"){
			$postFare_ret = "id=3050"."&choice=".$this->atrisUrlEncode($value_ret)."&date=".$longdate_ret."&from_date=".$this->atrisUrlEncode($time_depart_ret)."&to_date=".$this->atrisUrlEncode($time_arrive_ret)."&from_code=".$origin_ret."&to_code=".$destination_ret."&adult=". $adult_passenger_num_ret ."&child=". $child_passenger_num_ret ."&infant=". $infant_passenger_num_ret ."&row=3050"."&class_code=".$this->atrisUrlEncode($class_code_ret)."&chkbox=3050"."&seq=".$seq_ret."&defcurr=IDR&info=". $this->atrisUrlEncode("1~LionH2H|3050|" . $flightID_ret . "|" . $route_ret . "|1|" . $time_depart_ret . "|" . $time_arrive_ret . "~" . $class_code_ret . "~" . $value_ret . "~0|0|0");
			// var_dump($postFare_ret);
			$response = $this->scraping->request('liongetfareret.html', $this->url . '/api/bookingairlines/ajaxlionh2hfare', $postFare_ret);
			return $response;
		} elseif (substr($flightID_ret, 0, 2) == "SJ" || substr($flightID_ret, 0, 2) == "IN"){
			$postFare_ret = "id=3350"."&choice=".$this->atrisUrlEncode($value_ret)."&date=".$longdate_ret."&from_date=".$this->atrisUrlEncode($time_depart_ret)."&to_date=".$this->atrisUrlEncode($time_arrive_ret)."&from_code=".$origin_ret."&to_code=".$destination_ret."&adult=". $adult_passenger_num_ret ."&child=". $child_passenger_num_ret ."&infant=". $infant_passenger_num_ret ."&row=3350"."&class_code=".$this->atrisUrlEncode($class_code_ret)."&chkbox=3350"."&seq=".$seq_ret."&defcurr=IDR&info=". $this->atrisUrlEncode("1~SriwijayaAPI|3350|" . $flightID_ret . "|" . $route_ret . "|1|" . $time_depart_ret . "|" . $time_arrive_ret . "~" . $class_code_ret . "~" . $value_ret . "~0|0|0");
			// var_dump($postFare_ret);
			$response = $this->scraping->request('sriwijayagetfareret.html', $this->url . '/api/bookingairlines/ajaxsriwijayaapifare', $postFare_ret);
			return $response;
		} elseif (substr($flightID_ret, 0, 2) == "GA"){
			$postFare_ret = "id=2250"."&choice=".$this->atrisUrlEncode($value_ret)."&date=".$longdate_ret."&from_date=".$this->atrisUrlEncode($time_depart_ret)."&to_date=".$this->atrisUrlEncode($time_arrive_ret)."&from_code=".$origin_ret."&to_code=".$destination_ret."&adult=". $adult_passenger_num_ret ."&child=". $child_passenger_num_ret ."&infant=". $infant_passenger_num_ret ."&row=2250"."&class_code=".$this->atrisUrlEncode($class_code_ret)."&chkbox=2250"."&seq=".$seq_ret."&defcurr=IDR&info=". $this->atrisUrlEncode("1~SriwijayaAPI|2250|" . $flightID_ret . "|" . $route_ret . "|1|" . $time_depart_ret . "|" . $time_arrive_ret . "~" . $class_code_ret . "~" . $value_ret . "~0|0|0");
			// var_dump($postFare_ret);
			$response = $this->scraping->request('garudagetfareret.html', $this->url . '/api/bookingairlines/ajaxgarudaapifare', $postFare_ret);
			return $response;
		}
		//  elseif (substr($flightID_ret, 0, 2) == "GA"){
		// 	$postFare_ret = "id=2850"."&choice=".$this->atrisUrlEncode($value_ret)."&date=".$longdate_ret."&from_date=".$this->atrisUrlEncode($time_depart_ret)."&to_date=".$this->atrisUrlEncode($time_arrive_ret)."&from_code=".$origin_ret."&to_code=".$destination_ret."&adult=". $adult_passenger_num_ret ."&child=". $child_passenger_num_ret ."&infant=". $infant_passenger_num_ret ."&row=2850"."&class_code=".$this->atrisUrlEncode($class_code_ret)."&chkbox=2850"."&seq=".$seq_ret."&defcurr=IDR&info=". $this->atrisUrlEncode("1~KonsorsiumAltea|2850|" . $flightID_ret . "|" . $route_ret . "|1|" . $time_depart_ret . "|" . $time_arrive_ret . "~" . $class_code_ret . "~" . $value_ret . "~0|0|0");
		// 	var_dump($postFare_ret);
		// 	$response = $this->scraping->request('garudagetfareretaltea.html', '$this->url . '/api/bookingairlines/ajaxcitilinkapifare', $postFare_ret);
		// 	return $response;
		//} 
		else echo "Salah parameter getfareret kali ya? :/";
	}

	function getBooking($data_penumpang, $email, $phone_number0, $value, $depart_date, $origin, $flight_id, $destination, $class_code, $publish, $tax, $total, $from_date, $to_date, $passenger_num, $route, $time_depart, $time_arrive, $return_param, $date_ret){
	
		$depart_date = new DateTime($depart_date);
		$monthFirst = $depart_date->format('m-d-Y');
		$dayFirst = $depart_date->format('d-m-Y');
		$yearFirst = $depart_date->format('Y-m-d');
		$epochdate = $depart_date->format('U');
		$flight_id = str_replace('/\s+/', '  ', $flight_id);
		$passenger_num = ((int)$_POST['adult_passenger_num'] + (int)$_POST['child_passenger_num'] + (int)$_POST['infant_passenger_num']);

		if($return_param != NULL && $date_ret != NULL){
			// var_dump($return_param);

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
			
			if(substr($flight_id, 0, 2) == "QG"){
				$route_rt = "%2C3450";
				$flightnumber_ret = "3450";
				$flightnumber_dep = "3400";
				$flight_code = "CitilinkAPI";
			}
			elseif(substr($flight_id, 0, 2) == "JT" || substr($flight_id, 0, 2) == "ID" || substr($flight_id, 0, 2) == "OW" || substr($flight_id, 0, 2) == "IW"){
				$route_rt = "%2C3050";
				$flightnumber_ret = "3050";
				$flightnumber_dep = "3000";
				$flight_code = "LionH2H";
			} elseif(substr($flight_id, 0, 2) == "SJ" || substr($flight_id, 0, 2) == "IN"){
				$route_rt = "%2C3350";
				$flightnumber_ret = "3350";
				$flightnumber_dep = "3300";
				$flight_code = "SriwijayaAPI";
			} elseif(substr($flight_id, 0, 2) == "GA"){
				$route_rt = "%2C2250";
				$flightnumber_ret = "2250";
				$flightnumber_dep = "2200";
				$flight_code = "GarudaAPI";
			} 
			// elseif(substr($flight_id, 0, 2) == "GA"){
			// 	$route_rt = "%2C2850";
			// 	$flightnumber_ret = "2850";
			// 	$flightnumber_dep = "2200";
			// 	$flight_code = "KonsorsiumAltea";
			//	
			
			$return_code = $_POST['berangkat'];
			$date_ret  = new DateTime($date_ret);
			$to_date = $date_ret->format('d-m-Y');
			$to_date_u_ret = $date_ret->format('U');


			// echo "time depart ret: "; var_dump($time_depart_ret);
			// echo "time arrive ret: "; var_dump($time_arrive_ret);
			// echo "split ret";
			if(strlen($time_depart_ret) > 12 && strlen($time_arrive_ret)>12){
				$time_depart_ret_exp = explode("##", $time_depart_ret);
				$time_depart_ret0 = $time_depart_ret_exp[0];
				$time_depart_ret1 = $time_depart_ret_exp[1];
				$time_arrive_ret_exp = explode("##", $time_arrive_ret);
				$time_arrive_ret0 = $time_arrive_ret_exp[0];
				$time_arrive_ret1 = $time_arrive_ret_exp[1];
		
				$date_arranged_ret =  $time_depart_ret0 ."##". $time_arrive_ret0 . "|" . $time_depart_ret1 . "##" . $time_arrive_ret1; 

			} else $date_arranged_ret = $time_depart_ret . "|" . $time_arrive_ret;

			$chkbox_ret = "&check_box". $flightnumber_ret ."=" . $this->atrisUrlEncode("1~". $flight_code ."|$flightnumber_ret|". $flight_id_ret . "|" . $route_ret ."|1|" . $date_arranged_ret .  "~" . $class_code_ret . "~" . $value_ret. "~" . $publish_ret . "|" . $tax_ret .  "|" . $total_ret . "|IDR|IDR");

		if(substr($flight_id, 0, 2) == "QG"){
			$route_rt = "%2C3450";
			$flightnumber_ret = "3450";
			$flightnumber_dep = "3400";
			$flight_code = "CitilinkAPI";
		} elseif(substr($flight_id, 0, 2) == "JT" || substr($flight_id, 0, 2) == "ID" || substr($flight_id, 0, 2) == "OW" || substr($flight_id, 0, 2) == "IW"){
			$route_rt = "%2C3050";
			$flightnumber_ret = "3050";
			$flightnumber_dep = "3000";
			$flight_code = "LionH2H";
		} elseif(substr($flight_id, 0, 2) == "SJ" || substr($flight_id, 0, 2) == "IN"){
			$route_rt = "%2C3350";
			$flightnumber_ret = "3350";
			$flightnumber_dep = "3300";
			$flight_code = "SriwijayaAPI";
		} elseif(substr($flight_id, 0, 2) == "GA"){
			$route_rt = "%2C2250";
			$flightnumber_ret = "2250";
			$flightnumber_dep = "2200";
			$flight_code = "GarudaAPI";
		} 

	} elseif($return_param == ""){
		$route_rt = "";
		$return_code = "";
		$to_date = $epochdate;
		$chkbox_ret = "";
		if(substr($flight_id, 0, 2) == "QG"){
			$flightnumber_dep = "3400";
			$flight_code = "CitilinkAPI";
		}
		elseif(substr($flight_id, 0, 2) == "JT" || substr($flight_id, 0, 2) == "ID" || substr($flight_id, 0, 2) == "OW" || substr($flight_id, 0, 2) == "IW"){
			$flightnumber_dep = "3000";
			$flight_code = "LionH2H";
		} elseif(substr($flight_id, 0, 2) == "SJ" || substr($flight_id, 0, 2) == "IN"){
			$flightnumber_dep = "3300";
			$flight_code = "SriwijayaAPI";
		} elseif(substr($flight_id, 0, 2) == "GA"){
			$flightnumber_dep = "2200";
			$flight_code = "GarudaAPI";
		} 
	} 

		$postBook = "route=" . $flightnumber_dep . $route_rt ."&from_code=".$origin."&to_code=". $destination ."&return_code=". $return_code ."&from_date=".$dayFirst."&to_date=". $to_date."&count_passenger=". $passenger_num ."&adult=".(int)$_POST['adult_passenger_num']."&child=". (int)$_POST['child_passenger_num'] ."&infant=". (int)$_POST['infant_passenger_num'];
		
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
		// echo "time depart : "; var_dump($time_depart);
		// echo "time arrive : "; var_dump($time_arrive);
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
	
		$postBook = $postBook. "&check_box". $flightnumber_dep ."=" . $this->atrisUrlEncode("1~". $flight_code ."|$flightnumber_dep|". $flight_id . "|" . $route ."|1|" . $date_arranged .  "~" . $class_code . "~" . $value. "~" . $publish . "|" . $tax .  "|" . $total . "|IDR|IDR") . $chkbox_ret;
	
		var_dump( $postBook);

		$response = $this->scraping->request('citilinkbooking.html', $this->url . '/api/bookingairlines/booking', $postBook);
		return $response;
	}
	
	function searchBook($begin_date, $end_date, $lastname, $book_code){
	
		if ($end_date == ""){
			$end_date = date("d-m-Y");
		}

		if ($begin_date == ""){
			$begin_date = date("d-m-Y")-3; 
		}

		$begin_date_day_first = date('d-m-Y',strtotime($begin_date));
		$end_date_day_first = date('d-m-Y',strtotime($end_date));

		if ($lastname != ""){
			$search_choice = "passenger_name";
			$seacrh_input = $lastname;
		} elseif ($book_code != ""){
			$search_choice = "booking_code";
			$seacrh_input = $book_code;
		} elseif ($lastname !="" && $book_code !=""){
			echo "Inputnya yang bener, milih salah satu aja. -_-";
		} elseif ($lastname =="" && $book_code ==""){
			echo "Inputnya yang bener, belum diisi itu. -_-";
		}
		
		// var_dump($end_date, $begin_date);

		$post_search = "closeBtn=Close&act_search=Find&option=per_pages&partial=1&status_choice=any&find_in=any&issued_start_date=" . $end_date_day_first . "&booking_start_date=". $end_date_day_first. "&start_date=". $end_date_day_first ."&column_choice=".$search_choice."&booking_end_periode_date=". $end_date_day_first ."&booking_start_periode_date=". $begin_date_day_first ."&search_txt=". $seacrh_input;
		// var_dump($post_search);

		// nonpermanent $response = $this->scraping->request('searchbook.html', '$this->url . '/api/ticketingairlines/search', $post_search);
		$response = $this->scraping->request('searchbook.html', $this->url . '/api/ticketingairlines/search', $post_search);
		
		return $response;
	} 
	
	function infoBook($booking_id){
		$post_booking_id = "id=" . $booking_id;
		$response = $this->scraping->request('detailbook.html', $this->url . '/api/ticketingairlines/info', $post_booking_id);
		return $response;

	} 
	
	function printIssued($booking_id, $post_var, $schedule_flight_id){
		$urlrits = 'http://rits.versatech.co.id';

		$post_status = $post_var;
		var_dump($post_status);
		if(substr($schedule_flight_id, 0, 2) == "QG"){
			$this->logout();
			$login_rits = $this->scraping->request('login_rits.html', $urlrits. '/admin', 'user=' . $this->username . '&password=' . $this->password . '&commit=Login' );
			$content = $this->scraping->request('printissued.html', $urlrits. '/ticketingairlines/print/h2h_id/36/booking_id/' . $booking_id, $post_status);
			$logout_rits = $this->scraping->request('logout_rits.html',$urlrits. '/admin/logout', "");
			header("Location: printissued.html");
		} 
		elseif(substr($schedule_flight_id, 0, 2) == "JT" || substr($schedule_flight_id, 0, 2) == "ID" || substr($schedule_flight_id, 0, 2) == "OW" || substr($schedule_flight_id, 0, 2) == "IW"){
			$content = $this->scraping->request('printissued.pdf', $this->url . '/api/ticketingairlines/print/h2h_id/34/booking_id/' . $booking_id, $post_status);
			header("Location: printissued.pdf");
		}
		elseif(substr($schedule_flight_id, 0, 2) == "SJ" || substr($schedule_flight_id, 0, 2) == "IN"){
			$this->logout();
			$login_rits = $this->scraping->request('login_rits.html', $urlrits. '/admin', 'user=' . $this->username . '&password=' . $this->password . '&commit=Login' );
			$content = $this->scraping->request('printissued.html', $urlrits. '/ticketingairlines/print/h2h_id/35/booking_id/' . $booking_id, ' ');
			$logout_rits = $this->scraping->request('logout_rits.html',$urlrits. '/admin/logout', "");
			header("Location: printissued.html");
		} 
		elseif(substr($schedule_flight_id, 0, 2) == "GA"){
			$this->logout();
			$login_rits = $this->scraping->request('login_rits.html', $urlrits. '/admin', 'user=' . $this->username . '&password=' . $this->password . '&commit=Login' );
			$content = $this->scraping->request('printissued.html', $urlrits. '/ticketingairlines/print/h2h_id/32/booking_id/' . $booking_id, $post_status);
			$logout_rits = $this->scraping->request('logout_rits.html',$urlrits. '/admin/logout', "");
			header("Location: printissued.html");
		} 
	}

	function issuePayment($booking_id, $captcha_code = NULL){
		$url = $this->url;
		
		if ($captcha_code != NULL){
			$lionh2hcaptcha = $this->scraping->request('lionh2hcaptcha.html',  $url . '/api/bookingairlines/lionh2hcaptcha', 'captcha=' . $captcha_code); 
		} 
		
		$post_info_issue = $this->infoBook($booking_id); 
		$confirmpassword = $this->scraping->request('confirmpassword.html',  $url . '/api/ticketingairlines/confirmpassword', 'id=' . $booking_id);

		if($this->checkLogin()) {
			$ajaxticketing = $this->scraping->request('ajaxticketing.html',  $url . '/api/ticketingairlines/ajaxticketing', 'id=' . $booking_id . '&password=' . $this->password);
			var_dump($this->password);
			return $ajaxticketing;
		} else { 
			return json_encode("{code:'error', 'message':'udah logout'}");
		} 	
		
	}

	function isCaptchaResponse( $response ) {
		if ( array_key_exists('content', $response) && array_key_exists('captcha_string', $response['content']) ){
			$captcha_string = $response['content']['captcha_string'];
			$captcha_string = str_replace('data:image/jpg;base64,', '', $captcha_string );
			$captcha_string = str_replace('\/', '/', $captcha_string );
			var_dump($captcha_string);
			$captcha_img = base64_decode($captcha_string);
			$file = 'lionh2hcaptcha' . uniqid() . '.jpeg';
			$success = file_put_contents($file, $captcha_img);
			return $file;
		}
		return false;
	}
}
?>
