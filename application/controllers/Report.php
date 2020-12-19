
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
        public function grand_summary()
		{

			$data['title']				= "Dashboard";
			$data['user'] 				= $this->dashboardmodel->get_user();
			$data['loanbydistrict'] 	= $this->dashboardmodel->getloanbydistrict();
			$data['payment'] 			= $this->dashboardmodel->getPayment();
			$data['cashin'] 			= $this->vaultmodel->getTotalCashin();
			$data['bank_amount'] 		= $this->dashboardmodel->bank_amount();
			$data['vault'] 		 		= $this->vaultmodel->get_vault_balance();
			$data['expense'] 		 	= $this->dashboardmodel->get_total_expense();
			$data['interest_earn'] 		= $this->dashboardmodel->get_total_interest();
			$data['released_incentives'] = $this->dashboardmodel->get_released_incentives_grand();
			// print_r($data['interest_earn']);exit;
			$this->load->view('dashboard/header/header',$data);
			if($_SESSION['usertype'] == 1 || $_SESSION['usertype'] == 2){
                $this->load->view('dashboard/report/grand_summary');
			}else{
                $this->load->view('notallowed');
            }
			
			$this->load->view('dashboard/footer/footer');
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