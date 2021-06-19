<?php
	class cron extends CI_Controller{
		private $apiCode  = 'ST-GIOVA302694_1SM1F';
		private $password = '){fqk2]vhh';
		private $number = ["09166065386","09267308873"];
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
		public function __construct() {
			ini_set('max_execution_time', 300); //300 seconds = 5 minutes
			parent::__construct();
            // $this->load->library("Phpmailerlib");
            $this->load->library('phpmailer_library',NULL,'phpmailer_library');
            $this->load->library('Email_library',NULL,'email_lib');
        }
		private function getBank(){

			$sql    = "SELECT
						bank_account.bank_name,
						bank_account.account_number,
						(
						SELECT
							SUM(bank_transaction.amount)
						FROM
							bank_transaction
						WHERE
							bank_transaction.is_void = 0 AND bank_transaction.bank_transaction_type_id = 1 AND bank_transaction.bank_account_id = bank_account.bank_account_id
					) as totalin,
					(
						SELECT
							SUM(bank_transaction.amount)
						FROM
							bank_transaction
						WHERE
						bank_transaction.is_void = 0 AND bank_transaction.bank_transaction_type_id = 2 AND  bank_transaction.bank_account_id = bank_account.bank_account_id
					) as totalout
					FROM bank_account
					WHERE bank_account.is_active = 1";
			
			return $this->db->query($sql)->result();
		}
		public function sendReport(){

			$activeLoan 	 = $this->getTotalAddedActiveLoan(date("Y-m-d"));
			$pendingLoan 	 = $this->getTotalPendingLoan();
			$pendingCapital  = $this->getTotalPendingCapital();
			$activeCapital 	 = $this->getTotalAddedCapital(date("Y-m-d"));
			$activeBorrowers = $this->getTotalActiveBorrowers();
			$pendingSchedule = $this->getPendingSchedule();
			$cRequest 		 = $this->getCashiersRequest();
			$bank 		 = $this->getBank();
			$itextMoReport 		 = $this->getItextMoReport();

$message = "
Total Borrowers: {$activeBorrowers}
Added Loan: {$activeLoan}
Pending Loans: {$pendingLoan}
Added Capital: {$activeCapital}
Pending Capital: {$pendingCapital}
Casheirs Request: {$cRequest}
BANK: \r\n";

foreach ($bank as $key => $value) {
$diff = $value->totalin - $value->totalout;
$total = number_format($diff,2,".",",");
$message .= 
"($value->bank_name): $total \r\n";
}

$message .= "Message Left: {$itextMoReport}";


			for ($i = 0; $i < sizeOf($this->number); $i++) { 
				$num = $this->number[$i];
				$this->send($message,$num);
			}

			
			// $body = $this->email_lib->htmlDraft($message,"System Notification");
			// $this->email_lib->send($body,$value->email,"System Notification");
			
		}
		public function sendReportIT(){

			$activeLoan 	 = $this->getTotalAddedActiveLoan(date("Y-m-d"));
			$pendingLoan 	 = $this->getTotalPendingLoan();
			$pendingCapital  = $this->getTotalPendingCapital();
			$activeCapital 	 = $this->getTotalAddedCapital(date("Y-m-d"));
			$activeBorrowers = $this->getTotalActiveBorrowers();
			$pendingSchedule = $this->getPendingSchedule();
			$cRequest 		 = $this->getCashiersRequest();
			$itextMoReport 		 = $this->getItextMoReport();
			$bank 		 = $this->getBank();
$message = "
Total Borrowers: {$activeBorrowers}
Added Loan: {$activeLoan}
Pending Loans: {$pendingLoan}
Added Capital: {$activeCapital}
Pending Capital: {$pendingCapital}
Casheirs Request: {$cRequest}
BANK: \r\n";

foreach ($bank as $key => $value) {
$diff = $value->totalin - $value->totalout;
$total = number_format($diff,2,".",",");
$message .= 
"($value->bank_name): $total \r\n";
}

$message .= "Message Left: {$itextMoReport}";


			$this->send($message,'09356302694');

			// $body = $this->email_lib->htmlDraft($message,"System Notification");
			// $this->email_lib->send($body,$value->email,"System Notification");
			
		}
		private function getCashiersRequest(){
            try{
                $sql = "SELECT count(cashier_vault_request.cashier_vault_request_id) as 'count' 
						FROM cashier_vault_request 
						WHERE cashier_vault_request.is_done = 0 
						AND cashier_vault_request.is_active = 1";
                return $this->db->query($sql)->result()[0]->count;
            }catch(Exception $e) {
                return 0;
            }
        }
		private function getPendingSchedule(){
            try{
                $sql = "SELECT COUNT(schedules.id) as 'count' 
						FROM schedules 
						WHERE schedules.status_id = 3 AND schedules.is_active = 1";
                return $this->db->query($sql)->result()[0]->count;
            }catch(Exception $e) {
                return 0;
            }
        }
		private function getTotalActiveBorrowers(){
            try{
                $sql = "SELECT count(borrower.borrower_id) as 'count' 
						FROM borrower
						WHERE borrower.is_active = 1";
                return $this->db->query($sql)->result()[0]->count;
            }catch(Exception $e) {
                return 0;
            }
        }
        private function getTotalAddedActiveLoan($date = ""){
            try{
                $sql = "SELECT count(loan.loan_id) as 'count' FROM loan WHERE loan.is_released = 1 AND loan.status_id = 1 AND loan.is_active = 1";
                if(!empty($date)){
					$sql .= " AND DATE_FORMAT(loan.date_added,'%Y-%m-%d') = '{$date}'";
				}
				return $this->db->query($sql)->result()[0]->count;
            }catch(Exception $e) {
                return 0;
            }
        }
        private function getTotalPendingLoan(){
            try{
                $sql = "SELECT count(loan.loan_id) as 'count' FROM loan WHERE loan.is_released = 0 AND loan.status_id = 2 AND loan.is_active = 1";
                return $this->db->query($sql)->result()[0]->count;
            }catch(Exception $e) {
                return 0;
            }
        }
        private function getTotalPendingCapital(){
            try{
                $sql = "SELECT count(loan_add_capital.loan_add_capital_id) as 'count' FROM loan_add_capital LEFT JOIN loan ON loan.loan_id = loan_add_capital.loan_id WHERE loan.is_released = 0 AND loan.status_id = 2 AND loan.is_active = 1 AND loan_add_capital.is_released = 0 AND loan_add_capital.status_id = 2";
				return $this->db->query($sql)->result()[0]->count;
            }catch(Exception $e) {
                return 0;
            }
        } 
        private function getTotalAddedCapital($date = ""){
            try{
                $sql = "SELECT count(loan_add_capital.loan_add_capital_id) as 'count' FROM loan_add_capital LEFT JOIN loan ON loan.loan_id = loan_add_capital.loan_id WHERE loan.is_released = 1 AND loan.status_id = 1 AND loan.is_active = 1 AND loan_add_capital.is_released = 1 AND loan_add_capital.status_id = 1";
				if(!empty($date)){
					$sql .= " AND DATE_FORMAT(loan_add_capital.transact_date,'%Y-%m-%d') = '{$date}'";
				}
				return $this->db->query($sql)->result()[0]->count;
            }catch(Exception $e) {
                return 0;
            }
        }
		private function send($message,$number){

			try{

				$m = $message;

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
						'date'    => date("Y-m-d"), 
						"messageData" => $textMessage 
					);
				}else{
					$this->res = array(
						'isError' => true,
						'message'   =>  $this->textMessages[$res],
						'date'    => date("Y-m-d"),  
						"messageData" => $textMessage
					);
				}
			}catch(Exception $e) {
				$this->res = array(
					'isError' => true,
					'message'   => $e->getMessage(),
					'date'    => date("Y-m-d"),  
				);
			}
            $this->displayJSON($this->res);
		}
		private function sendNotif($message,$number){

			try{

				$m = $message;

				$textMessage = $this->messageNotifRender($m);
			
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
						'date'    => date("Y-m-d"), 
						"messageData" => $textMessage 
					);
				}else{
					$this->res = array(
						'isError' => true,
						'message'   =>  $this->textMessages[$res],
						'date'    => date("Y-m-d"),  
						"messageData" => $textMessage
					);
				}
			}catch(Exception $e) {
				$this->res = array(
					'isError' => true,
					'message'   => $e->getMessage(),
					'date'    => date("Y-m-d"),  
				);
			}
            $this->displayJSON($this->res);
		}
		public function getItextMoReport(){
			$res = "https://www.itexmo.com/php_api/apicode_info.php?apicode={$this->apiCode}";
			$result = $this->httpRequest("get",$res);

			$messageLeft 		  = !empty($result['Result ']['MessagesLeft']) ? $result['Result ']['MessagesLeft'] : 0;

			return $messageLeft;
		}
		public function sendNotificationInsurance(){

			$sql = "SELECT * FROM user WHERE user.user_id IN(1,19,27)";
			$user_data = $this->db->query($sql)->result();

			$date = date("Y-m-d");

			foreach ($user_data as $key => $value) {

				$data = array(
					'title' 	=> "Insurance Notification for the date of ".date("Y-m-16"),
					'user_id' 	=> $value->user_id,
					'link' 		=> "https://bariliprime.doitcebu.com/admin/borrower_to_insurance",
				);

				$this->db->insert("notification",$data);
				$message = "Hi ".$value->firstname.' '.$value->lastname." just be reminded that the insurance of BPLC clients will due on ".date("Y-m-16");
				
				if($value->mobile_number != ""){
					$this->sendNotif($message,$value->mobile_number);
				}
				
				if($value->email != ""){
					$body = $this->email_lib->htmlDraft($message,"System Notification");
					$this->email_lib->send($body,$value->email,"System Notification");
				}
			}

		
			$message = "Hi just be reminded that the insurance of BPLC clients will due on ".date("Y-m-16");
			$this->sendNotif($message,'09530466577');

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
		private function messageRender($message){
			// return "Hi Mr/Ms {$name} This message is to notify you about the application of your loan ({$loan_product})";
return "Daily Report

{$message}

FROM: BP Lending Corp.";
		}
		private function messageNotifRender($message){
			// return "Hi Mr/Ms {$name} This message is to notify you about the application of your loan ({$loan_product})";
return "System Notification

{$message}

FROM: BP Lending Corp.";
		}
	}
?>
