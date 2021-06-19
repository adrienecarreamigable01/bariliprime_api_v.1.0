
<?php
    /**
     * @author  Adriene Care Llanos Amigable <adrienecarreamigable01@gmail.com>
     * @version 0.1.0
    */ 
    class authentication extends CI_Controller{
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
        public function authenticate(){

            if(empty($_POST['username'])){
                $this->res = array(
                    'isError' => true,
                    'date'    => date("Y-m-d"),  
                    'message' => 'Username is required',
                );
            }
			else if(empty($_POST['password'])){
                $this->res = array(
                    'isError' => true,
                    'date'    => date("Y-m-d"),  
                    'message' => 'Password is required',
                );
            }
			else if(empty($_POST['server'])){
                $this->res = array(
                    'isError' => true,
                    'date'    => date("Y-m-d"),  
                    'message' => 'Server is required',
                );
            }
			else{

                try{

                    $username = $_POST['username'];
                    $password = base64_encode($_POST['password']);
					$date = date("Y-m-d").' '.date_default_timezone_get();
					$device = $this->getDevice();
					$browser = $this->getBrowser();
					
                    $sql = "SELECT user.user_id,
                                    user.id_number,
                                    user.usertype_id,
									CONCAT(user.lastname,', ',user.firstname) as name,
                                    user.lastname,
                                    user.firstname,
                                    user.middlename,
                                    user.username,
                                    user.email,
                                    user.mobile_number,
                                    user.recovery_code,
                                    user.last_login,
                                    user.image,
                                    user.basepay,
                                    user.module_id,
                                    user.modify_date,
                                    user.position,
                                    user.is_active,
                                    usertype.name as usertype
                            FROM user
                            LEFT JOIN usertype ON usertype.usertype_id = user.usertype_id
                            WHERE user.username = '{$username}' AND user.password = '{$password}' ";
                    $query      = $this->db->query($sql);
                    $result     = $query->result();
                    $num_rows   = $query->num_rows();
                    if( $num_rows < 1 ){
						
                        $this->res = array(
                            'isError' => true,
                            'date'    => date("Y-m-d"),  
                            'message' => "Invalid username and password",
                        );
						
						$description = array(
							'date' 			=> $date,
							'device' 		=> $device,
							'browser' 		=> $browser,
							'server' 		=> $_POST['server'],
							'username' 		=> $_POST['username'],
							'password' 		=> base64_encode($_POST['password']),
							'ip_address' 	=> $_SERVER['REMOTE_ADDR'],
						);

						$this->insertLogs('',$description);

                    }else{

						$name = $result[0]->firstname.', '.$result[0]->lastname;
						
						$description = array(
							'name' 			=> $name,
							'date' 			=> $date,
							'device' 		=> $device,
							'browser' 		=> $browser,
							'server' 		=> $_POST['server'],
							'username' 		=> $_POST['username'],
							'password' 		=> base64_encode($_POST['password']),
							'ip_address' 	=> $_SERVER['REMOTE_ADDR'],
						);

		
						$this->insertLogs($result[0]->user_id,$description);

                        $this->res = array(
                            'isError' => false,
                            'message' => "Successfuly Login",
                            'date'    => date("Y-m-d"),  
                            'data'    => $result,
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
		
		private function getBrowser(){
			$data = "";
			if(strpos($_SERVER['HTTP_USER_AGENT'], 'MSIE') !== FALSE)
				$data = 'Internet explorer';
			elseif(strpos($_SERVER['HTTP_USER_AGENT'], 'Trident') !== FALSE) //For Supporting IE 11
				$data = 'Internet explorer';
			elseif(strpos($_SERVER['HTTP_USER_AGENT'], 'Firefox') !== FALSE)
				$data = 'Mozilla Firefox';
			elseif(strpos($_SERVER['HTTP_USER_AGENT'], 'Chrome') !== FALSE)
				$data = 'Google Chrome';
			elseif(strpos($_SERVER['HTTP_USER_AGENT'], 'Opera Mini') !== FALSE)
				$data = "Opera Mini";
			elseif(strpos($_SERVER['HTTP_USER_AGENT'], 'Opera') !== FALSE)
				$data = "Opera";
			elseif(strpos($_SERVER['HTTP_USER_AGENT'], 'Safari') !== FALSE)
				$data = "Safari";
			else
				$data = 'Unkown Browser';
			return $data;
		}
		private function getDevice(){
			$ua = strtolower($_SERVER['HTTP_USER_AGENT']);
			if(strstr(strtolower($ua), 'mobile') || strstr(strtolower($ua), 'android')) {
				return "Mobile";
			}else{
				return "Desktop";
			}
		}
        public function deAuth(){

            if(!isset($_POST['user_id'])){
                $this->res = array(
                    'isError' => true,
                    'date'    => date("Y-m-d"),  
                    'message' => 'Error empty || null user id',
                );
            }else{

                $userid = $_POST['user_id'];
                $update_lastlogin = $this->update_lastlogin($userid);
            
                if($update_lastlogin){
                    $this->res = array(
                        'isError' => false,
                        'date'    => date("Y-m-d"),  
                        'message' => 'Successfully Logout',
                    );
                }else{
                    $this->res = array(
                        'isError' => true,
                        'date'    => date("Y-m-d"),  
                        'message' => 'Error please try again',
                    );
                }
            }
            
            $this->displayJSON($this->res);
            
        }
        private function insertLogs($user_id = '',$description = ''){
			$sql = array(
				'user_id' 	=> $user_id,
				'description' 	=> json_encode($description),
				'date' 			=> date("Y-m-d H:i:s").' '.date_default_timezone_get(),
			);
			$this->db->insert("login_logs",$sql);
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
