
<?php
    /**
     * @author  Adriene Care Llanos Amigable <adrienecarreamigable01@gmail.com>
     * @version 0.1.0
    */ 
    class loan extends CI_Controller{
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
        public function all(){
  
            try{


                $sql = "SELECT loan.loan_id, 
                        district.name as district,
                        CONCAT(borrower.lastname,' ,',borrower.firstname,' ',borrower.middlename) as borrower,
                        loan_product.name as loan_product,
                        loan.borrower_id, 
                        loan.transact_by, 
                        loan.transact_date, 
                        loan.loan_product_id, 
                        loan.principal_amount,
                        (SELECT 
                            CASE WHEN SUM(payment.amount) > 0 
                                THEN SUM(payment.amount) 
                                ELSE 0 END FROM payment 
                                WHERE payment.loan_id = loan.loan_id AND payment.payment_type_id = 2) as `total_paid`,
                        (SELECT 
                            CASE WHEN SUM(loan_add_capital.amount) > 0 
                                THEN SUM(loan_add_capital.amount) 
                            ELSE 0 END FROM loan_add_capital 
                            WHERE loan_add_capital.status_id = 1 
                            AND loan_add_capital.is_released = 1 
                            AND loan_add_capital.is_void = 0 
                            AND loan_add_capital.loan_id = loan.loan_id
                        ) as `added_capital`,
                        -- loan.principal_amount - total_paid as balance, 
                        loan.loan_type, 
                        loan.total_amount, 
                        loan.released_amount, 
                        loan.monthly_payment, 
                        loan.interest, 
                        loan.interest_amount, 
                        loan.term, 
                        loan.description, 
                        loan.due_date, 
                        loan.date_start, 
                        loan.processing_fee, 
                        loan.status_id, 
                        loan.is_active, 
                        loan.void_date, 
                        loan.note, 
                        loan.is_released, 
                        loan.is_reconstruct, 
                        loan.is_reloan, 
                        loan.reloan_from, 
                        loan.released_by, 
                        loan.released_date, 
                        loan.date_fully_paid, 
                        loan.loan_category_id, 
                        loan.date_modify, 
                        loan.modify_by, 
                        loan.deficit_amounts, 
                        loan.date_added, 
                        loan.net, 
                        loan.credit_line,
                        status.name as status
                        FROM loan 
                        LEFT JOIN loan_product ON loan_product.loan_product_id = loan.loan_product_id
                        LEFT JOIN borrower ON loan.borrower_id = borrower.borrower_id
                        LEFT JOIN district ON district.district_id = borrower.district_id
                        LEFT JOIN status 		ON status.status_id = loan.status_id
                        WHERE loan.is_active = 1 ";

                    if(!empty($_GET['status_id'])){
                        $status = $_GET['status_id'];
                        $sql .= " AND loan.status_id IN($status)";
                    }

                    if(!empty($_GET['borrower_id'])){
                        $sql .= " AND loan.borrower_id = '{$_GET['borrower_id']}'";
                    }

                    if(!empty($_GET['loan_id'])){
                        $sql .= " AND loan.loan_id = '{$_GET['loan_id']}'";
                    }

                    if(!empty($_GET['item'])){
                        $item = $_GET['item'];
                        $sql .= " AND borrower.firstname LIKE '%{$item}%' 
                                  OR borrower.lastname LIKE '%{$item}%'
                                  OR borrower.middlename LIKE '%{$item}%'
                                  OR district.name LIKE '%{$item}%'
                                  OR status.name LIKE '%{$item}%' ";
                    }

                    $sql .= " ORDER BY borrower.borrower_id ASC";
                    // print_r($sql);exit;
                $result = $this->db->query($sql)->result();

                $this->res = array(
                    'isError' => false,
                    'date'    => date("Y-m-d"),  
                    'data'    => $result,
                );
            }catch(Exception $e) {
                $this->res = array(
                    'isError' => true,
                    'message'   => $e->getMessage(),
                    'date'    => date("Y-m-d"),  
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


        /*
            * Add Borrower api
            * This will add new borrower to online database
        */
        public function add_borrower(){
            try{
                $fcpath = FCPATH;
                if(!empty($_FILES['borrower-image']['name'])){
                    $image = md5(mt_rand());
                    $ext = pathinfo($_FILES['borrower-image']['name'], PATHINFO_EXTENSION);
                    $data = $this->dashboardmodel->proc_add_borrower($image.'.'.$ext);
                    if($data){
                        // $path = $fcpath.'uploads/'.$data.'/';
                        $path = $this->getPath().'/uploads/'.$data.'/';
                        /* Cheack if file exist */
                        if (!file_exists($path)) {
                            mkdir($path, 0777, true);
                        }
                        /* Create updaload image */
                        $update_result = $this->upload('borrower-image', $image.'.'.$ext,$path , 323);
                        // check if the update return is successfuly executed
                        if($update_result){
                            $this->res = array(
                                'isError' => false,
                                'message' => 'Successfuly added new user',
                                'date'    => date("Y-m-d"),  
                            );
                        }else{
                            $this->res = array(
                                'isError' => false,
                                'message' => 'Successfuly added new user',
                                'date'    => date("Y-m-d"),  
                            );
                        }

                    }else{
                        $this->res = array(
                            'isError' => true,
                            'message' => 'Error adding new user',
                            'date'    => date("Y-m-d"),  
                        );
                    }
                    
                }
                else{
                    $fileName = uniqid().'.png';
                    $data = $this->borrowermodel->proc_add_borrower($fileName);
                    if($data){
                        // check if the update return is successfuly executed
                        $upload_result = $this->imageupload_local($fileName,$data);
                        if($upload_result){
                            $this->res = array(
                                'isError' => false,
                                'message' => 'Successfuly added new user',
                                'date'    => date("Y-m-d"),  
                            );
                        }else{
                            $this->res = array(
                                'isError' => false,
                                'message' => 'Successfuly added new user',
                                'date'    => date("Y-m-d"),  
                            );
                        }
                    }else{
                        $this->res = array(
                            'isError' => true,
                            'message' => 'Error adding new user',
                            'date'    => date("Y-m-d"),  
                        );
                    }
                    
                }
            }catch(Exception $e) {
                $this->res = array(
                    'isError' => true,
                    'message'   => $e->getMessage(),
                    'date'    => date("Y-m-d"),  
                );
            }
            /* Return the response */
            $this->displayJSON($this->res);
        }
        public function active_borrower(){
            try{
                $sql = "SELECT 
                        borrower.borrower_id,
                        borrower.firstname,
                        borrower.lastname,
                        borrower.middlename,
                        CONCAT(borrower.firstname,' ',borrower.lastname) as fullname,
                        borrower.image,
                        borrower.district_id,
                        borrower.date_registered,
                        borrower_details.borrower_details_id,
                        borrower_details.gender	,
                        borrower_details.birthdate,
                        borrower_details.present_address,
                        borrower_details.id_name,
                        borrower_details.id_number,
                        borrower_details.position,
                        borrower_details.income,
                        borrower_details.gross,
                        borrower_details.net,
                        borrower_details.others_id,
                        borrower_contact.borrower_contact_id,
                        borrower_contact.mobile,
                        borrower_contact.telephone,
                        borrower_contact.email
                    FROM borrower
                    LEFT JOIN borrower_contact ON borrower.borrower_id = borrower_contact.borrower_id
                    LEFT JOIN borrower_details ON borrower.borrower_id = borrower_details.borrower_id
                    WHERE borrower.is_active = 1 AND borrower_contact.email != ''";
                    $result = $this->db->query($sql)->result();
                    $this->res = array(
                        'isError' => false,
                        'date'    => date("Y-m-d"),  
                        'data'    => $result,
                    );
            }catch(Exception $e) {
                $this->res = array(
                    'isError' => true,
                    'message'   => $e->getMessage(),
                    'date'    => date("Y-m-d"),  
                );
            }
            
            $this->displayJSON($this->res);
        }
        public function active_borrower_account(){
            try{
                $sql = "SELECT 
                        borrower.borrower_id,
                        borrower.firstname,
                        borrower.lastname,
                        borrower.middlename,
                        CONCAT(borrower.firstname,' ',borrower.lastname) as fullname,
                        borrower.image,
                        borrower.district_id,
                        borrower_account.is_verified,
                        borrower_account.username
                    FROM borrower_account
                    LEFT JOIN borrower ON borrower.borrower_id = borrower_account.borrower_id
                    LEFT JOIN borrower_contact ON borrower.borrower_id = borrower_contact.borrower_id
                    LEFT JOIN borrower_details ON borrower.borrower_id = borrower_details.borrower_id
                    WHERE borrower.is_active = 1 ";
                    
                    if(!empty($_POST['district_id']) && $_POST['district_id'] != ""){
                        $district = $_POST['district_id'];
                        $sql .= "AND borrower.district_id = '{$district}' ";
                    }
                    
                    if(!empty($_POST['is_verified']) && $_POST['is_verified'] != ""){
                        $is_verified = $_POST['is_verified'];
                        $sql .= "AND borrower_account.is_verified = {$is_verified} ";
                    }

                    $result = $this->db->query($sql)->result();

                    $this->res = array(
                        'isError' => false,
                        'date'    => date("Y-m-d"),  
                        'data'    => $result,
                    );
            }catch(Exception $e) {
                $this->res = array(
                    'isError' => true,
                    'message'   => $e->getMessage(),
                    'date'    => date("Y-m-d"),  
                );
            }
            
            $this->displayJSON($this->res);
        }
        public function get_borrowers_to_email(){
            try{
                $sql = "SELECT borrower.borrower_id,
                        CONCAT(borrower.firstname,' ',borrower.lastname) as fullname,
                        borrower_details.gender,
                        borrower_contact.email
                    FROM borrower
                    LEFT JOIN borrower_contact ON borrower.borrower_id = borrower_contact.borrower_id
                    LEFT JOIN borrower_details ON borrower.borrower_id = borrower_details.borrower_id
                    WHERE borrower.is_active = 1 AND borrower_contact.email != ''";
                    $result = $this->db->query($sql)->result();
                    $this->res = array(
                        'isError' => false,
                        'date'    => date("Y-m-d"),  
                        'data'    => $result,
                    );
            }catch(Exception $e) {
                $this->res = array(
                    'isError' => true,
                    'message'   => $e->getMessage(),
                    'date'    => date("Y-m-d"),  
                );
            }
            
            $this->displayJSON($this->res);
        }
        public function get_borrowers_with_loan(){
            try{
                $sql = "SELECT loan.loan_id, 
                        district.name as district,
                        CONCAT(borrower.lastname,' ,',borrower.firstname,' ',borrower.middlename) as borrower,
                        loan_product.name as loan_product,
                        loan.borrower_id, 
                        loan.transact_by, 
                        loan.transact_date, 
                        loan.loan_product_id, 
                        loan.principal_amount,
                        (SELECT 
                            CASE WHEN SUM(payment.amount) > 0 
                                THEN SUM(payment.amount) 
                                ELSE 0 END FROM payment 
                                WHERE payment.loan_id = loan.loan_id AND payment.payment_type_id = 2) as `total_paid`,
                        (SELECT 
                            CASE WHEN SUM(loan_add_capital.amount) > 0 
                                THEN SUM(loan_add_capital.amount) 
                            ELSE 0 END FROM loan_add_capital 
                            WHERE loan_add_capital.status_id = 1 
                            AND loan_add_capital.is_released = 1 
                            AND loan_add_capital.is_void = 0 
                            AND loan_add_capital.loan_id = loan.loan_id
                        ) as `added_capital`,
                        -- loan.principal_amount - total_paid as balance, 
                        loan.loan_type, 
                        loan.total_amount, 
                        loan.released_amount, 
                        loan.monthly_payment, 
                        loan.interest, 
                        loan.interest_amount, 
                        loan.term, 
                        loan.description, 
                        loan.due_date, 
                        loan.date_start, 
                        loan.processing_fee, 
                        loan.status_id, 
                        loan.is_active, 
                        loan.void_date, 
                        loan.note, 
                        loan.is_released, 
                        loan.is_reconstruct, 
                        loan.is_reloan, 
                        loan.reloan_from, 
                        loan.released_by, 
                        loan.released_date, 
                        loan.date_fully_paid, 
                        loan.loan_category_id, 
                        loan.date_modify, 
                        loan.modify_by, 
                        loan.deficit_amounts, 
                        loan.date_added, 
                        loan.net, 
                        loan.credit_line
                        FROM loan 
                        LEFT JOIN loan_product ON loan_product.loan_product_id = loan.loan_product_id
                        LEFT JOIN borrower ON loan.borrower_id = borrower.borrower_id
                        LEFT JOIN district ON district.district_id = borrower.district_id
                        WHERE loan.is_active = 1 AND loan.status_id IN(1,2)
                        ORDER BY borrower.borrower_id";
                    $result = $this->db->query($sql)->result();
                    $this->res = array(
                        'isError' => false,
                        'message' => 'Success',
                        'date'    => date("Y-m-d"),  
                        'data'    => $result,
                    );
            }catch(Exception $e) {
                $this->res = array(
                    'isError' => true,
                    'message'   => $e->getMessage(),
                    'date'    => date("Y-m-d"),  
                );
            }
            
            $this->displayJSON($this->res);
        }
        public function get_borrowers_with_capital(){
            try{
                $sql = "SELECT loan_add_capital.loan_add_capital_id, 
                        district.name as district,
                        CONCAT(borrower.lastname,' ,',borrower.firstname,' ',borrower.middlename) as borrower,
                        loan_add_capital.amount,
                        loan_add_capital.released_amount,
                        loan_add_capital.interest_rate,
                        loan_add_capital.loan_id,
                        loan_add_capital.loan_cashadvance_id,
                        loan_add_capital.transact_date,
                        loan_add_capital.transact_by,
                        loan_add_capital.processing_fee,
                        loan_add_capital.released_by,
                        loan_add_capital.released_date,
                        loan_add_capital.is_released,
                        loan_add_capital.is_void,
                        loan_add_capital.void_date,
                        loan_add_capital.note,
                        loan_add_capital.monthly_payment,
                        loan_add_capital.due_date,
                        loan_add_capital.net_interest,
                        loan_add_capital.deficit_amount,
                        loan_add_capital.term,
                        loan_add_capital.status_id
                        FROM loan_add_capital
                        LEFT JOIN loan ON loan_add_capital.loan_id = loan.loan_id
                        LEFT JOIN loan_product ON loan_product.loan_product_id = loan.loan_product_id
                        LEFT JOIN borrower ON loan.borrower_id = borrower.borrower_id
                        LEFT JOIN district ON district.district_id = borrower.district_id
                        WHERE loan_add_capital.status_id IN(1,2) AND loan_add_capital.is_released = 1 AND loan_add_capital.is_void = 0 
                        ORDER BY borrower.borrower_id";
                    $result = $this->db->query($sql)->result();
                    $this->res = array(
                        'isError' => false,
                        'message' => 'Success',
                        'date'    => date("Y-m-d"),  
                        'data'    => $result,
                    );
            }catch(Exception $e) {
                $this->res = array(
                    'isError' => true,
                    'message'   => $e->getMessage(),
                    'date'    => date("Y-m-d"),  
                );
            }
            
            $this->displayJSON($this->res);
        }
        public function get_all_payments(){
            try{
                $sql = "SELECT * FROM payment";
                $result = $this->db->query($sql)->result();
                $this->res = array(
                    'isError' => false,
                    'message' => 'Success',
                    'date'    => date("Y-m-d"),  
                    'data'    => $result,
                );
            }catch(Exception $e) {
                $this->res = array(
                    'isError' => true,
                    'message'   => $e->getMessage(),
                    'date'    => date("Y-m-d"),  
                );
            }
            $this->displayJSON($this->res);
        }
        public function activateAccount(){
           
            if(!empty($_POST['borrower_id'])){
                $borrower_id = $_POST['borrower_id'];
                try{
                    $result = $this->borrowermodel->activateAccount($borrower_id);
                    if($result){
                        $this->res = array(
                            'isError' => false,
                            'message' => 'Successfuly activate account',
                            'date'    => date("Y-m-d"),  
                            'data'    => $result,
                        );
                    }else{
                        $this->res = array(
                            'isError' => true,
                            'message'   => "Error activating account! Please contact IT",
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
            }else{
                $this->res = array(
                    'isError' => true,
                    'message'   => "Please input borrower id",
                    'date'    => date("Y-m-d"),  
                );
            }
            $this->displayJSON($this->res);
        }
        public function overRideBorrowerPassword(){
           
            if(empty($_POST['borrower_id'])){
                $this->res = array(
                    'isError' => true,
                    'message'   => "Please input borrower id",
                    'date'    => date("Y-m-d"),  
                );
            }
            else if(empty($_POST['user'])){
                $this->res = array(
                    'isError' => true,
                    'message'   => "Empty user",
                    'date'    => date("Y-m-d"),  
                );
            }
            else{
                $borrower_id = $_POST['borrower_id'];
                $user        = $_POST['user'];
                try{
                    $result = $this->borrowermodel->overRideBorrowerPassword($borrower_id,$user);
                    if($result){
                        $this->res = array(
                            'isError' => false,
                            'message' => 'Successfuly Override Password',
                            'date'    => date("Y-m-d"),  
                            'data'    => $result,
                        );
                    }else{
                        $this->res = array(
                            'isError' => true,
                            'message'   => "Error activating account! Please contact IT",
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
        public function changePassword(){
            if(empty($_POST['borrower_id'])){
                $this->res = array(
                    'isError' => true,
                    'message'   => "Please input borrower id",
                    'date'    => date("Y-m-d"),  
                );
            }
            else if(empty($_POST['password'])){
                $this->res = array(
                    'isError' => true,
                    'message'   => "Empty password",
                    'date'    => date("Y-m-d"),  
                );
            }
            else if(empty($_POST['oldpassword'])){
                $this->res = array(
                    'isError' => true,
                    'message'   => "Empty Old Password",
                    'date'    => date("Y-m-d"),  
                );
            }
            else{
                $borrower_id    = $_POST['borrower_id'];
                $password       = $_POST['password'];
                $oldpassword     = $_POST['oldpassword'];
                $checkOldPassword = $this->borrowermodel->checkOldPassword($borrower_id,$oldpassword);
                if( count($checkOldPassword) == 0 ){
                    $this->res = array(
                        'isError' => true,
                        'message'   => "Invalid old password",
                        'date'    => date("Y-m-d"),  
                    );
                }
                else if( $oldpassword ==  $password ){
                    $this->res = array(
                        'isError' => true,
                        'message'   => "Cant use thesame password, Old password is same as the new password",
                        'date'    => date("Y-m-d"),  
                    );
                }
                else{
                    try{
                        $result = $this->borrowermodel->changePassword($borrower_id,$password);
                        if($result){
                            $this->res = array(
                                'isError' => false,
                                'message' => 'Successfuly Change Password',
                                'date'    => date("Y-m-d"),  
                                'data'    => $result,
                            );
                        }else{
                            $this->res = array(
                                'isError' => true,
                                'message'   => "Error activating account! Please contact IT",
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
            }
            $this->displayJSON($this->res);
        }
        public function changeUserName(){
            if(empty($_POST['borrower_id'])){
                $this->res = array(
                    'isError' => true,
                    'message'   => "Please input borrower id",
                    'date'    => date("Y-m-d"),  
                );
            }
            else if(empty($_POST['username'])){
                $this->res = array(
                    'isError' => true,
                    'message'   => "Empty password",
                    'date'    => date("Y-m-d"),  
                );
            }
            else{
                $borrower_id    = $_POST['borrower_id'];
                $username       = $_POST['username'];
                $checkOldPassword = $this->borrowermodel->checkOldUsername($borrower_id,$username);
                if( count($checkOldPassword) == 1 ){
                    $this->res = array(
                        'isError' => true,
                        'message'   => "Cant use thesame username, Old username is same as the new username",
                        'date'    => date("Y-m-d"),  
                    );
                }
                else{
                    try{
                        $result = $this->borrowermodel->changeUsername($borrower_id,$username);
                        if($result){
                            $this->res = array(
                                'isError' => false,
                                'message' => 'Successfuly Change Username',
                                'date'    => date("Y-m-d"),  
                                'data'    => $result,
                            );
                        }else{
                            $this->res = array(
                                'isError' => true,
                                'message'   => "Error activating account! Please contact IT",
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
            }
            $this->displayJSON($this->res);
        }
        public function syncAccount(){
            $transQuery = array();

            $sql = "SELECT borrower.borrower_id, 
                    CONCAT(borrower.lastname,' ,',borrower.firstname,' ',borrower.middlename) as name,
                    borrower.lastname,
                    borrower.firstname,
                    borrower.middlename,
                    borrower_contact.email,
                    borrower_details.gender
                    FROM borrower
                    LEFT JOIN borrower_contact ON borrower_contact.borrower_id = borrower.borrower_id
                    LEFT JOIN borrower_details ON borrower_details.borrower_details_id = borrower.borrower_id
                    WHERE borrower.borrower_id NOT IN(SELECT borrower_account.borrower_id FROM borrower_account) AND borrower_contact.email IS NOT NULL AND borrower_contact.email != ''";
            $data =  $this->db->query($sql)->result();
            if(!empty($data)){
                foreach ($data as $key => $value) {
                    $payload = array(
                        'borrower_id'   => $value->borrower_id,
                        'username'      => $value->email,
                        'password'      => $value->lastname,
                    );
                    $res = "INSERT INTO `borrower_account` (`borrower_id`, `username`, `password`, `last_login`, `is_verified`) 
                            VALUES ('{$value->borrower_id}', '{$value->email}', '{$value->lastname}', current_timestamp(), '0')";
                    array_push($transQuery,$res);
                }
                $result = array_filter($transQuery);   
                $res = $this->mysqlTQ($result);
                if($res){
                    $this->res =  array(
                        'isError' => true,
                        'message' => "Successfuly Sync Account"
                    );
                }else{
                    $this->res =  array(
                        'isError' => true,
                        'message' => "Error sync account"
                    );
                }
            }else{
                $this->res =  array(
                    'isError' => false,
                    'message' => "No user to be sync"
                );
            }
    
            $this->displayJSON($this->res);
            
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