
<?php
    /**
     * @author  Adriene Care Llanos Amigable <adrienecarreamigable01@gmail.com>
     * @version 0.1.0
    */ 
    class cashier extends CI_Controller{
        /* Global Variables */
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
            if(empty($_POST['userId'])){

                $this->res = array(
                    'isError' => true,
                    'message'   => "Please enter cashier id",
                    'date'    => date("Y-m-d"),  
                );

            }else{
                try{

                    $user_id = $_POST['userId'];
                    $date = !empty($_POST['date']) ? $_POST['date'] : date("Y-m-d");
                    $sql = "SELECT
                                cashier_vault.cashier_vault_id as id,
                                cashier_vault.amount,
                                cashier_vault.date_added,
                                cashier_vault.description,
                                cashier_vault.assign_id,
                                'Cashier' as 'type',
                                cashier_vault.transaction_type_id as transaction_type_id,
                                CONCAT(user.lastname,', ',user.firstname) as trasnsactby
                            FROM cashier_vault
                            LEFT JOIN user ON user.user_id = cashier_vault.user_id
                            WHERE cashier_vault.is_active = 1 AND cashier_vault.assign_id = '{$user_id}' AND DATE_FORMAT(cashier_vault.date_added,'%Y-%m-%d') = '{$date}'";
                    $data =  $this->db->query($sql)->result();

                    $this->res = array(
                        'isError'   => false,
                        'message'   => "Success",
                        'isDone'    => $this->checkIfDone($user_id,$date),
                        'total'     => $this->getTotal($data)['total'],
                        'in'        => $this->getTotal($data)['cashin'],
                        'out'        => $this->getTotal($data)['cashout'],
                        'data'      => $data,
                        'date'      => date("Y-m-d"),  
                    );

                }catch(Exception $e){
                    $this->res = array(
                        'isError' => true,
                        'message'   => $e->getMessage(),
                        'date'    => date("Y-m-d"),  
                    );
                }
            }

            $this->displayJSON($this->res);
            
        }
        private function getTotal($data){
            $total = 0;
            $cashin = 0;
            $cashout = 0;
            $array = array();
            foreach ($data as $key => $value) {
                if($value->transaction_type_id == "1"){
                    $total +=  floatval($value->amount);
                    $cashin += floatval($value->amount);
                }else{
                    $total -= floatval($value->amount);
                    $cashout += floatval($value->amount);
                }
            }

            return $array = array(
                'total' => $total,
                'cashin' => $cashin,
                'cashout' => $cashout,
            );
        }
        private function checkIfDone($date,$assign_id){
            // $date = date("Y-m-d",strtotime("2020-09-14"));
            if(empty($date)){
               return "Error";
            }
            if(empty($assign_id)){
                return "Error";
            }
            else{
                try{

                    $sql = "SELECT * FROM cashier_daily_transaction
                            WHERE DATE_FORMAT(cashier_daily_transaction.date,'%Y-%m-%d') = '{$date}' AND cashier_daily_transaction.assign_id = '{$assign_id}'";
                    $data =   $this->db->query($sql)->num_rows();
                    if( $data > 0 ){
                        return true;
                    }else{
                        return false;
                    }
                }
                catch(Exception $e) {
                    return "Error";
                }
            }

           
        }
        public function deposit(){

            if(empty($_POST['date'])){
                $this->res = array(
                    'isError'   => true,
                    'date'      => date("Y-m-d"),
                    'message'   => 'Please indicate a date',
                );
            }
            else if(empty($_POST['amount'])){
                $this->res = array(
                    'isError'   => true,
                    'date'      => date("Y-m-d"),
                    'message'   => 'Please indicate a amount',
                );
            }
            else if(empty($_POST['assign_id'])){
                $this->res = array(
                    'isError'   => true,
                    'date'      => date("Y-m-d"),
                    'message'   => 'Empty | Null user',
                );
            }
            else if(empty($_POST['transaction_type_id'])){
                $this->res = array(
                    'isError'   => true,
                    'date'      => date("Y-m-d"),
                    'message'   => 'Empty | Null transaction type',
                );
            }
            else if(empty($_POST['description'])){
                $this->res = array(
                    'isError'   => true,
                    'date'      => date("Y-m-d"),
                    'message'   => 'Empty | Null Description',
                );
            }
            else if(empty($_POST['user_id'])){
                $this->res = array(
                    'isError'   => true,
                    'date'      => date("Y-m-d"),
                    'message'   => 'Opps something went wrong ! you are not allowed to this system.',
                );
            }
            else{
                try{

                    $date = date("Y-m-d",strtotime($_POST['date']));
                    $time = date("H:i:s");
                    $l = $this->input->post("logs");
          
                    $payload = array(
                        'user_id'               => $_POST['user_id'],    
                        'amount'                => $_POST['amount'],    
                        'assign_id'             => $_POST['assign_id'],    
                        'date_added'            => date("Y-m-d H:i:s",strtotime($date.' '.$time)),    
                        'description'           => $_POST['description'],    
                        'transaction_type_id'   => $_POST['transaction_type_id'],    
                    );

                    if(!empty($_POST['borrower_id'])){
                        $payload['borrower_id'] = $_POST['borrower_id'];
                    }else{
                        $payload['borrower_id'] = "";
                    }

                    // print_r($payload);exit;

                    $res    = $this->db->insert("cashier_vault",$payload);
                    $data   = $this->db->insert_id();

                    if($data > 0){

                        $date   = date("Y-m-d H:i:s");
                        $amount = $_POST['amount'];
                        $name   = $_POST['name'];

                        $logs = array(
                            'description'       => "{$l} by {$name}", 
                            'cashier_vault_id'  => $data,
                        );

                        $this->insert_logs($logs);

                        $this->res = array(
                            'isError'   => false,
                            'data'      => $data,
                            'date'      => date("Y-m-d"),
                            'payload'   => $payload,
                            'message'   => 'Successfully added transaction',
                        );

                        

                    }else{
                        $this->res = array(
                            'isError'   => true,
                            'date'      => date("Y-m-d"),
                            'message'   => 'Error inserting data',
                        );
                    }
                }
                catch(Exception $e) {
                    $this->res = array(
                        'isError'   => true,
                        'date'      => date("Y-m-d"),
                        'message'   => $e->getMessage(),
                    );
                }
            }

            $this->displayJSON($this->res);
        }
        public function insert_logs($payload){
            return $this->db->insert("cashier_vault_logs",$payload);
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
        /*
            * Add Borrower api
            * This will add new borrower to online database
        */
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
    }
?>