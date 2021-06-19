<?php
	class Texting extends CI_Controller{
		private $apiCode  = 'ST-GIOVA302694_1SM1F';
		private $password = '){fqk2]vhh';
		private $textMessages = array(
			"0" => "Success! Message is now on queue and will be sent soon.",
			"1" => "Invalid Number.",
			"2" => "Number prefix not supported. Please contact us so we can add.",
			"3" => "Invalid ApiCode.",
			"4" => "Maximum Message per day reached. This will be reset every 12MN.",
			"5" => "Maximum allowed characters for message reached.",
			"6" => "System OFFLINE.",
			"7" => "Expired ApiCode.",
			"8" => "iTexMo Error. Please try again later.",
			"9" => "Invalid Function Parameters.",
			"10" => "Recipient's number is blocked due to FLOODING, message was ignored.",
			"11" => "Recipient's number is blocked temporarily due to HARD sending (after 3 retries of sending and message still failed to send) and the message was ignored. Try again after an hour.",
			"12" => "Invalid request. You can't set message priorities on non corporate apicodes.",
			"13" => "Invalid or Not Registered Custom Sender ID.",
			"14" => "Invalid preferred server number.",
			"15" => "IP Filtering enabled - Invalid IP.",
			"16" => "Authentication error. Contact support at support@itexmo.com",
			"17" => "Telco Error. Contact Support support@itexmo.com",
			"18" => "Message Filtering Enabled. Contact Support support@itexmo.com",
			"19" => "Account suspended. Contact Support support@itexmo.com",
		);
		public function getItextMoReport(){
			try{
				$res = "https://www.itexmo.com/php_api/apicode_info.php?apicode={$this->apiCode}";
				$result = $this->httpRequest("get",$res);

				$this->res = array(
					'isError'   => false,
					'message'   => "Success",
					'date'      => date("Y-m-d"),
					'data' 		=>  $result,  
				);

			}catch(Exception $e) {
				$this->res = array(
					'isError' => true,
					'message'   => $e->getMessage(),
					'date'    => date("Y-m-d"),  
				);
			}
			$this->displayJSON($this->res);
		}
		function send(){
			if(empty($_POST['number'])){
				$this->res = array(
					'isError' => true,
					'message'   =>  "Please indicate a number",
					'number'   =>  0,
					'date'    => date("Y-m-d"),  
				);
			}
			else if(empty($_POST['message'])){
				$this->res = array(
					'isError' => true,
					'message'   => "Please indicate a message",
					'number'   =>  $_POST['number'],
					'date'    => date("Y-m-d"),  
				);
			}
			else{
				try{

					$m = $_POST['message'];
					$number = $_POST['number'];
	
					$textMessage = $this->messageRender($m);
				
					$ch = curl_init();
					$itexmo = array('1' => $number, '2' => $textMessage, '3' => $this->apiCode, 'passwd' => $this->password);
		
					curl_setopt($ch, CURLOPT_URL,"https://www.itexmo.com/php_api/api.php");
					curl_setopt($ch, CURLOPT_POST, 1);
					curl_setopt($ch, CURLOPT_POSTFIELDS, 
							http_build_query($itexmo));
					curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
					$res =  curl_exec ($ch);
					curl_close ($ch);
				
					if($res == 0){
						$this->res = array(
							'isError' => false,
							'message'   =>  $this->textMessages[$res],
							'number'   =>  $number,
							'date'    => date("Y-m-d"),  
						);
					}else{
						$this->res = array(
							'isError' => true,
							'message'   =>  $this->textMessages[$res],
							'number'   =>  $number,
							'date'    => date("Y-m-d"),  
						);
					}
				}catch(Exception $e) {
					$this->res = array(
						'isError' => true,
						'number'   =>  $number,
						'message'   => $e->getMessage(),
						'date'    => date("Y-m-d"),  
					);
				}
			}
			

            $this->displayJSON($this->res);
			
		}
		private function httpRequest($request,$url){
			if($request == "get"){

				$connection = curl_init();

				curl_setopt($connection, CURLOPT_URL, $url);
				curl_setopt($connection, CURLOPT_RETURNTRANSFER, true);

				$data = curl_exec($connection);
				curl_close($connection);
				$array = json_decode($data, true);
				return $array;
				
			}else{

			}

		}
		//display json format for response
        private function displayJSON($data){
            if(isset($_SERVER['HTTP_USER_AGENT']) && strstr($_SERVER['HTTP_USER_AGENT'],"MSIE")){
                header('Content-Type: application/json');
            }
            else{
                header('Content-Type: application/json');
                header('Access-Control-Allow-Methods: GET, POST');
                header('Access-Control-Allow-Origin: *');
                header("Cache-Control: no-cache");
                header("Pragma: no-cache");
                echo json_encode($data);
            }
           
        }
		function sendNotification(){
			
			try{

				$m = $_POST['message'];
				$number = $_POST['number'];

				$textMessage = $this->notificationMessage();
			
				$ch = curl_init();
				$itexmo = array('1' => $number, '2' => $textMessage, '3' => $this->apiCode, 'passwd' => $this->password);
	
				curl_setopt($ch, CURLOPT_URL,"https://www.itexmo.com/php_api/api.php");
				curl_setopt($ch, CURLOPT_POST, 1);
				curl_setopt($ch, CURLOPT_POSTFIELDS, 
						http_build_query($itexmo));
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
				$res =  curl_exec ($ch);
				curl_close ($ch);
			
				if($res == 0){
					$this->res = array(
						'isError' => false,
						'message'   =>  $this->textMessages[$res],
						'number'   =>  $number,
						'date'    => date("Y-m-d"),  
					);
				}else{
					$this->res = array(
						'isError' => true,
						'message'   =>  $this->textMessages[$res],
						'number'   =>  $number,
						'date'    => date("Y-m-d"),  
					);
				}
			}catch(Exception $e) {
				$this->res = array(
					'isError' => true,
					'number'   =>  $number,
					'message'   => $e->getMessage(),
					'date'    => date("Y-m-d"),  
				);
			}
			

            $this->displayJSON($this->res);
			
		}
		private function notificationMessage(){
			return '
To all our valued clients:

  Please be informed that on:
1. Friday (JUNE 11, 2021) Office hours will be until 11:30AM. We will have a company activity.
2. Saturday (JUNE 12, 2021 - INDEPENDENCE DAY, a national public holiday) our office will be closed.
Next working day will be on Tuesday (JUNE 15, 2021)
BARILI PRIME LENDING CORP.';
		}
		private function messageRender($message){
			// return "Hi Mr/Ms {$name} This message is to notify you about the application of your loan ({$loan_product})";
return "Good Day! 

{$message}

Please do not reply to this message 
Contact us @
Mobile:09953931136
Tel:4709651
Email:bariliprime.lending2010@gmail.com";
		}
	}
?>
