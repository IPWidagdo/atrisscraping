<?php
require "scraping.php";

class Airlines {
	var $username = "";
	var $password = "";
	var $tmp_void;
	//var $url = 'https://atris.versatech.co.id';
	var $url = 'https://atris.versatiket.co.id';

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
		} elseif (substr($flightID, 0, 2) == "JT" || substr($flightID, 0, 2) == "ID" || substr($flightID, 0, 2) == "IW"){
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

	function getFare($value, $depart_date, $origin, $flightID, $destination, $class_code, $time_depart, $time_arrive, $seq, $adult_passenger_num, $child_passenger_num, $infant_passenger_num, $from_date, $to_date, $route, $longdate) {	
		if(substr($flightID, 0, 2) == "QG"){
			$postFare = "id=3400"."&choice=".$this->atrisUrlEncode($value)."&date=".$longdate . "&from_date=". $this->atrisUrlEncode($time_depart) ."&to_date=". $this->atrisUrlEncode($time_arrive) ."&from_code=".$origin."&to_code=".$destination."&adult=". $adult_passenger_num ."&child=". $child_passenger_num ."&infant=". $infant_passenger_num ."&row=3400"."&class_code=". $this->atrisUrlEncode($class_code)."&chkbox=3400"."&seq=".$seq."&defcurr=IDR&info=". $this->atrisUrlEncode("1~CitilinkAPI|3400|" . $flightID . "|" . $route. "|1|" . $time_depart . "|" . $time_arrive . "~" . $class_code . "~" . $value . "~0|0|0");
			var_dump($postFare);
			$response = $this->scraping->request('citilinkgetfare.html', $this->url . '/api/bookingairlines/ajaxcitilinkapifare', $postFare);
			return $response;

		} elseif (substr($flightID, 0, 2) == "JT" || substr($flightID, 0, 2) == "ID" || substr($flightID, 0, 2) == "IW"){
			if(strlen($flightID>10)){
				$type_flight = "2~LionH2H|3000|" . $flightID0 . "|" . $route0. "|1|" . $time_depart0 . "|" . $time_arrive0 . "~" . $class_code0 . "|" . $flightID1 . "|" . $route1. "|1|" . $time_depart1 . "|" . $time_arrive1 . "~" . $class_code1 . "~" . $value_transit . "~0|0|0";
			} else {
				$type_flight = "1~LionH2H|3000|" . $flightID . "|" . $route. "|1|" . $time_depart . "|" . $time_arrive . "~" . $class_code . "~" . $value . "~0|0|0";
			}

				$postFare = "id=3000"."&choice=".$this->atrisUrlEncode($value)."&date=".$longdate . "&from_date=". $this->atrisUrlEncode($time_depart) ."&to_date=". $this->atrisUrlEncode($time_arrive) ."&from_code=".$origin."&to_code=".$destination."&adult=". $adult_passenger_num ."&child=". $child_passenger_num ."&infant=". $infant_passenger_num ."&row=3000"."&class_code=". $this->atrisUrlEncode($class_code)."&chkbox=3000"."&seq=".$seq."&defcurr=IDR&info=". $this->atrisUrlEncode($type_flight);

			$response = $this->scraping->request('liongetfare.html', $this->url . '/api/bookingairlines/ajaxlionh2hfare', $postFare);
			// var_dump($postFare);
			return $response;


		}  elseif (substr($flightID, 0, 2) == "SJ" || substr($flightID, 0, 2) == "IN"){
			$postFare = "id=3300"."&choice=".$this->atrisUrlEncode($value)."&date=".$longdate . "&from_date=". $this->atrisUrlEncode($time_depart) ."&to_date=". $this->atrisUrlEncode($time_arrive) ."&from_code=".$origin."&to_code=".$destination."&adult=". $adult_passenger_num ."&child=". $child_passenger_num ."&infant=". $infant_passenger_num ."&row=3300"."&class_code=". $this->atrisUrlEncode($class_code)."&chkbox=3300"."&seq=".$seq."&defcurr=IDR&info=". $this->atrisUrlEncode("1~SriwijayaAPI|3300|" . $flightID . "|" . $route. "|1|" . $time_depart . "|" . $time_arrive . "~" . $class_code . "~" . $value . "~0|0|0");
			$response = $this->scraping->request('sriwijayagetfare.html', $this->url . '/api/bookingairlines/ajaxsriwijayaapifare', $postFare);
			var_dump($postFare);
			return $response;
		} elseif (substr($flightID, 0, 2) == "GA" ){
			$postFare = "id=2200"."&choice=".$this->atrisUrlEncode($value)."&date=".$longdate . "&from_date=". $this->atrisUrlEncode($time_depart) ."&to_date=". $this->atrisUrlEncode($time_arrive) ."&from_code=".$origin."&to_code=".$destination."&adult=". $adult_passenger_num ."&child=". $child_passenger_num ."&infant=". $infant_passenger_num ."&row=2200"."&class_code=". $this->atrisUrlEncode($class_code)."&chkbox=2200"."&seq=".$seq."&defcurr=IDR&info=". $this->atrisUrlEncode("1~GarudaAPI|2200|" . $flightID . "|" . $route. "|1|" . $time_depart . "|" . $time_arrive . "~" . $class_code . "~" . $value . "~0|0|0");
			$response = $this->scraping->request('garudagetfare.html', $this->url . '/api/bookingairlines/ajaxgarudaapifare', $postFare);
			// var_dump($postFare);
			return $response;
		}
		
		else echo "Salah parameter getfare kali ya? :/";
	}


	function getFareRet($value_ret, $depart_date_ret, $origin_ret, $flightID_ret, $destination_ret, $class_code_ret, $time_depart_ret, $time_arrive_ret, $seq_ret, $adult_passenger_num_ret, $child_passenger_num_ret, $infant_passenger_num_ret, $from_date, $to_date, $route_ret, $longdate_ret){	

		if(substr($flightID_ret, 0, 2) == "QG"){
			$postFare_ret = "id=3450"."&choice=".$this->atrisUrlEncode($value_ret)."&date=".$longdate_ret."&from_date=".$this->atrisUrlEncode($time_depart_ret)."&to_date=".$this->atrisUrlEncode($time_arrive_ret)."&from_code=".$origin_ret."&to_code=".$destination_ret."&adult=". $adult_passenger_num_ret ."&child=". $child_passenger_num_ret ."&infant=". $infant_passenger_num_ret ."&row=3450"."&class_code=".$this->atrisUrlEncode($class_code_ret)."&chkbox=3450"."&seq=".$seq_ret."&defcurr=IDR&info=". $this->atrisUrlEncode("1~CitilinkAPI|3450|" . $flightID_ret . "|" . $route_ret . "|1|" . $time_depart_ret . "|" . $time_arrive_ret . "~" . $class_code_ret . "~" . $value_ret . "~0|0|0");
			// var_dump($postFare_ret);
			$response = $this->scraping->request('citilinkapifareret.html', $this->url . '/api/bookingairlines/ajaxcitilinkapifare', $postFare_ret);
			return $response;
		
		} elseif (substr($flightID_ret, 0, 2) == "JT" || substr($flightID_ret, 0, 2) == "ID" || substr($flightID_ret, 0, 2) == "IW"){
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

	// function getFareLion($value, $depart_date, $origin, $destination, $class_code, $time_depart, $time_arrive, $seq, $adult_passenger_num, $child_passenger_num, $infant_passenger_num, $from_date, $to_date, $route, $longdate, $class_code_lion_1, $time_depart_lion_1, $time_arrive_lion_1, $route_lion_1, $value_lion_1, $flightID0, $flightID1){	
		
	// 	$postFare = "id=3000&choice=" . $this->atrisUrlEncode($value) . "&date=" . $longdate . "&from_date=" . $time_depart . "&to_date=" . $time_arrive . "&from_code=" . $origin . "&to_code=" . $destination . "&adult=" . $adult_passenger_num . "&child=" . $child_passenger_num . "&infant=" . $infant_passenger_num . "&row=3001&class_code=" . $class_code . "&chkbox=3000&seq=" . $seq. "&defcurr=IDR&info=2" . $this->atrisUrlEncode("~LionH2H|3000|" . $flightID0 . "|" . $route . "|1|" . $time_depart . "|" .$time_arrive . "~LionH2H|3000|" . $flightID1 . "|" . $route_lion_1 . "|1|" . $time_depart_lion_1 . "|" . $time_arrive . "~" . $class_code . "~" . $class_code_lion_1 . "~" . $value . "~" . $value_lion_1 . "~0|0|0");


	// 	// $type_flight = "2~LionH2H|3000|" . $flightID1 . "|" . $route0. "|1|" . $time_depart_lion_1 . "|" . $time_arrive_lion_1 . "~" . $class_code0 . "|" . $flightID1 . "|" . $route_lion_1. "|1|" . $time_depart1 . "|" . $time_arrive1 . "~" . $class_code_lion_1 . "~" . $value . "~" . $value_lion_1 . "~0|0|0";

	// 	// $postFare = "id=3000"."&choice=".$this->atrisUrlEncode($value)."&date=".$longdate . "&from_date=". $this->atrisUrlEncode($time_depart) ."&to_date=". $this->atrisUrlEncode($time_arrive) ."&from_code=".$origin."&to_code=".$destination."&adult=". $adult_passenger_num ."&child=". $child_passenger_num ."&infant=". $infant_passenger_num ."&row=3000"."&class_code=". $this->atrisUrlEncode($class_code)."&chkbox=3000"."&seq=".$seq."&defcurr=IDR&info=". $type_flight;

	// 	$response = $this->scraping->request('liongetfare.html', $this->url . '/api/bookingairlines/ajaxlionh2hfare', $postFare);
	// 	var_dump($postFare);
	// 	return $response;

	// }

	function getFareMultiFlight($availability, $flight_id, $origin, $destination, $adult_passenger_num, $child_passenger_num, $infant_passenger_num, $dateBook, $return_trip) {
		echo("step 1");
		//var_dump($availability);
		//exit();

		if (is_array($availability) || is_object($availability)) {  
			echo("step 3<br/>");
			$flight_id_multi = explode("##", $flight_id);
			$flight_num = sizeof($flight_id_multi);		
			// var_dump($flight_num);
			
			// BATAS
			if ($return_trip == TRUE){
				$depart_schedule = $availability['content']['return_schedule'];
				$schedule_len = sizeof($depart_schedule);
			} elseif ($return_trip == FALSE){
				$depart_schedule = $availability['content']['depart_schedule'];
				$schedule_len = sizeof($depart_schedule);
			}
			
			
			for($i = 0; $i < $schedule_len; $i++ ) {
				echo("step 4<br/>");
				echo "mulai baru<br/>";
				$params_fare_multi = "";
				$temp_route_param = "";
				$temp_class_param = "";
				$temp_value_param = "";
				
				$j = 0;

				for($j = 0; $j < $flight_num; $j++){
					echo("step 5<br/>");
					$flight_search_id = $flight_id_multi[$j];					

					$each_schedule = $depart_schedule[$i + $j];
					$flight = $each_schedule['flight'];
					$size_of_flightdata = sizeof($each_schedule);
					
					if (preg_replace('/[^\da-z]/i', '', $flight) != preg_replace('/[^\da-z]/i', '', $flight_search_id)) {
						echo "gak sama " . $flight ."!= " .$flight_search_id . "<br/>";
						break;
					}
					
					$pertamax = true;
					// nyari kursi tersedia
					for($iter = $size_of_flightdata;  $iter >= 0; $iter--){
						echo("step 6<br/>");
						
						if (!array_key_exists((string)$iter, $each_schedule))
						 	continue;

						$seat = $each_schedule[(string)$iter]['seat'];

						if ($seat == NULL || $seat == "0") {
							echo "kursi kosong <br/>";
							continue;
						}

						// ambil param kursi tersedia
						echo("step 7<br/>");
						// var_dump($each_schedule[(string)$iter]);

						$value = $each_schedule[(string)$iter]['value']; 
						$class_code = $each_schedule[(string)$iter]['class'];
						$time_depart_att = $each_schedule['time_depart'];
						$time_arrive_att = $each_schedule['time_arrive'];
						$longdate_att = $each_schedule['longdate'];
						$route_att = $each_schedule['route'];
						$flight_id_att = $each_schedule['flight'];
						
						if ($pertamax ) {
							$params_fare_multi = "id=3000"."&choice=".$this->atrisUrlEncode($value)."&date=".$longdate_att . "&from_date=". $this->atrisUrlEncode($time_depart_att) ."&to_date=". $this->atrisUrlEncode($time_arrive_att) ."&from_code=".$origin."&to_code=".$destination."&adult=". $adult_passenger_num ."&child=". $child_passenger_num ."&infant=". $infant_passenger_num ."&row=3000"."&class_code=". $this->atrisUrlEncode($class_code)."&chkbox=3000"."&seq=".$iter."&defcurr=IDR&info=" . $flight_num . "~";

							$pertamax = false;
						}						

						$temp_route_param = $temp_route_param . $this->atrisUrlEncode( "LionH2H|3000|"  . $flight_id_att . "|" . $route_att . "|1|" . $time_depart_att . "|" . $time_arrive_att ) . "~" ;
						$temp_class_param = $temp_class_param . $class_code ."~" ;
						$temp_value_param = $temp_value_param . $value . "~";
						echo("step 8". $each_schedule[(string)$iter]['class']."<br/>");
						// var_dump($temp_route_param);
						break;
						
					}
										
				}
				
				echo("step 9 ". $j. " ". $flight_num ."<br/>");
				if ($j >= $flight_num-1) {
					echo("step 10<br/>");
					$params_fare_multi = $params_fare_multi. $temp_route_param . $temp_class_param . $temp_value_param . "0%7C0%7C0" ;
					// var_dump($params_fare_multi);

					if ($return_trip = TRUE){
						$response = $this->scraping->request('liongetfaremultiret.html', $this->url . '/api/bookingairlines/ajaxlionh2hfare', $params_fare_multi);
						return $response;
					} elseif ($return_trip = FALSE){
						$response = $this->scraping->request('liongetfaremulti.html', $this->url . '/api/bookingairlines/ajaxlionh2hfare', $params_fare_multi);
						return $response;
					}
				} 				
			}
		} return "Gak dapet harga. :(((";
	}

	function getBooking($data_penumpang, $email, $phone_number0, $value, $depart_date, $origin, $flight_id, $destination, $class_code, $publish, $tax, $total, $from_date, $to_date, $passenger_num, $route, $time_depart, $time_arrive, $return_param) {
		$depart_date = new DateTime($depart_date);
		$monthFirst = $depart_date->format('m-d-Y');
		$dayFirst = $depart_date->format('d-m-Y');
		$yearFirst = $depart_date->format('Y-m-d');
		$epochdate = $depart_date->format('U');
		$flight_id = str_replace('/\s+/', '  ', $flight_id);
		$passenger_num = ((int)$_POST['adult_passenger_num'] + (int)$_POST['child_passenger_num'] + (int)$_POST['infant_passenger_num']);

		if($return_param != NULL){
			//echo "288";
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

			elseif(substr($flight_id, 0, 2) == "JT" || substr($flight_id, 0, 2) == "ID" || substr($flight_id, 0, 2) == "OW" || substr($flight_id, 0, 2) == "IW") {
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
			// }
	
			
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
			} else{
				$date_arranged_ret = $time_depart_ret . "|" . $time_arrive_ret;
			}

			$chkbox_ret = "&check_box". $flightnumber_ret ."=" . $this->atrisUrlEncode("1~". $flight_code ."|$flightnumber_ret|". $flight_id_ret . "|" . $route_ret ."|1|" . $date_arranged_ret .  "~" . $class_code_ret . "~" . $value_ret. "~" . $publish_ret . "|" . $tax_ret .  "|" . $total_ret . "|IDR|IDR");

		} else {
			$route_rt = "";
			$return_code = "";
			$to_date = $epochdate;
			$chkbox_ret = "";
		};

		$postBook = "route=" . $flightnumber_dep .$route_rt."&from_code=".$origin."&to_code=". $destination ."&return_code=". $return_code ."&from_date=".$dayFirst."&to_date=". $to_date."&count_passenger=". $passenger_num ."&adult=".(int)$_POST['adult_passenger_num']."&child=". (int)$_POST['child_passenger_num'] ."&infant=". (int)$_POST['infant_passenger_num'];
		
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
		// echo "split";
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

		$response = $this->scraping->request('citilinkbooking.html', 'https://atris.versatiket.co.id/api/bookingairlines/booking', $postBook);
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
		// var_dump($post_status);
		if(substr($schedule_flight_id, 0, 2) == "QG"){
			$this->logout();
			$login_rits = $this->scraping->request('login_rits.html', $urlrits. '/admin', 'user=' . $this->username . '&password=' . $this->password . '&commit=Login' );
			$content = $this->scraping->request('printissued.html', $urlrits. '/ticketingairlines/print/h2h_id/36/booking_id/' . $booking_id, $post_status);
			$logout_rits = $this->scraping->request('logout_rits.html',$urlrits. '/admin/logout', "");
			header("Location: printissued.html");
		} 
		elseif(substr($schedule_flight_id, 0, 2) == "JT" || substr($schedule_flight_id, 0, 2) == "ID" || substr($schedule_flight_id, 0, 2) == "IW"){
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
			// var_dump($this->password);
			return $ajaxticketing;
		} else { 
			return json_encode("{code:'error', 'message':'udah logout'}");
		} 	
		
	}

	function isCaptchaResponse( $response ) {
		if ( array_key_exists('content', $response) && array_key_exists('captcha_string', $response['content']) ) {
			$captcha_string = $response['content']['captcha_string'];
			$captcha_string = str_replace('data:image/jpg;base64,', '', $captcha_string );
			$captcha_string = str_replace('\/', '/', $captcha_string );
			// var_dump($captcha_string);
			$captcha_img = base64_decode($captcha_string);
			$file = 'lionh2hcaptcha' . uniqid() . '.jpeg';
			$success = file_put_contents($file, $captcha_img);
			return $file;
		}
		return false;
	}
}
?>
