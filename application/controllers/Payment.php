<?php
class Payment extends CI_Controller{

	private $res = array();
	/**
		* Class constructor.
		*
	*/
	public function __construct() {
		parent::__construct();
		date_default_timezone_set('Asia/Manila');
	}

    public function insurance_payment(){

		if(empty($_POST['amount'])){
			$this->res = array(
				'isError' => true,
				'message'   => "Empty amount",
				'date'    => date("Y-m-d"),  
			);
		}
		else if(empty($_POST['borrower_id'])){
			$this->res = array(
				'isError' => true,
				'message'   => "Empty borrower_id",
				'date'    => date("Y-m-d"),  
			);
		}else{
			try{
				$amount = $_POST['amount'];
				$borrower_id = $_POST['borrower_id'];
				$date = $_POST['date'];

				$array = (
					'amount' => $amount,
					'borrower_id' => $borrower_id,
					'date' => $date,
				);

				$data = $this->input->post('insurance_payment_log',$array);

				if($data){
					$this->res = array(
						'isError' => false,
						'message'   =>"Success",
						'date'    => date("Y-m-d"),  
					);
				}else{
					$this->res = array(
						'isError' => true,
						'message'   => $data,
						'date'    => date("Y-m-d"),  
					);
				}
			}catch(Exception $e) {
				$this->res = array(
					'isError' => true,
					'message'   => $e->getMessage(),
					'date'    => date("Y-m-d"),  
				);
			}
		}
		$this->displayJSON($this->res);
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
}
?>
