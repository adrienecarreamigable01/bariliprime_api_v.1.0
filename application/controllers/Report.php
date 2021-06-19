
        <?php
    /**
     * @author  Adriene Care Llanos Amigable <adrienecarreamigable01@gmail.com>
     * @version 0.1.0
    */ 
    class report extends CI_Controller{
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
        public function paymentReport()
		{
            try {
                
                $from_date 	= isset($_GET['from']) ? $_GET['from'] : date("Y-m-d");
                $to_date 	= isset($_GET['to']) ? $_GET['to'] : date("Y-m-d");
                
                $sql = "SELECT  payment.amount,
                                loan.loan_product_id,
                                payment.payment_date,
                                payment.invoice_number,
                                payment_mode.name as mode,
                                payment_type.payment_type as type,
                                loan_product.name as loan_product,
                                payment.payment_category,
                            CONCAT(user.lastname,', ',user.firstname) as name,
                            CONCAT(borrower.lastname,', ',borrower.firstname) as borrower
                        FROM payment
                        LEFT JOIN payment_mode 	ON payment_mode.payment_mode_id = payment.payment_mode_id
                        LEFT JOIN payment_type 	ON payment_type.payment_type_id = payment.payment_type_id
                        LEFT JOIN user 			ON user.user_id 				= payment.user_id
                        LEFT JOIN borrower 		ON borrower.borrower_id    		= payment.borrower_id
                        LEFT JOIN loan 			ON loan.loan_id 				= payment.loan_id 
                        LEFT JOIN loan_product 	ON loan_product.loan_product_id = loan.loan_product_id 
                        WHERE payment.payment_date BETWEEN '$from_date' AND '$to_date' AND loan.status_id IN(1,5) AND payment.payment_type_id IN(2,4)";
                $data = $this->db->query($sql)->result();

                $this->res = array(
                    'isError'   => false,
                    'data'      => $data,
                    'message'   => "Success",
                    'date'      => date("Y-m-d"),  
                );
                // return $sql;
            }
            catch(Exception $e){
                $this->res = array(
                    'isError'   => true,
                    'message'   => $e->getMessage(),
                    'date'      => date("Y-m-d"),  
                );
            }

			$this->displayJSON($this->res);
		}
        public function monthlyinterestPaymentReport()
		{
            try {

                $from_date 	= isset($_GET['from']) ? $_GET['from'] : date("Y-m-d");
                $to_date 	= isset($_GET['to']) ? $_GET['to'] : date("Y-m-d");
                
                $sql = "SELECT  payment.amount,
							loan.loan_product_id,
							payment.payment_date,
							payment.invoice_number,
							payment_mode.name as mode,
							payment_type.payment_type as type,
							loan_product.name as loan_product,
							payment.payment_category,
						   CONCAT(user.lastname,', ',user.firstname) as name,
						   CONCAT(borrower.lastname,', ',borrower.firstname) as borrower
					FROM payment
					LEFT JOIN payment_mode 	ON payment_mode.payment_mode_id = payment.payment_mode_id
					LEFT JOIN payment_type 	ON payment_type.payment_type_id = payment.payment_type_id
					LEFT JOIN user 			ON user.user_id 				= payment.user_id
					LEFT JOIN borrower 		ON borrower.borrower_id    		= payment.borrower_id
					LEFT JOIN loan 			ON loan.loan_id 				= payment.loan_id 
					LEFT JOIN loan_product 	ON loan_product.loan_product_id = loan.loan_product_id 
					WHERE payment.payment_date BETWEEN '$from_date' AND '$to_date' AND loan.status_id IN(1,5) AND payment.payment_type_id IN(1)";
                $data = $this->db->query($sql)->result();

                $this->res = array(
                    'isError'   => false,
                    'data'      => $data,
                    'message'   => "Success",
                    'date'      => date("Y-m-d"),  
                );
                // return $sql;
            }
            catch(Exception $e){
                $this->res = array(
                    'isError'   => true,
                    'message'   => $e->getMessage(),
                    'date'      => date("Y-m-d"),  
                );
            }

			$this->displayJSON($this->res);
		}
        public function getLoanReports()
		{
            try {

                $from_date 	= isset($_GET['from']) ? $_GET['from'] : date("Y-m-d");
                $to_date 	= isset($_GET['to']) ? $_GET['to'] : date("Y-m-d");

                $sql = "SELECT loan.loan_id,
                        loan.loan_product_id,
                        loan.borrower_id,
                        loan.principal_amount,
                        (loan.total_amount - (SELECT SUM(amount) as total FROM payment WHERE payment.loan_id = loan.loan_id AND payment.is_void = 0 AND payment.status = 1 AND payment.payment_type_id IN(2,3,4))) as remaining,
                        loan.released_date,
                        loan.interest,
                        loan.term,
                    loan.description,
                    loan.due_date,
                    loan.date_start,
                    loan.processing_fee,
                    loan.total_amount,
                    loan.monthly_payment,
                    loan.is_released,
                    loan.loan_category_id,
                    loan_product.name as loan_product,
                    loan.status_id,
                    status.name as status,
                    status.color,
                    CONCAT(borrower.firstname,' ',borrower.lastname) as Name,
                    CONCAT(user.lastname,', ',user.firstname) as transact_by_name,
                    borrower.firstname,borrower.lastname,borrower.middlename,
                    borrower.image,
                    borrower_contact.mobile,
                    borrower_contact.telephone,
                    borrower_contact.email,
                    borrower_details.gender,
                    borrower_details.birthdate,
                    borrower_details.present_address,
                    borrower_details.position,
                    borrower_details.id_name,
                    borrower_details.id_number,
                    district.name as 'district_name',
                    loan.credit_line,
                    loan_category.loan_category_name
                FROM loan
                LEFT JOIN user 			 	ON user.user_id 				= loan.transact_by
                LEFT JOIN borrower 			ON borrower.borrower_id 		= loan.borrower_id
                LEFT JOIN borrower_contact 	ON borrower_contact.borrower_id = borrower.borrower_id
                LEFT JOIN borrower_details 	ON borrower_details.borrower_id = borrower.borrower_id
                LEFT JOIN district 		   	ON district.district_id 		= borrower.district_id
                LEFT JOIN loan_product 		ON loan_product.loan_product_id = loan.loan_product_id
                LEFT JOIN status 			ON status.status_id 			= loan.status_id
                LEFT JOIN loan_category 	ON loan_category.loan_category_id  = loan.loan_category_id
                WHERE 1 AND loan.released_date BETWEEN '$from_date' AND '$to_date' AND is_released = 1 ";

                if(isset($_GET['status'])){
                    $status =  $_GET['status'];
                    $sql .="AND loan.status_id = {$status} ";
                }else{
                    $sql .="AND loan.status_id = 1 ";
                }

                $data = $this->db->query($sql)->result();

                $this->res = array(
                    'isError'   => false,
                    'data'      => $data,
                    'message'   => "Success",
                    'date'      => date("Y-m-d"),  
                );
                // return $sql;
            }
            catch(Exception $e){
                $this->res = array(
                    'isError'   => true,
                    'message'   => $e->getMessage(),
                    'date'      => date("Y-m-d"),  
                );
            }

			$this->displayJSON($this->res);
        }
        public function getInterestEarReport()
		{
            try {

                $from_date 	= isset($_GET['from']) ? $_GET['from'] : date("Y-m-d");
                $to_date 	= isset($_GET['to']) ? $_GET['to'] : date("Y-m-d");

                $sql = "SELECT loan.loan_id,
						loan.borrower_id,
						loan.principal_amount,
						loan.loan_product_id,
						loan.interest,
                        CONCAT(borrower.lastname,', ',borrower.firstname) as borrower,
						'Loan' AS 'type',
						loan.term,
                        loan_product.name as loan_product,
                        CONCAT(user.lastname,', ',user.firstname) as name
				FROM loan 
                LEFT JOIN borrower ON borrower.borrower_id = loan.borrower_id
                LEFT JOIN loan_product 		ON loan_product.loan_product_id = loan.loan_product_id
                LEFT JOIN user 			 	ON user.user_id 				= loan.transact_by
				WHERE loan.status_id IN(1,5) AND loan.is_released = 1 AND loan.is_active = 1 AND loan.released_date BETWEEN '$from_date' AND '$to_date'
				UNION
				SELECT  loan_add_capital.loan_add_capital_id as capital_id,
						loan.borrower_id,
						loan_add_capital.amount,
						loan.loan_product_id,
						loan_add_capital.interest_rate,
                        CONCAT(borrower.lastname,', ',borrower.firstname) as borrower,
						'Capital' AS 'type',
						loan_add_capital.term,
                        loan_product.name as loan_product,
                        CONCAT(user.lastname,', ',user.firstname) as name
				FROM loan_add_capital
				LEFT JOIN loan ON loan.loan_id = loan_add_capital.loan_id
                LEFT JOIN borrower ON borrower.borrower_id = loan.borrower_id
                LEFT JOIN loan_product 		ON loan_product.loan_product_id = loan.loan_product_id
                LEFT JOIN user 			 	ON user.user_id 				= loan_add_capital.transact_by
				WHERE loan_add_capital.status_id IN(1,5) AND loan_add_capital.is_released = 1 and loan.is_active = 1 AND loan_add_capital.released_date BETWEEN '$from_date' AND '$to_date'";

                $data = $this->db->query($sql)->result();

                $this->res = array(
                    'isError'   => false,
                    'data'      => $data,
                    'message'   => "Success",
                    'date'      => date("Y-m-d"),  
                );
                // return $sql;
            }
            catch(Exception $e){
                $this->res = array(
                    'isError'   => true,
                    'message'   => $e->getMessage(),
                    'date'      => date("Y-m-d"),  
                );
            }

			$this->displayJSON($this->res);
        }
        public function borrowerSalaryReport()
		{
            try {

                $date 	        = isset($_GET['date']) ? date("Y-m",strtotime($_GET['date'])) : date("Y-m-d");
                $district_id 	= isset($_GET['district_id']) ? $_GET['district_id'] : 1;

                $sql = "SELECT borrower_salary.borrower_salary_id,
                            borrower_salary.salary_date,
                            borrower_salary.amount,
                            borrower_salary.date_transact,
                        CONCAT(user.lastname,' ',user.firstname) as name,
                        CONCAT(borrower.lastname,', ',borrower.firstname,' ',borrower.middlename) as borrower,
                        district.name as 'district_name'
                    FROM borrower_salary
                    LEFT JOIN user 	ON user.user_id = borrower_salary.transact_by
                    LEFT JOIN borrower 	ON borrower.borrower_id = borrower_salary.borrower_id
                    LEFT JOIN district 		   ON district.district_id = borrower.district_id
                    WHERE borrower_salary.is_void = 0 AND borrower.district_id = '{$district_id}' AND borrower_salary.date_value = '$date'";

                $data = $this->db->query($sql)->result();

                $this->res = array(
                    'isError'   => false,
                    'data'      => $data,
                    'message'   => "Success",
                    'date'      => date("Y-m-d"),  
                );
                // return $sql;
            }
            catch(Exception $e){
                $this->res = array(
                    'isError'   => true,
                    'message'   => $e->getMessage(),
                    'date'      => date("Y-m-d"),  
                );
            }

			$this->displayJSON($this->res);
        }
        public function cashierReport()
		{

            if(!empty($_GET['user_id'])){
                try {

                    $date 	        = isset($_GET['date']) ? date("Y-m-d",strtotime($_GET['date'])) : date("Y-m-d");
                    $user_id 	    = $_GET['user_id'];

                    $sql = "SELECT
                        cashier_vault.cashier_vault_id as id,
                        cashier_vault.amount,
                        cashier_vault.date_added,
                        cashier_vault.description,
                        cashier_vault.assign_id,
                        trasaction_type.trasaction_type as type,
                        trasaction_type.trasaction_type_id,
                        trasaction_type.color,
                        cashier_vault.transaction_type_id as trasaction_type_id,
                        CONCAT(user.lastname,', ',user.firstname) as trasnsactby
                    FROM cashier_vault
                    LEFT JOIN user ON user.user_id = cashier_vault.user_id
                    LEFT JOIN trasaction_type ON trasaction_type.trasaction_type_id = cashier_vault.transaction_type_id
                    WHERE cashier_vault.is_active = 1 AND cashier_vault.assign_id = {$user_id} AND DATE_FORMAT(cashier_vault.date_added,'%Y-%m-%d') = '{$date}'";

                    $data = $this->db->query($sql)->result();

                    $this->res = array(
                        'isError'   => false,
                        'data'      => $data,
                        'message'   => "Success",
                        'date'      => date("Y-m-d"),  
                    );
                    // return $sql;
                }
                catch(Exception $e){
                    $this->res = array(
                        'isError'   => true,
                        'message'   => $e->getMessage(),
                        'date'      => date("Y-m-d"),  
                    );
                }
            }else{
                $this->res = array(
                    'isError'   => true,
                    'message'   => 'Empty user',
                    'date'      => date("Y-m-d"),  
                );
            }

			$this->displayJSON($this->res);
        }
        public function borrowerIncentiveReport()
		{
            try {

                $date 	        = isset($_GET['date']) ? date("Y-m",strtotime($_GET['date'])) : date("Y-m-d");
                $district_id 	= isset($_GET['district_id']) ? $_GET['district_id'] : 1;

                $sql = "SELECT borrower_incentives.incentives_id,
                            borrower_incentives.incentive_date,
                            borrower_incentives.amount,
                            borrower_incentives.date_transact,
                            loan_product.name as loan_product,
                        CONCAT(user.lastname,' ',user.firstname) as name,
                        CONCAT(borrower.lastname,', ',borrower.firstname,' ',borrower.middlename) as borrower,
                        district.name as 'district_name'
                    FROM borrower_incentives
                    LEFT JOIN user 	        ON user.user_id                        = borrower_incentives.transact_by
                    LEFT JOIN borrower 	    ON borrower.borrower_id                = borrower_incentives.borrower_id
                    LEFT JOIN district 		ON district.district_id                = borrower.district_id
                    LEFT JOIN loan_product  ON borrower_incentives.loan_product_id = loan_product.loan_product_id
                    WHERE borrower_incentives.is_void = 0 AND borrower.district_id = '{$district_id}' AND borrower_incentives.date_value = '$date'";

                $data = $this->db->query($sql)->result();

                $this->res = array(
                    'isError'   => false,
                    'data'      => $data,
                    'message'   => "Success",
                    'date'      => date("Y-m-d"),  
                );
                // return $sql;
            }
            catch(Exception $e){
                $this->res = array(
                    'isError'   => true,
                    'message'   => $e->getMessage(),
                    'date'      => date("Y-m-d"),  
                );
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