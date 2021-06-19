<?php
	ini_set('memory_limit', '-1');
	date_default_timezone_set('Asia/Manila');
	use Dompdf\Dompdf;
	use Dompdf\Options;
    class utils Extends CI_Controller{
        /* Global Variables */
        private $res = array();
		private $number = ["09166065386","09267308873"];
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
        /**
            * Class constructor.
            *
        */
        public function __construct() {
			parent::__construct();
			$this->load->library('Pdf',NULL,'pdf');
            date_default_timezone_set('Asia/Manila');
		}
		public function upload_receipt(){

			
			if(empty($_POST['title'])){
				$this->res = array(
					'isError'   => true,
					'message'   => "Empty Title",
					'date'      => date("Y-m-d"),  
				);
			}
			else if(empty($_POST['receipt_category_id'])){
				$this->res = array(
					'isError'   => true,
					'message'   => "Empty category",
					'date'      => date("Y-m-d"),  
				);
			}
			else if(empty($_POST['description'])){
				$this->res = array(
					'isError'   => true,
					'message'   => "Empty description",
					'date'      => date("Y-m-d"),  
				);
			}else{
				try{
					$receipt_data = array(
						'title' 			  => $_POST['title'],
						'receipt_category_id' => $_POST['receipt_category_id'],
						'description' 		  => $_POST['description'],
						'date_value' 		  => $_POST['date'],
					);
					$data = $this->db->insert("receipt_logs",$receipt_data);
					$last_id = $this->db->insert_id();
					if($data){

						$arrayTransQuery = array();

						foreach ($_POST['images'] as $key => $value) {

							$cardId 		= $value['cardId'];
							$receiptName 	= $value['receiptName'];
							$cardBase64 	= $value['cardBase64'];
							$description 	= $value['description'];

							$url_receipt = array(
								'receipt_logs_id' => $last_id,
								'title' 		  => $receiptName,
								'description' 	  => $description,
								'url' 			  => $cardBase64,
							);

							$arr_data =$this->db->set($url_receipt)->get_compiled_insert('receipt_galery');
							array_push($arrayTransQuery,$arr_data);

						}

						$result = array_filter($arrayTransQuery);
						$res = $this->mysqlTQ($result);

						if($res){
							$this->res = array(
								'isError'   => false,
								'message'   => "Successfuly added receipt data",
								'date'      => date("Y-m-d"),  
							);
						}else{
							$this->res = array(
								'isError'   => true,
								'message'   => "Error adding receipt",
								'date'      => date("Y-m-d"),  
							);
						}

						
					}
				}catch(Exception $e) {
					$this->res = array(
						'isError'   => true,
						'message'   => $e,
						'date'      => date("Y-m-d"),  
					);
				}
			}

			
			$this->displayJSON($this->res);
		}
		#mysql using Trasaction Query
		public function mysqlTQ($arrQuery) {
			// print_r($arrQuery);exit;
			
			if (!empty($arrQuery)) {
				$this->db->trans_start();
				foreach($arrQuery as $value) {
					$this->db->query($value);
				}
				if ($this->db->trans_status() === FALSE){
					$this->db->trans_rollback();
				} else {
					$this->db->trans_commit();
					return true;
				}
			}
			
		}
		public function generateQR($id){
			// Text content of the QRCode
			$data = "http://bariliprime-api-v1.doitcebu.com/utils/officeData/{$id}";
			// QRCode size
			$size = '500x500';
			// Path to image (web or local)
			$logo = 'https://bariliprime.doitcebu.com/assets/img/meeow-logo-white.png';
			// $logo = 'https://scontent.fceb1-2.fna.fbcdn.net/v/t1.0-1/p200x200/151177198_822365665024980_4220242842332620525_o.jpg?_nc_cat=100&ccb=3&_nc_sid=7206a8&_nc_eui2=AeHaF98Oxup2Vwp7wq5QKN3WtRpI4CF7iYi1GkjgIXuJiLFd4Pr9PDPNWcilaiR065c2GTLeJX6W_rZL_Refkn88&_nc_ohc=V_HLrtQYH4EAX9w1M64&_nc_ht=scontent.fceb1-2.fna&tp=6&oh=c5a1a9057666381190fb036bc0bb4fb3&oe=6051D6A8';
			// Get QR Code image from Google Chart API
			http://code.google.com/apis/chart/infographics/docs/qr_codes.html
			$QR = imagecreatefrompng('https://chart.googleapis.com/chart?cht=qr&chld=H|1&chs='.$size.'&chl='.urlencode($data));

			// START TO DRAW THE IMAGE ON THE QR CODE
			$logo = imagecreatefromstring(file_get_contents($logo));

			$QR_width = imagesx($QR);
			$QR_height = imagesy($QR);

			$logo_width = imagesx($logo);
			$logo_height = imagesy($logo);

			// Scale logo to fit in the QR Code
			$logo_qr_width = $QR_width/3;
			$scale = $logo_width/$logo_qr_width;
			$logo_qr_height = $logo_height/$scale;

			imagecopyresampled($QR, $logo, $QR_width/3, $QR_height/3, 0, 0, $logo_qr_width, $logo_qr_height, $logo_width, $logo_height);

			// END OF DRAW

			/**
			 * As this example is a plain PHP example, return
			 * an image response.
			 *
			 * Note: you can save the image if you want.
			 */
			header('Content-type: image/png');
			imagepng($QR);
			imagedestroy($QR);

			// If you decide to save the image somewhere remove the header and use instead :
			// $savePath = "/path/to-my-server-images/myqrcodewithlogo.png";
			// imagepng($QR, $savePath);
		}
		public function generatePayslip($id){
			// Text content of the QRCode
			$data = "http://bariliprime-api-v1.doitcebu.com/utils/genPayslip/{$id}";
			// QRCode size
			$size = '500x500';
			// Path to image (web or local)
			$logo = 'https://bariliprime.doitcebu.com/assets/img/meeow-logo-white.png';
			// $logo = 'https://scontent.fceb1-2.fna.fbcdn.net/v/t1.0-1/p200x200/151177198_822365665024980_4220242842332620525_o.jpg?_nc_cat=100&ccb=3&_nc_sid=7206a8&_nc_eui2=AeHaF98Oxup2Vwp7wq5QKN3WtRpI4CF7iYi1GkjgIXuJiLFd4Pr9PDPNWcilaiR065c2GTLeJX6W_rZL_Refkn88&_nc_ohc=V_HLrtQYH4EAX9w1M64&_nc_ht=scontent.fceb1-2.fna&tp=6&oh=c5a1a9057666381190fb036bc0bb4fb3&oe=6051D6A8';
			// Get QR Code image from Google Chart API
			http://code.google.com/apis/chart/infographics/docs/qr_codes.html
			$QR = imagecreatefrompng('https://chart.googleapis.com/chart?cht=qr&chld=H|1&chs='.$size.'&chl='.urlencode($data));

			// START TO DRAW THE IMAGE ON THE QR CODE
			$logo = imagecreatefromstring(file_get_contents($logo));

			$QR_width = imagesx($QR);
			$QR_height = imagesy($QR);

			$logo_width = imagesx($logo);
			$logo_height = imagesy($logo);

			// Scale logo to fit in the QR Code
			$logo_qr_width = $QR_width/3;
			$scale = $logo_width/$logo_qr_width;
			$logo_qr_height = $logo_height/$scale;

			imagecopyresampled($QR, $logo, $QR_width/3, $QR_height/3, 0, 0, $logo_qr_width, $logo_qr_height, $logo_width, $logo_height);

			// END OF DRAW

			/**
			 * As this example is a plain PHP example, return
			 * an image response.
			 *
			 * Note: you can save the image if you want.
			 */
			header('Content-type: image/png');
			imagepng($QR);
			imagedestroy($QR);

			// If you decide to save the image somewhere remove the header and use instead :
			// $savePath = "/path/to-my-server-images/myqrcodewithlogo.png";
			// imagepng($QR, $savePath);
		}
		public function updateSupplies(){

			if(empty($_POST['office_supplies_logs_id'])){
				$this->res = array(
					'isError'   => true,
					'message'   => "Empty office_supplies_logs_id",
					'date'      => date("Y-m-d"),  
				);
			}
			else if(empty($_POST['user'])){
				$this->res = array(
					'isError'   => true,
					'message'   => "Empty user",
					'date'      => date("Y-m-d"),  
				);
			}
			else if(empty($_POST['data'])){
				$this->res = array(
					'isError'   => true,
					'message'   => "Empty datas",
					'date'      => date("Y-m-d"),  
				);
			}
			else if(empty($_POST['description'])){
				$this->res = array(
					'isError'   => true,
					'message'   => "Empty description",
					'date'      => date("Y-m-d"),  
				);
			}
			else if(empty($_POST['signature'])){
				$this->res = array(
					'isError'   => true,
					'message'   => "Empty signature",
					'date'      => date("Y-m-d"),  
				);
			}
			else{
				try{

					$office_supplies_logs_id = $_POST['office_supplies_logs_id'];
					$user = $_POST['user'];
					$data = $_POST['data'];
					$description = $_POST['description'];
					$signature = $_POST['signature'];
					$array = array(
						'name'=> $user,
						'data'=> $data,
						'description'=> $description,
						'signature'=> $signature,
					);
					$this->db->where("office_supplies_logs_id",$office_supplies_logs_id);
					$update = $this->db->update("office_supplies_logs",$array);

					if($update){
						$this->res = array(
							'isError'   => false,
							'message'   => "Success",
							'date'      => date("Y-m-d"),  
						);
					}else{
						$this->res = array(
							'isError'   => true,
							'message'   => "Error",
							'date'      => date("Y-m-d"),  
						);
					}
				
				}catch(Exception $e) {
					$this->res = array(
						'isError'   => true,
						'message'   => $e,
						'date'      => date("Y-m-d"),  
					);
					
				}
			}

			$this->displayJSON($this->res);
		}
		public function addSupplies(){

			if(empty($_POST['user'])){
				$this->res = array(
					'isError'   => true,
					'message'   => "Empty user",
					'date'      => date("Y-m-d"),  
				);
			}
			else if(empty($_POST['data'])){
				$this->res = array(
					'isError'   => true,
					'message'   => "Empty datas",
					'date'      => date("Y-m-d"),  
				);
			}
			else if(empty($_POST['description'])){
				$this->res = array(
					'isError'   => true,
					'message'   => "Empty description",
					'date'      => date("Y-m-d"),  
				);
			}
			else if(empty($_POST['signature'])){
				$this->res = array(
					'isError'   => true,
					'message'   => "Empty Signature",
					'date'      => date("Y-m-d"),  
				);
			}
			else{
				try{

		
					$user = $_POST['user'];
					$data = $_POST['data'];
					$description = $_POST['description'];
					$signature = $_POST['signature'];

					$array = array(
						'name'=> $user,
						'data'=> $data,
						'description'=> $description,
						'signature'=> $signature,
					);

					$update = $this->db->insert("office_supplies_logs",$array);

					if($update){
						$this->res = array(
							'isError'   => false,
							'message'   => "Success",
							'date'      => date("Y-m-d"),  
						);
					}else{
						$this->res = array(
							'isError'   => true,
							'message'   => "Error",
							'date'      => date("Y-m-d"),  
						);
					}
				
				}catch(Exception $e) {
					$this->res = array(
						'isError'   => true,
						'message'   => $e,
						'date'      => date("Y-m-d"),  
					);
					
				}
			}

			$this->displayJSON($this->res);
		}
		public function get_office_data(){
			try{
				$sql = "SELECT *
						FROM office_supplies_logs";
				$data = $this->db->query($sql)->result();
				$this->res = array(
                    'isError'   => false,
					'message'   => "Success",
					'data'		=> $data,
                    'date'      => date("Y-m-d"),  
				);
				$this->displayJSON($this->res);
			}catch(Exception $e) {
                $this->res = array(
                    'isError'   => true,
                    'message'   => $e,
                    'date'      => date("Y-m-d"),  
				);
				$this->displayJSON($this->res);
			}
			
			
		}
		public function get_settlement(){
			try{
				$sql = "SELECT settlement_logs.settlement_logs_id,
						settlement_logs.amount,
						settlement_logs.sales,
						settlement_logs.date_time as 'date_created',
						settlement_logs.date_value,
						settlement_logs.time,
						settlement_logs.description
						FROM settlement_logs	
						WHERE settlement_logs.is_active = 1";

				if(!empty($_POST['month'])){
					$m = $_POST['month'];
					$sql .=" AND DATE_FORMAT(settlement_logs.date_value,'%Y-%m') = '$m'";
				}

				$data = $this->db->query($sql)->result();
				$this->res = array(
                    'isError'   => false,
					'message'   => "Success",
					'data'		=> $data,
                    'date'      => date("Y-m-d"),  
				);
				$this->displayJSON($this->res);
			}catch(Exception $e) {
                $this->res = array(
                    'isError'   => true,
                    'message'   => $e,
                    'date'      => date("Y-m-d"),  
				);
				$this->displayJSON($this->res);
			}
			
			
		}
		public function viewSettlementLogs(){
			if(empty($_POST['settlement_logs_id'])){
				$this->res = array(
					'isError'   => true,
					'message'   => "Empty settlement_logs_id",
					'date'      => date("Y-m-d"),  
				);
			}else{
				try{

					$id = $_POST['settlement_logs_id'];

					$sql = "SELECT settlement_logs_report.settlement_logs_report_id ,
							settlement_logs_report.description,
							settlement_logs_report.date_time
							FROM settlement_logs_report	
							WHERE settlement_logs_report.settlement_logs_id = '$id'";
					$data = $this->db->query($sql)->result();
					$this->res = array(
						'isError'   => false,
						'message'   => "Success",
						'data'		=> $data,
						'date'      => date("Y-m-d"),  
					);
			

				}catch(Exception $e) {
					$this->res = array(
						'isError'   => true,
						'message'   => $e,
						'date'      => date("Y-m-d"),  
					);
					
				}
			}

			$this->displayJSON($this->res);
			
			
		}
		public function add_settlement(){

			if(empty($_POST['amount'])){
				$this->res = array(
					'isError'   => true,
					'message'   => "Empty amount",
					'date'      => date("Y-m-d"),  
				);
			}
			else if(empty($_POST['description'])){
				$this->res = array(
					'isError'   => true,
					'message'   => "Empty description",
					'date'      => date("Y-m-d"),  
				);
			}
			else if(empty($_POST['date_value'])){
				$this->res = array(
					'isError'   => true,
					'message'   => "Empty date_value",
					'date'      => date("Y-m-d"),  
				);
			}
			else if(empty($_POST['name'])){
				$this->res = array(
					'isError'   => true,
					'message'   => "Empty name",
					'date'      => date("Y-m-d"),  
				);
			}
			else if(empty($_POST['sales'])){
				$this->res = array(
					'isError'   => true,
					'message'   => "Empty sales",
					'date'      => date("Y-m-d"),  
				);
			}
			else if(empty($_POST['time'])){
				$this->res = array(
					'isError'   => true,
					'message'   => "Empty time",
					'date'      => date("Y-m-d"),  
				);
			}
			else{
				try{

		
					$amount = $_POST['amount'];
					$description = $_POST['description'];
					$name = $_POST['name'];
					$sales = $_POST['sales'];
					$time = $_POST['time'];
					$date_value = date("Y-m-d H:i:s",strtotime($_POST['date_value']));

					$array = array(
						'amount'=> $amount,
						'sales'=> $sales,
						'time'=> $time,
						'date_value'=> $date_value,
						'description'=> $description,
					);

					$res = $this->db->insert("settlement_logs",$array);
					$id = $this->db->insert_id();
					if($res){

						$this->addSettlementLogs($id,"Added new settlement log # ($id) for the amount of ( $amount ) and total sales ($sales) added by {$name}");
						$this->sendReport("Added new settlement log # ($id) for the amount of ( $amount ) and total sales ($sales) added by {$name}");

						$this->res = array(
							'isError'   => false,
							'message'   => "Success",
							'date'      => date("Y-m-d"),  
						);

					}else{
						$this->res = array(
							'isError'   => true,
							'message'   => "Error",
							'date'      => date("Y-m-d"),  
						);
					}
				
				}catch(Exception $e) {
					$this->res = array(
						'isError'   => true,
						'message'   => $e,
						'date'      => date("Y-m-d"),  
					);
					
				}
			}

			$this->displayJSON($this->res);
		}
		public function update_settlement(){

			if(empty($_POST['settlement_logs_id'])){
				$this->res = array(
					'isError'   => true,
					'message'   => "Empty Id",
					'date'      => date("Y-m-d"),  
				);
			}
			else if(empty($_POST['amount'])){
				$this->res = array(
					'isError'   => true,
					'message'   => "Empty amount",
					'date'      => date("Y-m-d"),  
				);
			}
			else if(empty($_POST['date_value'])){
				$this->res = array(
					'isError'   => true,
					'message'   => "Empty date_value",
					'date'      => date("Y-m-d"),  
				);
			}
			else if(empty($_POST['description'])){
				$this->res = array(
					'isError'   => true,
					'message'   => "Empty description",
					'date'      => date("Y-m-d"),  
				);
			}
			else if(empty($_POST['name'])){
				$this->res = array(
					'isError'   => true,
					'message'   => "Empty name",
					'date'      => date("Y-m-d"),  
				);
			}
			else if(empty($_POST['sales'])){
				$this->res = array(
					'isError'   => true,
					'message'   => "Empty sales",
					'date'      => date("Y-m-d"),  
				);
			}
			else if(empty($_POST['time'])){
				$this->res = array(
					'isError'   => true,
					'message'   => "Empty time",
					'date'      => date("Y-m-d"),  
				);
			}
			else{
				try{

		
					$settlement_logs_id = $_POST['settlement_logs_id'];
					$amount = $_POST['amount'];
					$description = $_POST['description'];
					$date_value = date("Y-m-d H:i:s",strtotime($_POST['date_value']));
					$name = $_POST['name'];
					$sales = $_POST['sales'];
					$time = $_POST['time'];

					$array = array(
						'amount'=> $amount,
						'description'=> $description,
						'date_value'=> $date_value,
						'time'=> $time,
						'sales'=> $sales,
					);

					$this->db->where("settlement_logs_id",$settlement_logs_id);
					$res = $this->db->update("settlement_logs",$array);


					if($res){

						$this->addSettlementLogs($settlement_logs_id,"Update settlement log # ($settlement_logs_id) and total sales ($sales) for the amount of ( $amount ) updated by {$name}");

						$this->res = array(
							'isError'   => false,
							'message'   => "Success",
							'date'      => date("Y-m-d"),  
						);

					}else{
						$this->res = array(
							'isError'   => true,
							'message'   => "Error",
							'date'      => date("Y-m-d"),  
						);
					}
				
				}catch(Exception $e) {
					$this->res = array(
						'isError'   => true,
						'message'   => $e,
						'date'      => date("Y-m-d"),  
					);
					
				}
			}

			$this->displayJSON($this->res);
		}
		public function void_settlement(){

			if(empty($_POST['settlement_logs_id'])){
				$this->res = array(
					'isError'   => true,
					'message'   => "Empty Id",
					'date'      => date("Y-m-d"),  
				);
			}
			else if(empty($_POST['name'])){
				$this->res = array(
					'isError'   => true,
					'message'   => "Empty name",
					'date'      => date("Y-m-d"),  
				);
			}
			else if(empty($_POST['void_description'])){
				$this->res = array(
					'isError'   => true,
					'message'   => "Empty void description",
					'date'      => date("Y-m-d"),  
				);
			}
			else{
				try{

		
					$settlement_logs_id = $_POST['settlement_logs_id'];
					$name 				= $_POST['name'];
					$void_description 	= $_POST['void_description'];

					$array = array(
						'void_description'=> $void_description,
						'void_date'=> date("Y-m-d H:i:s"),
						'is_active'=> 0,
					);

					$this->db->where("settlement_logs_id",$settlement_logs_id);
					$res = $this->db->update("settlement_logs",$array);


					if($res){

						$this->addSettlementLogs($settlement_logs_id,"Void settlement log # ($settlement_logs_id) voided by {$name}");

						$this->res = array(
							'isError'   => false,
							'message'   => "Success",
							'date'      => date("Y-m-d"),  
						);

					}else{
						$this->res = array(
							'isError'   => true,
							'message'   => "Error",
							'date'      => date("Y-m-d"),  
						);
					}
				
				}catch(Exception $e) {
					$this->res = array(
						'isError'   => true,
						'message'   => $e,
						'date'      => date("Y-m-d"),  
					);
					
				}
			}

			$this->displayJSON($this->res);
		}
		private function addSettlementLogs($id,$desc){
			$array = array(
				'settlement_logs_id' => $id,
				'description' => $desc,
			);
			$this->db->insert("settlement_logs_report",$array);
		}
		function gen_uuid() {
			$uuid =  sprintf( '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
				// 32 bits for "time_low"
				mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ),
		
				// 16 bits for "time_mid"
				mt_rand( 0, 0xffff ),
		
				// 16 bits for "time_hi_and_version",
				// four most significant bits holds version number 4
				mt_rand( 0, 0x0fff ) | 0x4000,
		
				// 16 bits, 8 bits for "clk_seq_hi_res",
				// 8 bits for "clk_seq_low",
				// two most significant bits holds zero and one for variant DCE1.1
				mt_rand( 0, 0x3fff ) | 0x8000,
		
				// 48 bits for "node"
				mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff )
			);
			
			echo $uuid;
		}
		public function genPayslip($id){
			ini_set('max_execution_time', 300); //300 seconds = 5 minutes
			$sql = "SELECT *,deduction.pension,deduction.sss,deduction.pagibig,deduction.philhealth,deduction.health,deduction.unpaid_leave,deduction.tax_dection,deduction.cash_advance,credits.overtime,credits.paid_leaves,
								credits.transport_allowance,credits.bonus,credits.medical_allowance,credits.other_allowance,CONCAT(user.lastname,', ',user.firstname) as name,
								user.basepay,user.id_number,user.image,payment_mode.name as mode, usertype.name as user_type,user.position
					FROM payroll
					LEFT JOIN deduction 	ON deduction.deduction_id 		= payroll.deduction_id
					LEFT JOIN credits 		ON credits.credits_id 			= payroll.credits_id
					LEFT JOIN user 			ON user.user_id 				= payroll.user_id
					LEFT JOIN usertype 		ON user.usertype_id 			= usertype.usertype_id
					LEFT JOIN payment_mode 	ON payment_mode.payment_mode_id = payroll.payment_mode_id
					WHERE payroll.payroll_id = '{$id}'";
			$data = $this->db->query($sql)->result();


			
			  $data['payroll'] 	= $data;
			//   print_r($data['payroll']);exit;
			$name = $data['payroll'][0]->name;
			$url = 'http://bariliprime-api-v1.doitcebu.com/utils/generatePayslip/'.$id;
			$data['qr'] = $url;
			$this->pdf->load_view4_portrait($name,'report/payslip',$data);

			// $this->renderPayslip($data);
		}
		public function officeData($id){
			try{
				$sql = "SELECT * FROM office_supplies_logs
						WHERE office_supplies_logs.office_supplies_logs_id = {$id}";
				$data = $this->db->query($sql)->result();
				$this->formatOfficeDataHtml($data);
			}catch(Exception $e) {
                $this->res = array(
                    'isError'   => false,
                    'message'   => "Success",
                    'date'      => date("Y-m-d"),  
				);
				$this->displayJSON($this->res);
			}
			
			
		}
		public function sendReport($message){
			for ($i = 0; $i < sizeOf($this->number); $i++) { 
				$num = $this->number[$i];
				$this->send($message,$num);
			}
			// $this->send($message,"09356302694");
		}
		private function messageRender($message){
			// return "Hi Mr/Ms {$name} This message is to notify you about the application of your loan ({$loan_product})";
return "System Report

{$message}

FROM: BP Lending Corp.";
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
			
				// if($res == 0){
				// 	$this->res = array(
				// 		'isError' => false,
				// 		'message'   =>  $this->textMessages[$res],
				// 		'date'    => date("Y-m-d"), 
				// 		"messageData" => $textMessage 
				// 	);
				// }else{
				// 	$this->res = array(
				// 		'isError' => true,
				// 		'message'   =>  $this->textMessages[$res],
				// 		'date'    => date("Y-m-d"),  
				// 		"messageData" => $textMessage
				// 	);
				// }
			}catch(Exception $e) {
				// $this->res = array(
				// 	'isError' => true,
				// 	'message'   => $e->getMessage(),
				// 	'date'    => date("Y-m-d"),  
				// );
			}
            // $this->displayJSON($this->res);
		}
		private function hasInsurace($borrower_id,$date){
			$sql = "SELECT * FROM insurance_payment_log
					WHERE insurance_payment_log.borrower_id = '$borrower_id' 
					AND DATE_FORMAT('$date','%Y-%m-01') BETWEEN DATE_FORMAT(insurance_payment_log.from_date,'%Y-%m-%d') AND DATE_FORMAT(insurance_payment_log.to_date,'%Y-%m-%d')
					AND insurance_payment_log.is_active = 1";
			$query = $this->db->query($sql);
			if($query->num_rows() > 0){
				return true;
			}else{
				return false;
			}
		}
		public function get_insurance(){
			try{

				$sql = "SELECT insurance_payment_log.insurance_payment_id ,
						insurance_payment_log.borrower_id,
						CONCAT(borrower.lastname,' ,',borrower.firstname,' ',borrower.middlename) as name,
						insurance_payment_log.amount,
						insurance_payment_log.date,
						insurance_payment_log.from_date,
						insurance_payment_log.to_date,
						insurance_payment_log.is_paid,
						district.name as district
						FROM insurance_payment_log	
						LEFT JOIN borrower ON borrower.borrower_id = insurance_payment_log.borrower_id
						LEFT JOIN district ON district.district_id = borrower.district_id 
						WHERE insurance_payment_log.is_active = 1";
				
				if(!empty($_POST['date'])){
					$date = date("Y-m-1",strtotime($_POST['date']));
					$sql .= " AND DATE_FORMAT('$date','%Y-%m-01') BETWEEN DATE_FORMAT(insurance_payment_log.from_date,'%Y-%m-%d') AND DATE_FORMAT(insurance_payment_log.to_date,'%Y-%m-%d')";
				}

				$data = $this->db->query($sql)->result();

				$this->res = array(
                    'isError'   => false,
					'message'   => "Success",
					'data'		=> $data,
                    'date'      => date("Y-m-d"),  
				);
				$this->displayJSON($this->res);

			}catch(Exception $e) {
                $this->res = array(
                    'isError'   => true,
                    'message'   => $e,
                    'date'      => date("Y-m-d"),  
				);
				$this->displayJSON($this->res);
			}
			
			
		}
		public function upload_insurance(){

			if(empty($_POST['data'])){
				$this->res = array(
					'isError'   => true,
					'message'   => "Empty Data",
					'date'      => date("Y-m-d"),  
				);
			}
			else if(empty($_POST['from_date'])){
				$this->res = array(
					'isError'   => true,
					'message'   => "Empty From Date",
					'date'      => date("Y-m-d"),  
				);
			}
			else{
				try{
					$array_insert = array();
					$array_repeated = array();

	
				
					foreach ($_POST['data'] as $key => $value) {

						$borrower_id = $value['borrower_id'];
						$coverage 	 = $value['coverage'];
						$from_date 	 = date("Y-m-1",strtotime($_POST['from_date']));
						$to_date 	 = date('Y-m-t', strtotime("+6 months", strtotime($from_date)));
						$date 		 = date("Y-m-1",strtotime($value['date']));
						
						if(!$this->hasInsurace($borrower_id,$date)){

							$insert_logs = array(
								'borrower_id' 	=> $borrower_id,
								'amount' 		=> $coverage,
								'from_date' 	=> $from_date,
								'to_date' 		=> $to_date,
								'date' 			=> $from_date,
							);

							$logs_res = $this->db->set($insert_logs)->get_compiled_insert('insurance_payment_log');
							array_push($array_insert,$logs_res);

						}else{
							array_push($array_repeated,array(
								'borrower_id' 	=> $borrower_id,
								'amount' 		=> $coverage,
								'from_date' 	=> $from_date,
								'to_date' 		=> $to_date,
								'date' 			=> $date,
							));
						}
					}

				


					if( sizeof($array_insert) > 0 &&  sizeof($array_repeated) <= 0 ){
						$result = array_filter($array_insert);   
						// print_r($result);exit;
						$res = $this->mysqlTQ($result);
						if($res){
							$this->res = array(
								'isError'   => false,
								'message'   => "Success",
								'date'      => date("Y-m-d"),  
								'error'		=> $array_repeated,
							);
						}else{
							$this->res = array(
								'isError'   => true,
								'message'   => "Error",
								'date'      => date("Y-m-d"),  
								'error'		=> $array_repeated,
							);
						}
					}
					else if( sizeof($array_insert) <= 0 && sizeof($array_repeated) > 0 ){
						$this->res = array(
							'isError'   => true,
							'message'   => "All ".sizeof($array_repeated).' is already on the table for the date for this date',
							'date'      => date("Y-m-d"),  
							'data'		=> $array_repeated,
						);
					}
					else if( sizeof($array_insert) > 0 &&  sizeof($array_repeated) > 0 ){
						$result = array_filter($array_insert);   
						// print_r($result);exit;
						$res = $this->mysqlTQ($result);
						if($res){
							$this->res = array(
								'isError'   => false,
								'message'   => "Success ".sizeof($array_insert)." inserted but ".sizeof($array_repeated).' are not inserted due to reapeted data',
								'date'      => date("Y-m-d"),  
								'error'		=> $array_repeated,
							);
						}else{
							$this->res = array(
								'isError'   => true,
								'message'   => "Error",
								'date'      => date("Y-m-d"),  
								'error'		=> $array_repeated,
							);
						}
					}
					else{
						$this->res = array(
							'isError'   => true,
							'message'   => "Error",
							'date'      => date("Y-m-d"),  
						);
					}
				}catch(Exception $e) {
					$this->res = array(
						'isError'   => true,
						'message'   => $e,
						'date'      => date("Y-m-d"),  
					);
				}
			}
			$this->displayJSON($this->res);
		}
		private function renderPayslip($data){
			if(!empty($data)){

				$res = $data[0];

	

				$total_pay = ($res->base_pay * $res->no_of_days) + $res->overtime + $res->paid_leaves + $res->transport_allowance + $res->bonus + $res->medical_allowance + $res->other_allowance;
				$total_deduction =  $res->sss + $res->pagibig + $res->philhealth + $res->unpaid_leave + $res->cash_advance;
				$net_pay = $total_pay - $total_deduction;
				$qr = 'http://bariliprime-api-v1.doitcebu.com/utils/generatePayslip/'.$res->payroll_id;

				$html = '<!DOCTYPE html>
				<html>
				<head>
					<title>Payroll of Mr/Ms <?php echo $payroll[0]->name; ?></title>
					<style type="text/css">
						body{
							font-weight: none;
							font-size: 11pt;
						}
						.table tr td{
							border: 1px solid black;
							font-size: 10pt;
							height: 20px;
							font-weight: bold;
						}
						.table-condensed > thead > tr > th,
						.table-condensed > tbody > tr > th,
						.table-condensed > tfoot > tr > th,
						.table-condensed > thead > tr > td,
						.table-condensed > tbody > tr > td,
						.table-condensed > tfoot > tr > td {
						  padding: 1px !important;
						}
						@media print {
						 body{
							 margin-top: 0px !important;
						 }
						}
					</style>
				</head>
				<body>	
					<div class="row">
						<div class="col-12">
							<table>
								<tr>
									<td style="width: 50px;">
										<img style="width: 100px;height: 80px;" src="'.base_url()."/assets/img/Logo.png".'">
									</td>
									<td style="width: 430px;" class="text-center">
										<div class="col-12 text-center">
											<h1>Payslip</h1>
										</div>
									</td>
									<!-- <td>
										<p style="line-height: 1em;font-size: 10px;">
											<b>Office Number: 470-9651</b>
											<b>Home Number: 470-9276</b>
											<b>SEC Registration Number:</b>
											<b>CS201633364/ CEO 38935</b>
											<b>TIN: 483-109-532-000</b>
										</p>
									</td> -->
								</tr>
							</table>
						</div>
					</div>
					<div class="row">
						<div class="col-12">
							<p>Hilarion Alquizola StreetBarili, Cebu</p>
						</div>
					</div>
					<div class="row">
						<div class="col-12">
							<table style="border: 1px solid black;">
								<tr style="background-color: #77a09d">
									<td class="text-center" style="width: 355px;border: 1px solid black;">
										<b>Name:</b> '.$res->name.'<br>
									</td>
									<td class="text-center" style="width: 355px;border: 1px solid black;">
										<b>Date:</b> '.$res->payroll_date.'<br>	
									</td>
									<td class="text-center" style="width: 355px;border: 1px solid black;">
										<b>Employee ID:</b>	'.$res->id_number.'<br>
									</td>
								</tr>
								<tr>
									<td colspan="3">
										<div class="row">
											<div class="col-12">
												<table style="width:100%;">
													<thead>
														<tr style="background-color: #ccc">
															<th>Earnings</th>
															<th>Adjustments</th>
															<th>Deductions</th>
															<th>Computations</th>
														</tr>
													</thead>
													<tbody>
														<tr tyle="background-color: #77a09d">
															<td style="width:25%;">
																<b>Basic Pay</b> : '.number_format($res->basepay * $res->basepay,2,'.',',').'
															</td>
															<td style="width:25%;">
																<b>Overtime</b> : '.number_format($res->overtime,2,'.',',').'
																<b>Paid Leaves</b> : '.number_format($res->paid_leaves,2,'.',',').'
																<b>Transport Allowance</b> : '.number_format($res->transport_allowance,2,'.',',').'
																<b>Bonus</b> : '.number_format($res->bonus,2,'.',',').'
																<b>Medical Allowance</b> : '.number_format($res->medical_allowance,2,'.',',').'
																<b>Other Allowance</b> : '.number_format($res->other_allowance,2,'.',',').'
															</td>
															<td style="width:25%;">
																<b>SSS</b> : '.number_format($res->sss,2,'.',',').'
																<b>Pagibig</b> : '.number_format($res->pagibig,2,'.',',').'
																<b>Philhealth</b> : '.number_format($res->philhealth,2,'.',',').'
																<b>Unpaid Leave</b> : '.number_format($res->unpaid_leave,2,'.',',').'
																<b>Cash Advance</b> : '.number_format($res->cash_advance,2,'.',',').'
															</td>
															<td style="width:25%;">
																<b>Salary Income</b> : '.number_format($res->basepay * $res->no_of_days,2,'.',',').'
																<div style="background-color: #ccc"><b>Salary Adj</b> : '.number_format($total_pay,2,'.',',').'<br>
																----------------------------------------------------<br>
																<div style="background-color: #ccc"><b>Deductions</b> : '.number_format($total_deduction,2,'.',',').'<br>
																----------------------------------------------------<br>
																<div style="background-color: #ccc"><b>Net Pay</b> : '.number_format($net_pay,2,'.',',').'
																<br>
																<img width="100" src="'.$qr.'" alt="">
				
															</td>
														</tr>
													</tbody>
												</table>
											</div>
										</div>
									</td>
								</tr>
								<tr>
									<td>
										
									</td>
									<td>
										
									</td>
									<td classs="text-right">
										<div class="text-right">
											<b>Net Pay</b> : <?php echo number_format($net_pay,2,".",","); ?>
										</div>
									</td>
								</tr>
							</table>
						</div>
					</div>
				</body>
				</html>';
				echo $html;
			}
		}
		private function formatOfficeDataHtml($data){
			if(!empty($data)){
				$res = $data;

				$responseData = $res[0];
				$logo = 'https://bariliprime.doitcebu.com/assets/img/Logo.png';

				$html = '<!DOCTYPE html>
				<html lang="en">
				<head>
					<meta charset="UTF-8">
					<meta name="viewport" content="width=device-width, initial-scale=1.0">
					<title>Document</title>
					<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
					<script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
					<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
					<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
				</head>
				<body class="container">
					<div class="row">
					<div class="col-12 mt-5">
					<h1> <img width="100" src="'.$logo.'"> BP Lending Logs</h1>';

				if($responseData->is_active == 0){
					$html .= '<div class="alert alert-danger alert-dismissible fade show" role="alert">
						<strong>Message : </strong> this item is currently in-active
						<button type="button" class="close" data-dismiss="alert" aria-label="Close">
						<span aria-hidden="true">&times;</span>
						</button>
					</div>';
				}else{
					$html .= '<div class="alert alert-success alert-dismissible fade show" role="alert">
						<strong>Message : </strong> this item is active
						<button type="button" class="close" data-dismiss="alert" aria-label="Close">
						<span aria-hidden="true">&times;</span>
						</button>
					</div>';
				}

				$html .= '<ul class="list-group">';

							foreach ($responseData as $key => $value) {
								
								if(is_array($value)){
									foreach ($value as $objkey => $objvalue) {
										$html .="<li class='list-group-item'> <pre>".$this->uppercase($objkey)." - ".$this->uppercase($objvalue)."</pre> </li>";	
									}
								}
								if(is_object(json_decode($value))){
									// print_r(json_decode($value));exit;
									foreach (json_decode($value) as $objkey => $objvalue) {
										$html .="<li class='list-group-item'> <pre>".$this->uppercase($objkey)." - ".$this->uppercase($objvalue)."</pre> </li>";	
									}
								}
								else{
									if($key == "signature"){
										// $html .="<li class='list-group-item'> <pre>".$this->uppercase($key)." - ".$this->uppercase($value)."</pre></li>";	
										$html .="<li class='list-group-item'><pre>Signature</pre><br><img src=".$value."></li>";	
									}
									else if($key == "is_active"){
										// $html .="<li class='list-group-item'> <pre>".$this->uppercase($key)." - ".$this->uppercase($value)."</pre></li>";	
										$html .="<li class='list-group-item'> Status: <span class=".($value == 1 ? "text-success" : "text-danger")."> ".$this->uppercase($value == 1 ? "Active" : "Inactive")."</span></li>";	
									}
									else{
										$html .="<li class='list-group-item'> <pre>".$this->uppercase($key)." - ".$this->uppercase($value)."</pre></li>";	
									}
								}
							}

					$html .= '</ul></div></div></body></html>';
						echo $html;
				
			}
		}
		public function payInsurace(){

			if(empty($_POST['id'])){
				$this->res = array(
					'isError'   => true,
					'message'   => "Empty Id",
					'date'      => date("Y-m-d"),  
				);
			}
			else{
				try{

		
					$insurace = $_POST['id'];

					$array = array(
						'is_paid'=> 1,
					);

					$this->db->where("insurance_payment_id",$insurace);
					$res = $this->db->update("insurance_payment_log",$array);


					if($res){

						$this->res = array(
							'isError'   => false,
							'message'   => "Success",
							'date'      => date("Y-m-d"),  
						);

					}else{
						$this->res = array(
							'isError'   => true,
							'message'   => "Error",
							'date'      => date("Y-m-d"),  
						);
					}
				
				}catch(Exception $e) {
					$this->res = array(
						'isError'   => true,
						'message'   => $e,
						'date'      => date("Y-m-d"),  
					);
					
				}
			}

			$this->displayJSON($this->res);
		}
		public function voidInsurance(){

			if(empty($_POST['id'])){
				$this->res = array(
					'isError'   => true,
					'message'   => "Empty Id",
					'date'      => date("Y-m-d"),  
				);
			}
			else if(empty($_POST['description'])){
				$this->res = array(
					'isError'   => true,
					'message'   => "Empty description",
					'date'      => date("Y-m-d"),  
				);
			}
			else{
				try{

		
					$insurace = $_POST['id'];

					$array = array(
						'is_active' => 0,
						'void_date' => date("Y-m-d"),
						'description' => $_POST['description']
					);

					$this->db->where("insurance_payment_id",$insurace);
					$res = $this->db->update("insurance_payment_log",$array);


					if($res){

						$this->res = array(
							'isError'   => false,
							'message'   => "Success",
							'date'      => date("Y-m-d"),  
						);

					}else{
						$this->res = array(
							'isError'   => true,
							'message'   => "Error",
							'date'      => date("Y-m-d"),  
						);
					}
				
				}catch(Exception $e) {
					$this->res = array(
						'isError'   => true,
						'message'   => $e,
						'date'      => date("Y-m-d"),  
					);
					
				}
			}

			$this->displayJSON($this->res);
		}
		// Employee
		public function employee(){
			$request_method = $_SERVER['REQUEST_METHOD'];
			switch ($request_method) {
				case 'GET':
					getEmployee();
					break;
				case 'POST':
					generateEmployee();
					break;
				case 'PUT':
					updateEmployee();
					break;
				case 'DELETE':
					deleteEmployee();
					break;
				default:
					# code...
					break;
			}
		
		}
		private function getUsersToGenerate($user_id){
			$sql = "SELECT * FROM users
					WHERE users.user_id = '$user_id'";
			return $this->db->query($sql)->result();
		}
		public function getEmployee(){

			try{

				$sql = "SELECT * FROM employee";
				$res = $this->db->query($sql)->result();
				
				if($res){
					$this->res = array(
						'isError'   => false,
						'data' 		=> $res,
						'message'   => "Success",
						'date'      => date("Y-m-d"),  
					);
				}else{
					$this->res = array(
						'isError'   => true,
						'message'   => "Error",
						'date'      => date("Y-m-d"),  
					);
				}
			
			}catch(Exception $e) {
				$this->res = array(
					'isError'   => true,
					'message'   => $e,
					'date'      => date("Y-m-d"),  
				);
				
			}

			$this->displayJSON($this->res);
		}
		public function generateEmployee(){

			if(empty($_POST['user_id'])){
				$this->res = array(
					'isError'   => true,
					'message'   => "Empty user",
					'date'      => date("Y-m-d"),  
				);
			}
			else{
				try{

					$user_id = $_POST['user_id'];
					$users_data = $this->getUsersToGenerate($user_id);

					if(!empty($users_data)){

						$array = array(
							'employee_id' => $users_data[0]->id_number,
							'date_hired'  => date("Y-m-d"),
						);
	
						$update = $this->db->insert("employee",$array);
	
						if($update){
							$this->res = array(
								'isError'   => false,
								'message'   => "Success",
								'date'      => date("Y-m-d"),  
							);
						}else{
							$this->res = array(
								'isError'   => true,
								'message'   => "Error",
								'date'      => date("Y-m-d"),  
							);
						}
					}else{
						$this->res = array(
							'isError'   => true,
							'message'   => 'No users with this user id found',
							'date'      => date("Y-m-d"),  
						);
					}
				
				}catch(Exception $e) {
					$this->res = array(
						'isError'   => true,
						'message'   => $e,
						'date'      => date("Y-m-d"),  
					);
					
				}
			}

			$this->displayJSON($this->res);
		}
		public function updateEmployee(){

			if(empty($_POST['user_id'])){
				$this->res = array(
					'isError'   => true,
					'message'   => "Empty user",
					'date'      => date("Y-m-d"),  
				);
			}
			else{
				try{

					$user_id = $_POST['user_id'];
		
					$array = array(
						'employee_id' 	=> $_POST['employee_id'],
						'sss_id' 	 	=> $_POST['sss_id'],
						'tin'  			=> $_POST['tin'],
						'pag-ibig'  	=> $_POST['	pag-ibig'],
						'date_hired'  	=> $_POST['date_hired'],
					);

					$update = $this->db->update("employee",$array);

					if($update){
						$this->res = array(
							'isError'   => false,
							'message'   => "Success",
							'date'      => date("Y-m-d"),  
						);
					}else{
						$this->res = array(
							'isError'   => true,
							'message'   => "Error",
							'date'      => date("Y-m-d"),  
						);
					}
				
				}catch(Exception $e) {
					$this->res = array(
						'isError'   => true,
						'message'   => $e,
						'date'      => date("Y-m-d"),  
					);
					
				}
			}

			$this->displayJSON($this->res);
		}
		public function deleteEmployee(){

			if(empty($_POST['user_id'])){
				$this->res = array(
					'isError'   => true,
					'message'   => "Empty user",
					'date'      => date("Y-m-d"),  
				);
			}
			else{
				try{

					$user_id = $_POST['user_id'];
		
					$array = array(
						'date_suspended' => date("Y-m-d"),
						'is_active'  	 => 0,
					);

					$update = $this->db->update("employee",$array);

					if($update){
						$this->res = array(
							'isError'   => false,
							'message'   => "Success",
							'date'      => date("Y-m-d"),  
						);
					}else{
						$this->res = array(
							'isError'   => true,
							'message'   => "Error",
							'date'      => date("Y-m-d"),  
						);
					}
				
				}catch(Exception $e) {
					$this->res = array(
						'isError'   => true,
						'message'   => $e,
						'date'      => date("Y-m-d"),  
					);
					
				}
			}

			$this->displayJSON($this->res);
		}
		// Employee
		private function uppercase($str){
			return strtoupper($str);
		}
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
    }
?>
