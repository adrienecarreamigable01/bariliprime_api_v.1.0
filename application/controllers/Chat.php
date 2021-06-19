<?php
class Chat extends CI_Controller{

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

        if(empty($_GET['user_id'])){
            $this->res = array(
				'isError' => true,
				'message'   => "Please enter user_id",
				'date'    => date("Y-m-d"),  
			);

        }else{
            $userId = $_GET['user_id'];
            $sql    = "SELECT
							*
						FROM
							chat_messages
						WHERE
							find_in_set({$userId}, cast(chat_messages._users as char)) > 0";

            $data   = $this->db->query($sql)->result();

			$this->res = array(
				'isError' => false,
				'data'    => $data,  
			);
        }

        $this->displayJSON($this->res);
    }
    public function get_to_send_user(){

		if(empty($_GET['user_id'])){
			$this->res = array(
				'isError' => true,
				'message'    => 'Empty user_id',  
			);
		}else{
			$usersData = [];
			$userId = $_GET['user_id'];
			$users    = "SELECT
							chat_messages._users as user
						FROM
							chat_messages
						WHERE 1";

			if(!empty($userId)){
				$users .= " AND find_in_set({$userId}, cast(chat_messages._users as char)) > 0";
			}

			
			$users_data = $this->db->query($users)->result();


		

			foreach ($users_data as $key => $value) {
				$exploded = explode(",",$value->user);

				for ($i=0; $i < sizeof($exploded); $i++) { 
					if($exploded[$i] != $_GET['user_id']){
						array_push($usersData,$exploded[$i]);
					}
				}
				
			}



			array_push($usersData,$_GET['user_id']);

			

			$imploded = implode(",",$usersData);

			
			$user_usql = "SELECT * FROM user
					WHERE 1";

			if(!empty($imploded)){
				$user_usql .= " AND user.user_id NOT IN($imploded) AND user.is_active = 1";
			}

			$r   = $this->db->query($user_usql)->result();


			$this->res = array(
				'isError' => false,
				'data'    => $r,  
			);
			
		}

        
		

        $this->displayJSON($this->res);
    }
	private function checkIfNotThreadExist($tid){
		$sql = "SELECT chat_messages.id FROM chat_messages
				WHERE chat_messages.threadid = '{$tid}'";
		$data = $this->db->query($sql)->num_rows();
		if($data <= 0){
			return true;
		}else{
			return false;
		}
	}
    public function send(){

		
        if($this->checkIfNotThreadExist($_POST['threadId'])){

            $array    = array(
				'threadId' => $_POST['threadId'],
				'message' => $_POST['message'],
				'_users'  => $_POST['_userId'],
			);

            $data   = $this->db->insert("chat_messages",$array);

			if($data){
				$this->res = array(
					'isError' => false,
					'data'    => $data,  
				);
			}

        }else{
           
            $array    = array(
				'message' => $_POST['message'],
				'_users'  => $_POST['_userId'],
			);

			$this->db->where("threadid",$_POST['threadId'] );
            $data   = $this->db->update("chat_messages",$array);
			if($data){
				$this->res = array(
					'isError' => false,
					'data'    => $data,  
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
