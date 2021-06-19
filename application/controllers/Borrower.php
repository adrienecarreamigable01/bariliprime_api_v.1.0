
<?php
    /**
     * @author  Adriene Care Llanos Amigable <adrienecarreamigable01@gmail.com>
     * @version 0.1.0
    */ 
    class Borrower extends CI_Controller{
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

        public function count(){
            try{
                $sql = "SELECT 
                        count(borrower.borrower_id) as total
                    FROM borrower
                    LEFT JOIN borrower_contact ON borrower.borrower_id = borrower_contact.borrower_id
                    LEFT JOIN borrower_details ON borrower.borrower_id = borrower_details.borrower_id 
                    LEFT JOIN district ON district.district_id = borrower.district_id 
                    WHERE 1";

                    if(!empty($_GET['is_active'])){
                        $is_active = $_GET['is_active'];
                        $sql .= " AND borrower.is_active = {$is_active}";
                    }

                    $result = $this->db->query($sql)->result();

                    $this->res = array(
                        'isError' => false,
                        'date'    => date("Y-m-d"),  
                        'data'    => !empty($result) ? $result[0]->total : 0,
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
        public function all(){
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
                        borrower_details.hasInsured,
                        borrower_contact.borrower_contact_id,
                        borrower_contact.mobile,
                        borrower_contact.telephone,
                        borrower_contact.email,
                        district.name as district
                    FROM borrower
                    LEFT JOIN borrower_contact ON borrower.borrower_id = borrower_contact.borrower_id
                    LEFT JOIN borrower_details ON borrower.borrower_id = borrower_details.borrower_id 
                    LEFT JOIN district ON district.district_id = borrower.district_id 
                    WHERE 1";

                    if(!empty($_GET['is_active'])){
                        $is_active = $_GET['is_active'];
                        $sql .= " AND borrower.is_active = {$is_active}";
                    }
                    if(!empty($_GET['isInsured'])){
                        $isInsured = $_GET['isInsured'];
                        $sql .= " AND borrower_details.hasInsured = {$isInsured}";
                    }

                    if(!empty($_GET['item'])){
                        $item = $_GET['item'];
                        $sql .= " AND borrower.firstname LIKE '%{$item}%' 
                                  OR borrower.lastname LIKE '%{$item}%'
                                  OR borrower.middlename LIKE '%{$item}%'
                                  OR district.name LIKE '%{$item}%' ";
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
        public function toInsurance(){
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
                        borrower_details.hasInsured,
                        borrower_contact.borrower_contact_id,
                        borrower_contact.mobile,
                        borrower_contact.telephone,
                        borrower_contact.email,
                        district.name as district
                    FROM borrower
                    LEFT JOIN borrower_contact ON borrower.borrower_id = borrower_contact.borrower_id
                    LEFT JOIN borrower_details ON borrower.borrower_id = borrower_details.borrower_id 
                    LEFT JOIN district ON district.district_id = borrower.district_id 
                    WHERE 1";

                    if(!empty($_GET['is_active'])){
                        $is_active = $_GET['is_active'];
                        $sql .= " AND borrower.is_active = {$is_active}";
                    }
                    if(!empty($_GET['isInsured'])){
                        $isInsured = $_GET['isInsured'];
                        $sql .= " AND borrower_details.hasInsured = {$isInsured}";
                    }

                    if(!empty($_GET['item'])){
                        $item = $_GET['item'];
                        $sql .= " AND borrower.firstname LIKE '%{$item}%' 
                                  OR borrower.lastname LIKE '%{$item}%'
                                  OR borrower.middlename LIKE '%{$item}%'
                                  OR district.name LIKE '%{$item}%' ";
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
        public function balance(){

            $total_loan = 0;

           try{
                if( !isset($_POST['borrower_id']) ){
                    $this->res = array(
                        'isError' => true,
                        'message' => 'Please Select a Borrower',
                        'date'    => date("Y-m-d"),  
                        'data'    => "",
                    );
                }else{
                    $borrower_id = $_POST['borrower_id'];
                    $sql = "SELECT loan.principal_amount,
                        (SELECT 
                        CASE WHEN SUM(loan_add_capital.amount) > 0 
                            THEN SUM(loan_add_capital.amount) 
                        ELSE 0 END FROM loan_add_capital 
                        WHERE loan_add_capital.status_id = 1 
                        AND loan_add_capital.is_released = 1 
                        AND loan_add_capital.is_void = 0 
                        AND loan_add_capital.loan_id = loan.loan_id) as added_capital,
                        (SELECT 
                                CASE WHEN SUM(payment.amount) > 0 
                                THEN SUM(payment.amount) 
                                ELSE 0 END FROM payment 
                                WHERE payment.loan_id = loan.loan_id AND payment.payment_type_id IN(2,4) AND payment.is_void = 0 AND payment.status = 1
                        ) as total_payment
                        FROM loan
                        WHERE loan.borrower_id = {$borrower_id} AND loan.status_id = 1 AND loan.is_released = 1 AND loan.is_active = 1";

                    $result = $this->db->query($sql)->result();

                    if(!empty($result)){

                        foreach ($result as $key => $value) {
                            $total_loan += ($value->principal_amount + $value->added_capital) - $value->total_payment;
                        }

                        $this->res = array(
                            'isError' => false,
                            'message' => 'Success',
                            'date'    => date("Y-m-d"),  
                            'data'    => $total_loan,
                        );

                    }else{
                        $this->res = array(
                            'isError' => false,
                            'message' => 'Success',
                            'date'    => date("Y-m-d"),  
                            'data'    => 0,
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

            $this->displayJSON($this->res);
        }
        public function info(){
            if(empty($_GET['id'])){
                $this->res = array(
                    'isError' => true,
                    'message'   => "Empty Id",
                    'date'    => date("Y-m-d"),  
                );
            }else{
                $id = $_GET['id'];
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
                            borrower_details.atm_number,
                            borrower_details.atm_name,
                            borrower_details.gsis_number,
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
                        WHERe borrower.borrower_id = '{$id}'";
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
            }
            
            
            $this->displayJSON($this->res);
        }
        // public function info(){
        //     if(empty($_GET['id'])){
        //         $this->res = array(
        //             'isError' => true,
        //             'message'   => "Empty Id",
        //             'date'    => date("Y-m-d"),  
        //         );
        //     }else{
        //         $id = $_GET['id'];
        //         try{
        //             $sql = "SELECT 
        //                     borrower.borrower_id,
        //                     borrower.firstname,
        //                     borrower.lastname,
        //                     borrower.middlename,
        //                     CONCAT(borrower.firstname,' ',borrower.lastname) as fullname,
        //                     borrower.image,
        //                     borrower.district_id,
        //                     borrower.date_registered,
        //                     borrower_details.borrower_details_id,
        //                     borrower_details.atm_number,
        //                     borrower_details.atm_name,
        //                     borrower_details.gender	,
        //                     borrower_details.birthdate,
        //                     borrower_details.present_address,
        //                     borrower_details.id_name,
        //                     borrower_details.id_number,
        //                     borrower_details.position,
        //                     borrower_details.income,
        //                     borrower_details.gross,
        //                     borrower_details.net,
        //                     borrower_details.others_id,
        //                     borrower_contact.borrower_contact_id,
        //                     borrower_contact.mobile,
        //                     borrower_contact.telephone,
        //                     borrower_contact.email  
        //                 FROM borrower
        //                 LEFT JOIN borrower_contact ON borrower.borrower_id = borrower_contact.borrower_id
        //                 LEFT JOIN borrower_details ON borrower.borrower_id = borrower_details.borrower_id
        //                 WHERe borrower.borrower_id = '{$id}'";
        //                 $result = $this->db->query($sql)->result();
        //                 $this->res = array(
        //                     'isError' => false,
        //                     'date'    => date("Y-m-d"),  
        //                     'data'    => $result,
        //                 );
        //         }catch(Exception $e) {
        //             $this->res = array(
        //                 'isError' => true,
        //                 'message'   => $e->getMessage(),
        //                 'date'    => date("Y-m-d"),  
        //             );
        //         }
        //     }
            
            
        //     $this->displayJSON($this->res);
        // }
        public function comaker($id)
		{
			$sql = "SELECT comaker.comaker_id,comaker.lastname,comaker.firstname,comaker.middlename,comaker.address,
						comaker.mobile,comaker.landline,comaker.valid_id_no,comaker.date_issued,comaker.place_issued
					FROM comaker
					WHERE comaker.borrower_id = $id";
            $data = $this->db->query($sql)->result();
            $this->res = array(
                'isError' => false,
                'date'    => date("Y-m-d"),  
                'data'    => $data,
            );
            $this->displayJSON($this->res); 
		}
		public function witness($id)
		{
			$sql = "SELECT  _witness.witness_id,_witness.lastname,_witness.firstname,_witness.middlename,_witness.address
					FROM _witness
					WHERE _witness.borrower_id = $id";
			$data = $this->db->query($sql)->result();
            $this->res = array(
                'isError' => false,
                'date'    => date("Y-m-d"),  
                'data'    => $data,
            );
            $this->displayJSON($this->res);
		}
        public function loan(){
  
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
                        WHERE loan.is_active = 1 AND loan.status_id IN(1,2)";

                    if(!empty($_GET['borrower_id'])){
                        $sql .= " AND loan.borrower_id = '{$_GET['borrower_id']}'";
                    }
                    if(!empty($_GET['loan_id'])){
                        $sql .= " AND loan.loan_id = '{$_GET['loan_id']}'";
                    }
                    $sql .= " ORDER BY borrower.borrower_id";
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
        public function addedCapital(){
  
            try{
                $sql = "SELECT loan_add_capital.loan_add_capital_id, 
                        district.name as district,
                        loan.loan_id,
                        borrower.borrower_id,
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
                        WHERE loan_add_capital.status_id IN(1,2) AND loan_add_capital.is_released = 1 AND loan_add_capital.is_void = 0";

                    if(!empty($_GET['borrower_id'])){
                        $sql .= " AND loan.borrower_id = '{$_GET['borrower_id']}'";
                    }
                    if(!empty($_GET['loan_id'])){
                        $sql .= " AND loan.loan_id = '{$_GET['loan_id']}'";
                    }
                    if(!empty($_GET['capital_id'])){
                        $sql .= " AND loan_add_capital.loan_add_capital_id = '{$_GET['capital_id']}'";
                    }


                    $sql .= " ORDER BY borrower.borrower_id";
                    
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
        public function salary($type = ""){

            try{

                $sql = "SELECT borrower.borrower_id,
                        CONCAT(borrower.lastname,' ,',borrower.firstname,' ',borrower.middlename) as name,
                        borrower_salary.borrower_salary_id,
                        borrower_salary.amount,
                        borrower_salary.date_transact,
                        borrower_salary.date_value,
                        borrower_salary.salary_date,
                        borrower_salary.transact_by,
                        borrower_salary.edit_by,
                        borrower_salary.edit_by_date,
                        borrower_salary.is_void,
                        borrower_salary.is_use,
                        borrower_salary.is_take,
                        borrower_salary.void_date
                    FROM borrower_salary
                    LEFT JOIN borrower  ON borrower_salary.borrower_id = borrower.borrower_id";

                if($type == "active"){
                    $sql .="WHERE borrower_salary.is_void = 0";
                }
                if($type == "inactive"){
                    $sql .="WHERE borrower_salary.is_void = 1";
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
        public function incentives($type = ""){

            try{

                $sql = "SELECT borrower.borrower_id,
                        CONCAT(borrower.lastname,' ,',borrower.firstname,' ',borrower.middlename) as name,
                        loan_product.name as loan_product,
                        borrower_incentives.loan_product_id,
                        borrower_incentives.amount,
                        borrower_incentives.date_transact,
                        borrower_incentives.date_value,
                        borrower_incentives.incentive_date,
                        borrower_incentives.transact_by,
                        borrower_incentives.edit_by,
                        borrower_incentives.edit_by_date,
                        borrower_incentives.is_void,
                        borrower_incentives.void_date,
                        borrower_incentives.is_used,
                        borrower_incentives.description,
                        borrower_incentives.status_id
                    FROM borrower_incentives
                    LEFT JOIN borrower      ON borrower_incentives.borrower_id  = borrower.borrower_id
                    LEFT JOIN loan_product  ON loan_product.loan_product_id     = borrower_incentives.loan_product_id";

                if($type == "active"){
                    $sql .="WHERE borrower_incentives.is_void = 0";
                }
                if($type == "inactive"){
                    $sql .="WHERE borrower_incentives.is_void = 1";
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
        public function district(){
            try{
                $sql = "SELECT * FROM district ORDER BY district_id";
                $result =  $this->db->query($sql)->result();
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
		public function get_schedule(){
            $response = array();
            // $borrower_id = 1;
            $borrower_id = $this->input->post('borrower_id');
            if(empty($borrower_id)){
                $this->res  = array(
                    'isError'  => true,
                    'message'   => "Empty borrower id",
                    'date'      => date("Y-m-d H:i:s")
                );
            }else{
				$data = $this->borrowermodel->getBorrowerSchedule($borrower_id);
                try{
					$this->res  = array(
						'isError'  => false,
						'message'   => "Success",
						'data'      => $data,
						'date'      => date("Y-m-d H:i:s")
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
		public function get_request(){
            $response = array();
			try{
				$data = $this->borrowermodel->get_request();
				$this->res  = array(
					'isError'  => false,
					'message'   => "Success",
					'data'      => $data,
					'date'      => date("Y-m-d H:i:s")
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
		public function add_request(){
			$response = array();

			if(empty($_POST['borrower_requests'])){
				$this->res = array(
					'isError' => true,
					'message'   => "Empty borrower_requests",
					'date'    => date("Y-m-d"),  
				);
			}
			else if(empty($_POST['borrower_id'])){
				$this->res = array(
					'isError' => true,
					'message'   => "Empty borrower_id",
					'date'    => date("Y-m-d"),  
				);
			}
			else{
				try{
					$borrower_requests = $_POST['borrower_requests'];
					$borrower_id 	  = $_POST['borrower_id'];
					
					$data = array(
						'borrower_requests'  => $borrower_requests,
						'date'					=> date("Y-m-d H:i:s"),
						'borrower_id' 			=> $borrower_id,
					);

					$res = $this->db->insert("borrower_requests",$data);
					if($res){
						$this->res  = array(
							'isError'  => false,
							'message'   => "Success",
							'data'      => $data,
							'date'      => date("Y-m-d H:i:s")
						);
					}else{
						$this->res = array(
							'isError' => true,
							'message'   => "Error",
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
		public function update_request(){
			$response = array();

			if(empty($_POST['borrower_requests_id'])){
				$this->res = array(
					'isError' => true,
					'message'   => "Empty borrower_requests_id",
					'date'    => date("Y-m-d"),  
				);
			}
			else if(empty($_POST['borrower_requests'])){
				$this->res = array(
					'isError' => true,
					'message'   => "Empty borrower_requests",
					'date'    => date("Y-m-d"),  
				);
			}
			else if(empty($_POST['borrower_id'])){
				$this->res = array(
					'isError' => true,
					'message'   => "Empty borrower_id",
					'date'    => date("Y-m-d"),  
				);
			}
			else{
				try{
					$borrower_requests_id = $_POST['borrower_requests_id'];
					$borrower_requests = $_POST['borrower_requests'];
					$borrower_id 	  = $_POST['borrower_id'];
					
					$data = array(
						'borrower_requests'  => $borrower_requests,
						'date'					=> date("Y-m-d H:i:s"),
						'borrower_id' 			=> $borrower_id,
					);

					$this->db->where("borrower_requests_id",$borrower_requests_id);
					$res = $this->db->update("borrower_requests",$data);
					if($res){
						$this->res  = array(
							'isError'  => false,
							'message'   => "Success",
							'data'      => $data,
							'date'      => date("Y-m-d H:i:s")
						);
					}else{
						$this->res = array(
							'isError' => true,
							'message'   => "Error",
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
		public function approve_request(){
			$response = array();

			if(empty($_POST['borrower_requests_id'])){
				$this->res = array(
					'isError' => true,
					'message'   => "Empty borrower request id",
					'date'    => date("Y-m-d"),  
				);
			}else{
				try{
					$id = $_POST['borrower_requests_id'];
					$note = $_POST['note'];
					
					$data = array(
						'status_id'=>1,
						'note'=> $note
					);

					$this->db->where("borrower_requests_id",$id);
					$res = $this->db->update("borrower_requests",$data);
					if($res){
						$this->res  = array(
							'isError'  => false,
							'message'   => "Success",
							'data'      => $data,
							'date'      => date("Y-m-d H:i:s")
						);
					}else{
						$this->res = array(
							'isError' => true,
							'message'   => "Error",
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
		public function remove_request(){
			$response = array();

			if(empty($_POST['borrower_requests_id'])){
				$this->res = array(
					'isError' => true,
					'message'   => "Empty borrower request id",
					'date'    => date("Y-m-d"),  
				);
			}else{
				try{
					$id = $_POST['borrower_requests_id'];
					
					$data = array(
						'status_id' => 6
					);

					$this->db->where("borrower_requests_id",$id);
					$res = $this->db->update("borrower_requests",$data);
					if($res){
						$this->res  = array(
							'isError'  => false,
							'message'   => "Success",
							'data'      => $data,
							'date'      => date("Y-m-d H:i:s")
						);
					}else{
						$this->res = array(
							'isError' => true,
							'message'   => "Error",
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
		public function disapprove_request(){
			$response = array();

			if(empty($_POST['borrower_requests_id'])){
				$this->res = array(
					'isError' => true,
					'message'   => "Empty borrower request id",
					'date'    => date("Y-m-d"),  
				);
			}else{
				try{
					$id = $_POST['borrower_requests_id'];
					$note = $_POST['note'];
					
					$data = array(
						'status_id'=> 3,
						'note' => $note
					);

					$this->db->where("borrower_requests_id",$id);
					$res = $this->db->update("borrower_requests",$data);
					if($res){
						$this->res  = array(
							'isError'  => false,
							'message'   => "Success",
							'data'      => $data,
							'date'      => date("Y-m-d H:i:s")
						);
					}else{
						$this->res = array(
							'isError' => true,
							'message'   => "Error",
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
		public function done_request(){
			$response = array();

			if(empty($_POST['borrower_requests_id'])){
				$this->res = array(
					'isError' => true,
					'message'   => "Empty borrower request id",
					'date'    => date("Y-m-d"),  
				);
			}else{
				try{
					$id = $_POST['borrower_requests_id'];

					$data = array(
						'status_id'=> 9,
					);

					$this->db->where("borrower_requests_id",$id);
					$res = $this->db->update("borrower_requests",$data);
					if($res){
						$this->res  = array(
							'isError'  => false,
							'message'   => "Success",
							'data'      => $data,
							'date'      => date("Y-m-d H:i:s")
						);
					}else{
						$this->res = array(
							'isError' => true,
							'message'   => "Error",
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
		public function get_insurance_payment_log(){
			$response = array();
			try{

				$sql = "SELECT * FROM insurance_payment_log	
						WHERE insurance_payment_log.is_active = 1 ";

				if(!empty($_GET['borrower_id'])){
					$bid = $_GET['borrower_id'];
					$sql .= "AND insurance_payment_log.borrower_id = '$bid' ";
				}

				if(!empty($_GET['date_value'])){
					$date_value = date("Y-m-d",strtotime($_GET['date_value']));
					$sql .= "AND DATE_FORMAT(insurance_payment_log.date,'%Y-%m') = DATE_FORMAT('$date_value','%Y-%m')";
				}


				$data = $this->db->query($sql)->result();
				
				$this->res  = array(
					'isError'  => false,
					'message'   => "Success",
					'data'      => $data,
					'date'      => date("Y-m-d H:i:s")
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
