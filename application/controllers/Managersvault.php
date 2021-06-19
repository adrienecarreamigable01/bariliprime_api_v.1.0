
<?php
    /**
     * @author  Adriene Care Llanos Amigable <adrienecarreamigable01@gmail.com>
     * @version 0.1.0
    */ 
    class managersvault extends CI_Controller{
        /* Global Variables */
        private $res = array();
        /**
            * Class constructor.
            *
        */
        public function __construct() {
			parent::__construct();
            date_default_timezone_set('Asia/Manila');
            $this->load->model('BorrowerModel','borrowermodel');
        }
        #Login
        public function get(){

            if(empty($_POST['user_id'])){
                $this->res = array(
                    'isError' => true,
                    'date'    => date("Y-m-d"),  
                    'message' => 'User id is required',
                );
            }else{

                try{

					$user_id = $_POST['user_id'];
					
					$sql = "SELECT
								managers_vault.managers_vault_id,
								managers_vault.managers_vault_type_id,
								managers_vault.user_id,
								managers_vault.amount,
								managers_vault.date,
								CONCAT(user.lastname,' ,',user.firstname) AS username,
								managers_vault_type.managers_vault_type,
								managers_vault.description,
								managers_vault.is_active
							FROM managers_vault 
							LEFT JOIN user ON user.user_id = managers_vault.user_id
							LEFT JOIN managers_vault_type ON managers_vault_type.managers_vault_type_id = managers_vault.managers_vault_type_id
							WHERE managers_vault.user_id = {$user_id} AND managers_vault.is_active = 1
							ORDER BY managers_vault.date ASC";
					
                    
					$data = $this->db->query($sql)->result();
					$newData = array();
					$total = 0;

					foreach ($data as $key => $value) {
						
						$value->managers_vault_type_id == 1 ? $total += $value->amount : $total -= $value->amount;
						
						array_push($newData,array(
							'managers_vault_id' => $value->managers_vault_id,
							'managers_vault_type_id' => $value->managers_vault_type_id,
							'user_id' => $value->user_id,
							'amount' => $value->amount,
							'running_balance' => $total,
							'date' => $value->date,
							'username' => $value->username,
							'managers_vault_type' => $value->managers_vault_type,
							'description' => $value->description,
							'is_active' => $value->is_active,
						));
						
					}

					$this->res = array(
						'isError' => false,
						'date'    => date("Y-m-d"),  
						'message' => "success",
						'data' => $newData
					);
    
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
        public function add(){

            if(empty($_POST['user_id'])){
                $this->res = array(
                    'isError' => true,
                    'date'    => date("Y-m-d"),  
                    'message' => 'User id is required',
                );
			}
            else if(empty($_POST['amount'])){
                $this->res = array(
                    'isError' => true,
                    'date'    => date("Y-m-d"),  
                    'message' => 'amount is required',
                );
			}
            else if(empty($_POST['managers_vault_type_id'])){
                $this->res = array(
                    'isError' => true,
                    'date'    => date("Y-m-d"),  
                    'message' => 'managers_vault_type_id is required',
                );
			}
            else if(empty($_POST['description'])){
                $this->res = array(
                    'isError' => true,
                    'date'    => date("Y-m-d"),  
                    'message' => 'description is required',
                );
			}
			else{

                try{

					$array = array(
						'user_id' 				 => $_POST['user_id'],
						'date' 					 => date("Y-m-d H:i:s"),
						'amount' 				 => $_POST['amount'],
						'managers_vault_type_id' => $_POST['managers_vault_type_id'],
						'description' 			 => $_POST['description'],
					);
                    
					$data = $this->db->insert("managers_vault",$array);

					if($data){
						$this->res = array(
							'isError' => false,
							'date'    => date("Y-m-d"),  
							'message' => "success",
							'data' => $data
						);
					}else{
						$this->res = array(
							'isError' => true,
							'date'    => date("Y-m-d"),  
							'message' => 'Error inserting',
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
		private function getManagersVaultData($id = ""){
			$sql = "SELECT * FROM managers_vault
					WHERE 1";
			if(!empty($id) || $id != ""){
				$sql .=" AND managers_vault.managers_vault_id = '$id'";
			}
			return $this->db->query($sql)->result();
		}
        public function update(){

            if(empty($_POST['amount'])){
                $this->res = array(
                    'isError' => true,
                    'date'    => date("Y-m-d"),  
                    'message' => 'amount is required',
                );
			}
            else if(empty($_POST['managers_vault_id'])){
                $this->res = array(
                    'isError' => true,
                    'date'    => date("Y-m-d"),  
                    'message' => 'managers_vault_id is required',
                );
			}
            // else if(empty($_POST['managers_vault_type_id'])){
            //     $this->res = array(
            //         'isError' => true,
            //         'date'    => date("Y-m-d"),  
            //         'message' => 'managers_vault_type_id is required',
            //     );
			// }
            else if(empty($_POST['description'])){
                $this->res = array(
                    'isError' => true,
                    'date'    => date("Y-m-d"),  
                    'message' => 'description is required',
                );
			}
			else{

                try{

					$managers_vault_id  = $_POST['managers_vault_id'];
					$managersVaultData = $this->getManagersVaultData($managers_vault_id);

					$array = array(
						'user_id' => $_POST['user_id'],
						// 'date' => date("Y-m-d H:i:s"),
						'amount' => $_POST['amount'],
						'description' => $_POST['description'],
					);
                    
					$this->db->where("managers_vault_id",$managers_vault_id);
					$data = $this->db->update("managers_vault",$array);

					if($data){
						if(!is_null ($managersVaultData[0]->minivault_id) || $managersVaultData[0]->minivault_id != ""){
							$payload = array(   
								'amount' => $_POST['amount'],     
							);
							// print_r($payload);exit;
							$this->db->where("cashier_vault_id ",$managersVaultData[0]->minivault_id);
							$this->db->update("cashier_vault",$payload);
						}else{
							$this->res = array(
								'isError' => false,
								'date'    => date("Y-m-d"),  
								'message' => "success",
								'data' => $data
							);
						}
						
					}else{
						$this->res = array(
							'isError' => true,
							'date'    => date("Y-m-d"),  
							'message' => 'Error inserting',
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
		private function add_data($userId,$amount,$managers_vault_type_id,$description,$minivault_id = ""){
			
			$array = array(
				'user_id'   			 => $userId,
				'date' 					 => date("Y-m-d H:i:s"),
				'amount'    			 => $amount,
				'managers_vault_type_id' => $managers_vault_type_id,
				'description' 			 => $description,
			);

			if($minivault_id != "" || !empty($minivault_id)){
				$array['minivault_id'] = $minivault_id;
			}
			
			return $this->db->insert("managers_vault",$array);
		}
        public function transferToCashier(){

			$response = array();

			if(empty($_POST['name'])){
				$this->res = array(
					'isError'   => true,
					'date'      => date("Y-m-d"),
					'message'   => 'Empty user name',
				);
			}
			else if(empty($_POST['user_id'])){
				$this->res = array(
					'isError'   => true,
					'date'      => date("Y-m-d"),
					'message'   => 'Empty user_id',
				);
			}
			else if(empty($_POST['assign_id'])){
				$this->res = array(
					'isError'   => true,
					'date'      => date("Y-m-d"),
					'message'   => 'Empty assign_id',
				);
			}
			else if(empty($_POST['amount'])){
				$this->res = array(
					'isError'   => true,
					'date'      => date("Y-m-d"),
					'message'   => 'Empty amount',
				);
			}
			else if(empty($_POST['description'])){
				$this->res = array(
					'isError'   => true,
					'date'      => date("Y-m-d"),
					'message'   => 'Empty description',
				);
			}else{
				try{
					$date = date("Y-m-d",strtotime($_POST['date']));
					$time = date("H:i:s");
					$l = $this->input->post("logs");
					$cashier_vault_type_id  = $_POST['cashier_vault_type_id'];
					$amount 				= $_POST['amount'];
					$description 			= $_POST['description'];
				 
					$payload = array(
						'user_id'               => $_POST['user_id'],    
						'amount'                => $_POST['amount'],    
						'assign_id'             => $_POST['assign_id'],    
						'date_added'            => date("Y-m-d H:i:s",strtotime($date.' '.$time)),    
						'description'           => $description,    
						'cashier_vault_type_id' => $cashier_vault_type_id,    
						'transaction_type_id'   => $_POST['transaction_type_id'],    
					);
	
				
					// print_r($payload);exit;
					$this->db->insert("cashier_vault",$payload);
					$data = $this->db->insert_id();

					if($data > 0 ){

						$addData = $this->add_data($_POST['user_id'],$_POST['amount'],2,$_POST['description'],$data);

						if($addData){
							
							$date   = date("Y-m-d H:i:s");
							$amount = $_POST['amount'];
							$name   = $_POST['name'];
	
							
							$logs = array(
								'description'       => "{$l} by {$name}", 
								'cashier_vault_id'  => $data,
							);
							
							$res = $this->db->insert("cashier_vault_logs",$logs);

							if($res){
								$this->res = array(
									'isError'   => false,
									'data'      => $data,
									'date'      => date("Y-m-d"),
									'payload'      =>$payload,
									'message'   => 'Successfully added transaction',
								);
							}else{
								$this->res = array(
									'isError'   => false,
									'data'      => $data,
									'date'      => date("Y-m-d"),
									'payload'      =>$payload,
									'message'   => 'Successfully added transaction',
								);
							}
						}else{
							$this->res = array(
								'isError'   => true,
								'date'      => date("Y-m-d"),
								'message'   => 'Error',
							);
						}
					}else{
						$this->res = array(
							'isError'   => true,
							'date'      => date("Y-m-d"),
							'message'   => 'Eror inserting data',
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
        private function update_lastlogin($userid){

            date_default_timezone_set('Asia/Manila');
            
			$data = array(
				'last_login' => date("Y/m/d h:i:sa"),
            );
            
			$this->db->where('user_id', $userid);
			return $this->db->update('user',$data);
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
