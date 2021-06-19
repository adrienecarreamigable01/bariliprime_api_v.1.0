<?php
class Bank extends CI_Controller{

	private $res = array();
	/**
		* Class constructor.
		*
	*/
	public function __construct() {
		parent::__construct();
		date_default_timezone_set('Asia/Manila');
	}

    public function get(){

		$sql    = "SELECT
						*
					FROM
					bank_transaction
					WHERE 1";
		
		if(!empty($_GET['bank_id'])){
			$sql .= " AND bank_transaction.bank_account_id = {$_GET['bank_id']}";
		}

		$data   = $this->db->query($sql)->result();

		$this->res = array(
			'isError' => false,
			'data'    => $data,  
			'last_transaction' => !empty($_GET['bank_id']) ? $this->last_bank_transaction($_GET['bank_id']) : "",
		);

        $this->displayJSON($this->res);
    }
    public function last_bank_transaction($id){

		$sql    = "SELECT
						bank_transaction.bank_transaction_id,
						bank_transaction.bank_account_id,
						bank_transaction.amount,
						bank_transaction.transaction_date,
						bank_transaction.bank_transaction_type_id,
						bank_transaction.receipt_number,
						bank_transaction.image,
						bank_transaction.void_date,
						bank_transaction.is_void,
						bank_transaction_type.bank_transaction_type
					FROM
					bank_transaction
					LEFT JOIN bank_transaction_type ON bank_transaction_type.bank_transaction_type_id = bank_transaction.bank_transaction_type_id
					WHERE 1 AND bank_transaction.bank_account_id = '$id'
					ORDER BY bank_transaction.bank_transaction_id DESC
					LIMIT 1 ";
		


		return $this->db->query($sql)->result();
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
