
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
            }else if(empty($_POST['password'])){
                $this->res = array(
                    'isError' => true,
                    'date'    => date("Y-m-d"),  
                    'message' => 'Password is required',
                );
            }else{

                try{

                    $username = $_POST['username'];
                    $password = base64_encode($_POST['password']);
    
                    $sql = "SELECT user.user_id,
                                    user.id_number,
                                    user.usertype_id ,
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
                            'message' => "Account not found",
                        );
                    }else{
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