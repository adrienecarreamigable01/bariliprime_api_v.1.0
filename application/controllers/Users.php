
<?php
    /**
     * @author  Adriene Care Llanos Amigable <adrienecarreamigable01@gmail.com>
     * @version 0.1.0
    */ 
    class users extends CI_Controller{
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
            
            try{

                $sql = "SELECT * FROM user
                        WHERE user.is_active = 1";

                if(!empty($_GET['usertype_id'])){
                    $sql .=" AND user.usertype_id = '{$_GET['usertype_id']}'";
                }

                $data =  $this->db->query($sql)->result();

                $this->res = array(
                    'isError'   => false,
                    'message'   => "Success",
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

            $this->displayJSON($this->res);
            
        }
        public function updateProfile(){

            if(!isset($_POST['user_id'])){
                $this->res = array(
                    'isError'   => true,
                    'message'   => "Empty | Null UserId",
                    'date'      => date("Y-m-d"),  
                );
            }
            else if(!isset($_POST['lastname'])){
                $this->res = array(
                    'isError'   => true,
                    'message'   => "Empty | Null Lastname",
                    'date'      => date("Y-m-d"),  
                );
            }
            else if(!isset($_POST['firstname'])){
                $this->res = array(
                    'isError'   => true,
                    'message'   => "Empty | Null Firstname",
                    'date'      => date("Y-m-d"),  
                );
            }
            else if(!isset($_POST['email'])){
                $this->res = array(
                    'isError'   => true,
                    'message'   => "Empty | Null Email",
                    'date'      => date("Y-m-d"),  
                );
            }
            else if(!isset($_POST['mobile_number'])){
                $this->res = array(
                    'isError'   => true,
                    'message'   => "Empty | Null Mobile",
                    'date'      => date("Y-m-d"),  
                );
            }
            else{
                try{

                    $id = $_POST['user_id'];
    
                    $data = array(
                        'lastname' 		=> $_POST['lastname'],
                        'firstname' 	=> $_POST['firstname'],
                        'middlename' 	=> $_POST['middlename'],
                        'email' 		=> $_POST['email'],
                        'mobile_number' => $_POST['mobile_number'],
                        // 'group_id' 		=> $this->input->post('group'),
                    );
    
                    $this->db->where('user_id',$id);
                    $updateData =  $this->db->update('user',$data);
    
                    if($updateData){
                        $fullname = $_POST['lastname'].', '.$_POST['firstname'];
                        $insert_logs = array(
                            'logs' => $fullname." updated profile using mobile ".date("Y-m-d H:i:s"),
                            'user_id' 	=> $id
                        );
                        $insert_logs_res = $this->db->insert("logs",$insert_logs);
                        if($insert_logs_res){
                            $this->res = array(
                                'isError'   => false,
                                'message'   => "Successfuly Update User",
                                'data'      => $data,
                                'date'      => date("Y-m-d"),  
                            );
                        }else{
                            $this->res = array(
                                'isError'   => false,
                                'message'   => "Successfuly Update User",
                                'data'      => $data,
                                'date'      => date("Y-m-d"),  
                            );
                        }
                    }else{
                        $this->res = array(
                            'isError' => true,
                            'message'   => "Error Updating Data",
                            'date'    => date("Y-m-d"),  
                        );
                    }
    
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
        public function updateUsername(){

            if(!isset($_POST['user_id'])){
                $this->res = array(
                    'isError'   => true,
                    'message'   => "Empty | Null UserId",
                    'date'      => date("Y-m-d"),  
                );
            }
            else if(!isset($_POST['username'])){
                $this->res = array(
                    'isError'   => true,
                    'message'   => "Empty | Null username",
                    'date'      => date("Y-m-d"),  
                );
            }
            else if(!isset($_POST['password'])){
                $this->res = array(
                    'isError'   => true,
                    'message'   => "Empty | Null password",
                    'date'      => date("Y-m-d"),  
                );
            }
            else{
                try{

                    $id         = $_POST['user_id'];
                    $password   = $_POST['password'];
                    $username   = $_POST['username'];

                    $checkPassword = $this->checkPassword($id,$password);
         
                    if($checkPassword){

                        $data = array(
                            'username' 		=> $username,
                        );

                        $this->db->where('user_id',$id);
                        $updateData =  $this->db->update('user',$data);
        
                        if($updateData){
                            $fullname = $_POST['name'];
                            $insert_logs = array(
                                'logs' => $fullname." updated username using mobile ".date("Y-m-d H:i:s"),
                                'user_id' 	=> $id
                            );
                            $insert_logs_res = $this->db->insert("logs",$insert_logs);
                            if($insert_logs_res){
                                $this->res = array(
                                    'isError'   => false,
                                    'message'   => "Successfuly Update User",
                                    'data'      => $data,
                                    'date'      => date("Y-m-d"),  
                                );
                            }else{
                                $this->res = array(
                                    'isError'   => false,
                                    'message'   => "Successfuly Update User",
                                    'data'      => $data,
                                    'date'      => date("Y-m-d"),  
                                );
                            }
                        }else{
                            $this->res = array(
                                'isError'   => true,
                                'message'   => "Error Updating Data",
                                'data'      => $data,
                                'date'      => date("Y-m-d"),  
                            );
                        }
                    }else{
                        $this->res = array(
                            'isError'   => true,
                            'message'   => "Please enter your valid password",
                            'date'      => date("Y-m-d"),  
                        );
                    }
        
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
        public function updatePassword(){

            if(!isset($_POST['user_id'])){
                $this->res = array(
                    'isError'   => true,
                    'message'   => "Empty | Null UserId",
                    'date'      => date("Y-m-d"),  
                );
            }
            else if(!isset($_POST['newPassword'])){
                $this->res = array(
                    'isError'   => true,
                    'message'   => "Empty | Null new password",
                    'date'      => date("Y-m-d"),  
                );
            }
            else if(!isset($_POST['oldPassword'])){
                $this->res = array(
                    'isError'   => true,
                    'message'   => "Empty | Null old password",
                    'date'      => date("Y-m-d"),  
                );
            }
            else{
                try{

                    $id         = $_POST['user_id'];
                    $oldPw      = $_POST['oldPassword'];
                    $newPw      = $_POST['newPassword'];

                    $checkPassword = $this->checkPassword($id,$oldPw);
         
                    if($checkPassword){

                        $pw = base64_encode($newPw);

                        $data = array(
                            'password' 		=> $pw,
                        );
                        $this->db->where('user_id',$id);
                        $updateData =  $this->db->update('user',$data);
        
                        if($updateData){
                            $fullname = $_POST['name'];
                            $insert_logs = array(
                                'logs' => $fullname." updated password using mobile ".date("Y-m-d H:i:s"),
                                'user_id' 	=> $id
                            );
                            $insert_logs_res = $this->db->insert("logs",$insert_logs);
                            if($insert_logs_res){
                                $this->res = array(
                                    'isError'   => false,
                                    'message'   => "Successfuly Update Password",
                                    'data'      => $data,
                                    'date'      => date("Y-m-d"),  
                                );
                            }else{
                                $this->res = array(
                                    'isError'   => false,
                                    'message'   => "Successfuly Update Password",
                                    'data'      => $data,
                                    'date'      => date("Y-m-d"),  
                                );
                            }
                        }else{
                            $this->res = array(
                                'isError'   => true,
                                'message'   => "Error Updating Password",
                                'data'      => $data,
                                'date'      => date("Y-m-d"),  
                            );
                        }
                    }else{
                        $this->res = array(
                            'isError'   => true,
                            'message'   => "Please enter your valid password",
                            'date'      => date("Y-m-d"),  
                        );
                    }
        
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
        public function kb(){

			try{

				$sql = "SELECT knowledgebase.knowledgebase_id,
							knowledgebase.knowledgebase_type_id,
							knowledgebase_type.knowledgebase_type,
							knowledgebase.article,
							knowledgebase.section_number,
							knowledgebase.section_details,
							knowledgebase.level,
							knowledgebase.penalty
						FROM knowledgebase
						LEFT JOIN knowledgebase_type ON knowledgebase_type.knowledgebase_type_id = knowledgebase.knowledgebase_type_id
						WHERE knowledgebase.is_active = 1 ";

					if(!empty($_POST['knowledgebase_type_id'])){
						$sql .= "AND knowledgebase.knowledgebase_type_id = ".$_POST['knowledgebase_type_id'];
					}else{
						$sql .= "AND knowledgebase.knowledgebase_type_id = 1";
					}
				$data = $this->db->query($sql)->result();

				$this->res = array(
					'isError'   => false,
					'message'   => "success",
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
            
            
            $this->displayJSON($this->res);
		}
		public function getLoginLogs($user_id = ""){
			try{
				$sql = "SELECT * FROM login_logs
						WHERE 1";
				if($user_id != ""){
					$sql .= " AND login_logs.user_id = '{$user_id}'";
				}

				$sql .= " ORDER BY login_logs.user_id DESC";

				$data = $this->db->query($sql)->result();
				
				$this->res = array(
					'isError'   => false,
					'message'   => "success",
					'data'      => $data,
					'date'      => date("Y-m-d"),  
				);
			}
			catch(Exception $e){
				$this->res = array(
					'isError' => true,
					'message'   => $e->getMessage(),
					'date'    => date("Y-m-d"),  
				);
			}
            
            
            $this->displayJSON($this->res);
		}
        private function checkPassword($userid,$password){

            $pw = base64_encode($password);

            $sql = "SELECT *
                    FROM user
                    WHERE user.password = '$pw' AND user.user_id = '$userid' AND user.is_active = 1";
            $query = $this->db->query($sql);

            if($query->num_rows() > 0){
                return true;
            }else{
                return false;
            }
        }
		public function test(){
			echo base_url();
		}
		public function getTimeLogs(){
			try{
				$sql = "SELECT
						time_logs.time_logs_id,
						CONCAT(user.firstname,' ',user.lastname) as name,
						time_logs.user_id,
						time_logs.time_in,
						time_logs.time_out,
						time_logs.is_paid
						FROM time_logs
						LEFT JOIN user ON user.user_id = time_logs.user_id
						WHERE 1 AND time_logs.is_active = 1";

				if(isset($_GET['user_id'])){
					$user_id = $_GET['user_id'];
					$sql .= " AND time_logs.user_id = '{$user_id}'";
				}

				if(isset($_GET['is_paid'])){
					$is_paid = $_GET['is_paid'];
					$sql .= " AND time_logs.is_paid = '{$is_paid}'";
				}

				$sql .= " ORDER BY time_logs.time_in ASC";

				$data = $this->db->query($sql)->result();
				
				$this->res = array(
					'isError'   => false,
					'message'   => "success",
					'data'      => $data,
					'date'      => date("Y-m-d"),  
				);
			}
			catch(Exception $e){
				$this->res = array(
					'isError' => true,
					'message'   => $e->getMessage(),
					'date'    => date("Y-m-d"),  
				);
			}
            
            
            $this->displayJSON($this->res);
		}
		public function addLogs(){

            if(!isset($_POST['date'])){
                $this->res = array(
                    'isError'   => true,
                    'message'   => "Empty | Null date",
                    'date'      => date("Y-m-d"),  
                );
            }
            else if(!isset($_POST['is_paid'])){
                $this->res = array(
                    'isError'   => true,
                    'message'   => "Empty | Null is_paid",
                    'date'      => date("Y-m-d"),  
                );
            }
            else if(!isset($_POST['user_id'])){
                $this->res = array(
                    'isError'   => true,
                    'message'   => "Empty | Null user_id",
                    'date'      => date("Y-m-d"),  
                );
            }
            else{
                try{

                    $user_id         = $_POST['user_id'];
                    $date         = $_POST['date'];
                    $is_paid         = $_POST['is_paid'];

					$data = array(
						'user_id' => $user_id,
						'time_in' => date("Y-m-d 8:00:00",strtotime($date)),
						'time_out' =>date("Y-m-d 5:00:00",strtotime($date)),
						'is_paid' => $is_paid,
					);

					$datax =  $this->db->insert('time_logs',$data);
	
					if($datax){
						$this->res = array(
							'isError'   => false,
							'message'   => "Successfuly add timelog",
							'date'      => date("Y-m-d"),  
						);
					}else{
						$this->res = array(
							'isError'   => false,
							'message'   => "Successfuly add timelog",
							'date'      => date("Y-m-d"),  
						);
					}

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
		public function deleteLogs(){

            if(!isset($_POST['time_logs_id'])){
                $this->res = array(
                    'isError'   => true,
                    'message'   => "Empty | Null time_logs_id",
                    'date'      => date("Y-m-d"),  
                );
            }
            else{
                try{

                    $time_logs_id         = $_POST['time_logs_id'];

                    $data = array('is_active' => 0);
					$this->db->where('time_logs_id',$time_logs_id);
					$updateData =  $this->db->update('time_logs',$data);
	
					if($updateData){
						$this->res = array(
							'isError'   => false,
							'message'   => "Successfuly delete timelog",
							'date'      => date("Y-m-d"),  
						);
					}else{
						$this->res = array(
							'isError'   => false,
							'message'   => "Successfuly delete timelog",
							'date'      => date("Y-m-d"),  
						);
					}

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
