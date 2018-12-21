<?php
    class Scraping {  
        public $session;
        public $verbose;

        function __construct($session_id = null) {
    
            if (is_null($session_id)) 
                $this->session_id = date("Y-m-d-H-i-s") . uniqid();		
            else
                $this->session_id = $session_id;
    
            $this->session_file = dirname(__FILE__) ."/" . $this->session_id ."cookies.txt";
            $this->session = curl_init();
            
            curl_setopt($this->session, CURLOPT_FOLLOWLOCATION, true);
            curl_setopt($this->session, CURLOPT_COOKIEFILE, $this->session_file);
            curl_setopt($this->session, CURLOPT_COOKIEJAR, $this->session_file);
            curl_setopt($this->session, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($this->session, CURLOPT_CONNECTTIMEOUT, 0);
            curl_setopt($this->session, CURLOPT_TIMEOUT, 400);
            curl_setopt($this->session, CURLINFO_HEADER_OUT, false);
            curl_setopt($this->session, CURLOPT_VERBOSE, true);
            curl_setopt($this->session, CURLOPT_STDERR, $this->verbose = fopen('php://temp', 'rw+') );
            curl_setopt($this->session, CURLOPT_FILETIME, true);
            // echo "session file " . $this->session_file ."<br/>";
        }

        function request($pagename, $url, $post_var){

            $fopen = fopen ($pagename, 'w+');
            
            curl_setopt($this->session, CURLOPT_URL, $url);
            
            if($post_var != FALSE){
                curl_setopt($this->session, CURLOPT_POSTFIELDS, $post_var);
            }

            set_time_limit(120);
                        
            $data = curl_exec($this->session);
            $data_json = json_decode($data, true);
            //$headers = curl_getinfo($this->session, CURLINFO_HEADER_OUT);
            //print_r( $headers );
            // echo "Verbose information:\n", !rewind($this->verbose), stream_get_contents($this->verbose), "<br/> <br/>";
            //curl_close($data);
            fwrite($fopen, $data);
            return $data_json;
            
        }

        function saveSession() {
            if (gettype($this->session) == 'resource') curl_close($this->session);
		    else var_dump($this->session);

		    return $this->session_id;
        }
    }
?>