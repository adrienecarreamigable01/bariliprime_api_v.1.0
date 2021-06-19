
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
                        borrower.lastname,
                        borrower.firstname,
                        borrower.middlename,
                        borrower.image,
                        borrower_details.birthdate,
                        borrower_details.gender,
                        borrower_details.present_address,
                        card_status.card_status,
                        card_status.textColor,
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
                                WHERE payment.status = 1 AND payment.loan_id = loan.loan_id AND payment.payment_type_id IN(2,3,5,6,7)) as `total_paid`,
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
                        LEFT JOIN borrower_details 	ON borrower_details.borrower_id = borrower.borrower_id  
                        LEFT JOIN card_status 	ON card_status.card_status_id  = borrower_details.gsis  
                        LEFT JOIN status 		ON status.status_id = loan.status_id
                        WHERE loan.is_active = 1";

                    if(!empty($_GET['status_id'])){
                        $status = $_GET['status_id'];
                        $sql .= " AND loan.status_id IN($status)";
                    }

                    if(!empty($_GET['is_released'])){
                        $is_released = $_GET['is_released'];
                        $sql .= " AND loan.is_released IN($is_released)";
                    }else{
                        $sql .= " AND loan.is_released = 0";
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

                    if( !empty($_GET['status_id']) && !empty($_GET['is_released']) ){
                        $status = $_GET['status_id'];
                        $is_released = $_GET['is_released'];
                        if( $status == 1 && $is_released == 1){
                            $sql .= " ORDER BY loan.released_date DESC";
                        }
                        else if( $status == 6){
                            $sql .= " ORDER BY loan.void_date DESC";
                        }
                        else{
                            $sql .= " ORDER BY loan.date_added DESC";
                        }
                    }

                    
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
        public function toInsurance(){
  
            try{

                $sql = "SELECT loan.loan_id, 
                        district.name as district,
						borrower.borrower_id as bid,
                        borrower.lastname,
                        borrower.firstname,
                        borrower.middlename,
                        borrower_details.birthdate,
                        borrower_details.gender,
                        borrower_details.present_address,
                        CONCAT(borrower.lastname,' ,',borrower.firstname,' ',borrower.middlename) as borrower,
                        loan.borrower_id, 
                        SUM(loan.principal_amount) as totalAmount,
                        (SELECT 
                            CASE WHEN SUM(payment.amount) > 0 
                             	   THEN SUM(payment.amount) 
                                ELSE 0 END FROM payment 
								LEFT JOIN loan ON loan.loan_id = payment.loan_id
                                WHERE payment.status = 1 
								AND payment.payment_type_id IN(2,4,5,6,7) 
								AND payment.borrower_id = bid
								AND loan.is_active = 1 AND loan.is_released = 1 AND loan.status_id = 1
						) as `total_paid`,
                        (SELECT 
                            CASE WHEN SUM(loan_add_capital.amount) > 0 
                                THEN SUM(loan_add_capital.amount) 
                            ELSE 0 END FROM loan_add_capital 
							LEFT JOIN loan ON loan.loan_id = loan_add_capital.loan_id
                            WHERE loan_add_capital.status_id = 1 
                            AND loan_add_capital.is_released = 1 
                            AND loan_add_capital.is_void = 0 
                            AND loan.borrower_id = borrower.borrower_id
                        ) as `added_capital`
                        -- loan.principal_amount - total_paid as balance, 
                        FROM loan 
                        LEFT JOIN loan_product ON loan_product.loan_product_id = loan.loan_product_id
                        LEFT JOIN borrower ON loan.borrower_id = borrower.borrower_id
                        LEFT JOIN district ON district.district_id = borrower.district_id
                        LEFT JOIN borrower_details 	ON borrower_details.borrower_id = borrower.borrower_id  
                        LEFT JOIN status 		ON status.status_id = loan.status_id
						WHERE loan.is_active = 1 AND loan.is_released = 1 AND loan.status_id = 1 AND borrower_details.hasInsured = 1 AND loan.loan_product_id IN(1,2,6,7,12,9)";
						
						if(!empty($_GET['district'])){
							$district = $_GET['district'];
							$sql .= " AND borrower.district_id = {$district}";
						}
				
						$sql .=" GROUP BY borrower.borrower_id
								ORDER BY loan.released_date DESC";

               
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
        public function count(){
  
            try{


                $sql = "SELECT count(loan.loan_id) as total
                        FROM loan
                        WHERE loan.is_active = 1 ";

                    if(!empty($_GET['status_id'])){
                        $status = $_GET['status_id'];
                        $sql .= " AND loan.status_id IN($status)";
                    }

                    if(!empty($_GET['is_released'])){
                        $is_released = $_GET['is_released'];
                        $sql .= " AND loan.is_released IN($is_released)";
                    }else{
                        $sql .= " AND loan.is_released = 0";
                    }

                    if(!empty($_GET['borrower_id'])){
                        $sql .= " AND loan.borrower_id = '{$_GET['borrower_id']}'";
                    }

                    // print_r($sql);exit;
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
        public function info(){
            
            if(empty($_GET['loan_id'])){
                $this->res = array(
                    'isError' => true,
                    'message'   => "Empty or null loan id",
                    'date'    => date("Y-m-d"),  
                );
            }else{
                try{
                    $loan_id = $_GET['loan_id']; 
                    $sql = "SELECT loan.loan_id,
                            loan.loan_id as loanid,
                            (SELECT 
                                CASE WHEN SUM(payment.amount) > 0 
                                    THEN SUM(payment.amount) 
                                    ELSE 0 END FROM payment 
                                    WHERE payment.loan_id = loan.loan_id AND payment.payment_type_id IN(2,4) AND payment.is_void = 0 AND payment.status = 1) as `total_paid`,
                            (SELECT 
                                CASE WHEN SUM(loan_add_capital.amount) > 0 
                                    THEN SUM(loan_add_capital.amount) 
                                ELSE 0 END FROM loan_add_capital 
                                WHERE loan_add_capital.status_id = 1 
                                AND loan_add_capital.is_released = 1 
                                AND loan_add_capital.is_void = 0 
                                AND loan_add_capital.loan_id = loanid
                            ) as `added_capital`,
                            loan.transact_by,
                            loan.borrower_id,
                            loan.loan_type,
                            loan.interest_amount,
                            loan.loan_product_id,
                            loan.deficit_amounts,
                            loan.borrower_id,
                            loan.principal_amount,
                            loan.released_date,
                            loan.interest,
                            loan.is_reloan,
                        loan.term,
                        loan.date_start,
                        loan.note,
                        loan.description,
                        loan.due_date,
                        loan.released_amount,
                        loan.loan_category_id,
                        loan.interest,
                        loan.processing_fee,
                        loan.total_amount,
                        loan.reloan_from,
                        loan.monthly_payment,
                        loan.released_date,
                        loan.is_released,
                        loan.is_reconstruct,
                        loan_product.name as loan_product,
                        status.status_id,
                        status.name as status,
                        status.color,
                        CONCAT(borrower.lastname,',',borrower.firstname,' ',borrower.middlename) as Name,
                        borrower.firstname,borrower.lastname,
                        borrower.middlename,borrower.image,
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
                        (loan.total_amount - (SELECT SUM(amount) as total FROM payment WHERE payment.loan_id = loan.loan_id AND payment.is_void = 0 AND payment.status = 1 AND payment.payment_type_id IN(2,3,4))) as balance
                    FROM loan
                    LEFT JOIN borrower 			ON borrower.borrower_id 		= loan.borrower_id
                    LEFT JOIN borrower_contact 	ON borrower_contact.borrower_id = borrower.borrower_id
                    LEFT JOIN borrower_details 	ON borrower_details.borrower_id = borrower.borrower_id
                    LEFT JOIN district 		   	ON district.district_id 		= borrower.district_id
                    LEFT JOIN loan_product 		ON loan_product.loan_product_id = loan.loan_product_id
                    LEFT JOIN status 			ON status.status_id 			= loan.status_id
                    WHERE loan.loan_id = '{$loan_id}'";
                    $data = $this->db->query($sql)->result();
                    
                    $this->res = array(
                        'isError' => false,
                        'message' => "success",
                        'data'    =>$data,
                        'date'    => date("Y-m-d"),  
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
        public function get_single_loan($loan_id){
			$sql = "SELECT loan.loan_id,
							loan.loan_id as loanid,
							(SELECT 
								CASE WHEN SUM(payment.amount) > 0 
									THEN SUM(payment.amount) 
									ELSE 0 END FROM payment 
									WHERE payment.loan_id = loan.loan_id AND payment.payment_type_id IN(2,4) AND payment.is_void = 0 AND payment.status = 1) as `total_paid`,
							(SELECT 
								CASE WHEN SUM(loan_add_capital.amount) > 0 
									THEN SUM(loan_add_capital.amount) 
								ELSE 0 END FROM loan_add_capital 
								WHERE loan_add_capital.status_id = 1 
								AND loan_add_capital.is_released = 1 
								AND loan_add_capital.is_void = 0 
								AND loan_add_capital.loan_id = loanid
							) as `added_capital`,
							loan.transact_by,
							loan.borrower_id,
							loan.loan_type,
							loan.interest_amount,
							loan.loan_product_id,
							loan.deficit_amounts,
							loan.borrower_id,
							loan.principal_amount,
							loan.released_date,
							loan.interest,
							loan.is_reloan,
						   loan.term,
						   loan.date_start,
						   loan.note,
						   loan.description,
						   loan.due_date,
						   loan.released_amount,
						   loan.loan_category_id,
						   loan.interest,
						   loan.processing_fee,
						   loan.total_amount,
						   loan.reloan_from,
						   loan.monthly_payment,
						   loan.released_date,
						   loan.is_released,
						   loan.is_reconstruct,
						   loan_product.name as loan_product,
						   status.status_id,
						   status.name as status,
						   status.color,
						   CONCAT(borrower.lastname,',',borrower.firstname,' ',borrower.middlename) as Name,
						   borrower.firstname,borrower.lastname,
						   borrower.middlename,borrower.image,
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
						   (loan.total_amount - (SELECT SUM(amount) as total FROM payment WHERE payment.loan_id = loan.loan_id AND payment.is_void = 0 AND payment.status = 1 AND payment.payment_type_id IN(2,3,4))) as balance
					FROM loan
					LEFT JOIN borrower 			ON borrower.borrower_id 		= loan.borrower_id
					LEFT JOIN borrower_contact 	ON borrower_contact.borrower_id = borrower.borrower_id
					LEFT JOIN borrower_details 	ON borrower_details.borrower_id = borrower.borrower_id
					LEFT JOIN district 		   	ON district.district_id 		= borrower.district_id
					LEFT JOIN loan_product 		ON loan_product.loan_product_id = loan.loan_product_id
					LEFT JOIN status 			ON status.status_id 			= loan.status_id
					WHERE loan.loan_id = $loan_id";
			$data = $this->db->query($sql)->result();
			return $data;
		}
        public function get_single_loan_by_product(){
            $borrower_id = $_GET['id'];
            $loan_product = $_GET['loan_product'];
			$sql = "SELECT loan.loan_id,
							loan.loan_id as loanid,
							(SELECT 
								CASE WHEN SUM(payment.amount) > 0 
									THEN SUM(payment.amount) 
									ELSE 0 END FROM payment 
									WHERE payment.loan_id = loan.loan_id AND payment.payment_type_id IN(2,4) AND payment.is_void = 0 AND payment.status = 1) as `total_paid`,
							(SELECT 
								CASE WHEN SUM(loan_add_capital.amount) > 0 
									THEN SUM(loan_add_capital.amount) 
								ELSE 0 END FROM loan_add_capital 
								WHERE loan_add_capital.status_id = 1 
								AND loan_add_capital.is_released = 1 
								AND loan_add_capital.is_void = 0 
								AND loan_add_capital.loan_id = loanid
							) as `added_capital`,
							loan.transact_by,
							loan.borrower_id,
							loan.loan_type,
							loan.interest_amount,
							loan.loan_product_id,
							loan.deficit_amounts,
							loan.borrower_id,
							loan.principal_amount,
							loan.released_date,
							loan.interest,
							loan.is_reloan,
						   loan.term,
						   loan.date_start,
						   loan.note,
						   loan.description,
						   loan.due_date,
						   loan.released_amount,
						   loan.loan_category_id,
						   loan.interest,
						   loan.processing_fee,
						   loan.total_amount,
						   loan.reloan_from,
						   loan.monthly_payment,
						   loan.released_date,
						   loan.is_released,
						   loan.is_reconstruct,
						   loan_product.name as loan_product,
						   status.status_id,
						   status.name as status,
						   status.color,
						   CONCAT(borrower.lastname,',',borrower.firstname,' ',borrower.middlename) as Name,
						   borrower.firstname,borrower.lastname,
						   borrower.middlename,borrower.image,
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
						   (loan.total_amount - (SELECT SUM(amount) as total FROM payment WHERE payment.loan_id = loan.loan_id AND payment.is_void = 0 AND payment.status = 1 AND payment.payment_type_id IN(2,3,4))) as balance
					FROM loan
					LEFT JOIN borrower 			ON borrower.borrower_id 		= loan.borrower_id
					LEFT JOIN borrower_contact 	ON borrower_contact.borrower_id = borrower.borrower_id
					LEFT JOIN borrower_details 	ON borrower_details.borrower_id = borrower.borrower_id
					LEFT JOIN district 		   	ON district.district_id 		= borrower.district_id
					LEFT JOIN loan_product 		ON loan_product.loan_product_id = loan.loan_product_id
					LEFT JOIN status 			ON status.status_id 			= loan.status_id
                    WHERE loan.loan_product_id IN($loan_product) AND loan.borrower_id = '{$borrower_id}'";
			$data = $this->db->query($sql)->result();
            $this->res = array(
                'isError' => false,
                'message'   => "success",
                'data'      => $data,
                'date'    => date("Y-m-d"),  
            );
            $this->displayJSON($this->res);
		}
        public function get_single_loan_by_group(){
            $borrower_id = $_GET['id'];
            $loan_group = $_GET['loan_group'];
			$sql = "SELECT loan.loan_id,
							loan.loan_id as loanid,
							(SELECT 
								CASE WHEN SUM(payment.amount) > 0 
									THEN SUM(payment.amount) 
									ELSE 0 END FROM payment 
									WHERE payment.loan_id = loan.loan_id AND payment.payment_type_id IN(2,4) AND payment.is_void = 0 AND payment.status = 1) as `total_paid`,
							(SELECT 
								CASE WHEN SUM(loan_add_capital.amount) > 0 
									THEN SUM(loan_add_capital.amount) 
								ELSE 0 END FROM loan_add_capital 
								WHERE loan_add_capital.status_id = 1 
								AND loan_add_capital.is_released = 1 
								AND loan_add_capital.is_void = 0 
								AND loan_add_capital.loan_id = loanid
							) as `added_capital`,
							loan.transact_by,
							loan.borrower_id,
							loan.loan_type,
							loan.interest_amount,
							loan.loan_product_id,
							loan.deficit_amounts,
							loan.borrower_id,
							loan.principal_amount,
							loan.released_date,
							loan.interest,
							loan.is_reloan,
						   loan.term,
						   loan.date_start,
						   loan.note,
						   loan.description,
						   loan.due_date,
						   loan.released_amount,
						   loan.loan_category_id,
						   loan.interest,
						   loan.processing_fee,
						   loan.total_amount,
						   loan.reloan_from,
						   loan.monthly_payment,
						   loan.released_date,
						   loan.is_released,
						   loan.is_reconstruct,
						   loan_product.name as loan_product,
						   status.status_id,
						   status.name as status,
						   status.color,
						   CONCAT(borrower.lastname,',',borrower.firstname,' ',borrower.middlename) as Name,
						   borrower.firstname,borrower.lastname,
						   borrower.middlename,borrower.image,
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
						   (loan.total_amount - (SELECT SUM(amount) as total FROM payment WHERE payment.loan_id = loan.loan_id AND payment.is_void = 0 AND payment.status = 1 AND payment.payment_type_id IN(2,3,4))) as balance
					FROM loan
					LEFT JOIN borrower 			ON borrower.borrower_id 		= loan.borrower_id
					LEFT JOIN borrower_contact 	ON borrower_contact.borrower_id = borrower.borrower_id
					LEFT JOIN borrower_details 	ON borrower_details.borrower_id = borrower.borrower_id
					LEFT JOIN district 		   	ON district.district_id 		= borrower.district_id
					LEFT JOIN loan_product 		ON loan_product.loan_product_id = loan.loan_product_id
					LEFT JOIN status 			ON status.status_id 			= loan.status_id
                    WHERE loan.loan_group IN($loan_group) AND loan.borrower_id = '{$borrower_id}'";
			$data = $this->db->query($sql)->result();
            $this->res = array(
                'isError' => false,
                'message'   => "success",
                'data'      => $data,
                'date'    => date("Y-m-d"),  
            );
            $this->displayJSON($this->res);
		}
        public function approve(){

            $incentive_product = array(3,4,5,6,11,8,9,10,13,14);
			$loanid 		= $this->input->post('loan_id');
			$name 		    = $this->input->post('name');
            $date_released 	= !empty($this->input->post('date_released')) ? $this->input->post('date_released') : date("Y-m-d");
            
            if(empty($loanid)){
                
                $this->res = array(
                    'isError' => true,
                    'message'   => "Empty Loan Id",
                    'date'    => date("Y-m-d"),  
                );
            }
            else if(empty($name)){
                
                $this->res = array(
                    'isError' => true,
                    'message'   => "Please indicate name",
                    'date'    => date("Y-m-d"),  
                );
            }
            else {      
               try{
                    $loan_data 		= $this->get_single_loan($loanid)[0];

                    $is_reconstruct = $loan_data->is_reconstruct;
                    $array = array(6);

                    if(in_array($loan_data->loan_product_id,$array)){
                        $due_date = date('Y-m-d', strtotime("+2 months", strtotime($date_released)));
                    }
                    else if(in_array($loan_data->loan_product_id,$incentive_product)){
                        $term = $loan_data->term;
                        $due_date = date('Y-m-d', strtotime("+".$term." months", strtotime($date_released)));
                    }
                    else{
                        $due_date = date('Y-m-d', strtotime("+1 months", strtotime($date_released)));
                    }
                    
                    
                    $data = array(
                        'status_id' 	=> 1,
                        'released_date' =>  $date_released,
                        'due_date' 	    =>  $due_date,
                    );
                    $this->db->where('loan_id',$loanid);
                    $res =  $this->db->update('loan',$data);

                    if($res){
                        $insert_logs = array(
                            'logs' => "#{$loanid} {$loan_data->loan_product} Loan has been approve for the date of ".date("Y-m-d H:i:s").' by '. $name.' using mobile application',
                            'loan_id' => $loanid,
                            'borrower_id' => $loan_data->borrower_id,
                        );
                        $insert_logs_res = $this->db->insert("logs",$insert_logs);
                        
                        if($insert_logs_res ){
                            $notif 	= array(
                                'message' => 'One loan has been approve by '. $name.' using mobile applicaition',
                                'link' => 'borrower/loan/'.$loanid
                            );

                            $resNotif = $this->insertNotificaiton($notif);

                            $this->res = array(
                                'isError' => false,
                                'message'   => "Successfuly approve loan",
                                'date'    => date("Y-m-d"),  
                            );

                        }else{
                            $this->res = array(
                                'isError' => false,
                                'message'   => "Successfuly approve loan",
                                'date'    => date("Y-m-d"),  
                            );
                        }
                    }
               }
               catch(Exception $e) {
                    $this->res = array(
                        'isError' => true,
                        'message'   => $e->getMessage(),
                        'date'    => date("Y-m-d"),  
                    );
                }
            }

            $this->displayJSON($this->res);
        }
        public function release(){
			
			$vault_balance  = $this->get_vault_balance();
			$loanid 		= $this->input->post('loan_id');
           
			$payload = array(
				'primary_key' 	=> 'loan_id',
				'id' 			=> $loanid,
            );
            
            $inVault  		= $this->checkifInVault($payload);
            
            if(empty($loanid)){
                $this->res = array(
                    'isError' => true,
                    "message"=>"Empty loan id",
                    'date'    => date("Y-m-d"),  
                );
            }else{

                $loan 			= $this->get_single_loan($loanid)[0];
               
                if($vault_balance < $loan->released_amount){
                    $this->res = array(
                        'isError' => true,
                        "message"=>"Insufficient amount on vault the vault balance is ".$vault_balance,
                        'date'    => date("Y-m-d"),  
                    );
                }
                else if(!empty($inVault)){
                    $this->res = array(
                        'isError' => true,
                        "message"=>"Loan already released, Please reload your browser",
                        'date'    => date("Y-m-d"),  
                    );
                }
                else{

                   try{
                        
                        $data = array(
                            'is_released' 	=> 1,
                            'released_by' 	=> $_POST['userId'],
                            'released_date' =>date('Y-m-d'),
                        );
    
                        if($loan->loan_product_id == 1 || $loan->loan_product_id == 2)
                        {
                            $due_date 			= date('Y-m-d', strtotime("+1 months", strtotime($loan->released_date)));
                            $data['date_start'] = $due_date;
                        }
                        else if($loan->loan_product_id == 6 || $loan->loan_product_id == 12)
                        {
                            $due_date 			= date('Y-m-d', strtotime("+2 months", strtotime($loan->released_date)));
                            $data['date_start'] = $due_date;
                        }
                        // else if($loan->loan_product_id == 9 AND $loan->loan_category_id == 1)
                        // {
                        // 	$due_date 			= date('Y-m-d', strtotime("+1 months", strtotime($loan->released_date)));
                        // 	$data['date_start'] = $due_date;
                        // }
                        else if($loan->loan_product_id == 9 AND $loan->loan_category_id == 2)
                        {
                            $due_date 			= date('Y-m-d', strtotime("+1 months", strtotime($loan->released_date)));
                            $data['date_start'] = $due_date;
                        }
                        else
                        {
                            $due_date 			= date('Y-m-d', strtotime("+".$loan->term." months", strtotime($loan->released_date)));
                            $data['date_start'] = $due_date;
                        }
                        $this->db->where('loan_id',$loanid);
                        $this->db->update('loan',$data);
                        //insert data to vault
                        $vault = array(
                            'trasaction_type_id' 	=> 2,
                            'vault_type' 			=> 2,
                            'loan_id' 				=> $loanid,
                            'user_id' 				=> $_POST['userId'],
                            'amount' 				=> $loan->released_amount,
                            'description' 			=> $loan->loan_product." - Loan released of mr/ms ".$loan->Name.' for the date of '. $loan->released_date,
                        );
                        $res = $this->db->insert('vault',$vault);
                        
                        if($res){
                            
                            $notif 	= array(
                                'message' => "#{$loanid} {$loan->loan_product} Loan has been released with a loan {$loanid} with the amount of {$loan->released_amount} for the date of ".date("Y-m-d H:i:s").' by '. $_POST['name'],
                                'link' => 'borrower/loan/'.$loanid
                            );
    
                            $r = $this->insertNotificaiton($notif);
    
                            if($r)
                            {
                                $insert_logs = array(
                                    'logs' => "#{$loanid} {$loan->loan_product} Loan has been released with a loan {$loanid} with the amount of {$loan->released_amount} for the date of ".date("Y-m-d H:i:s").' by '. $_POST['name'],
                                    'loan_id' => $loanid,
                                    'borrower_id' => $loan->borrower_id,
                                );
                                $this->db->insert('logs',$insert_logs);
                                $this->res = array(
                                    'isError' => false,
                                    "message"=>"Successfuly realeased loan",
                                    'date'    => date("Y-m-d"),  
                                );
                            }
                            else{
                                $this->res = array(
                                    'isError' => false,
                                    "message" =>"Error releasing loan",
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
                }
            }
			
            $this->displayJSON($this->res);
		}
        public function void()
		{
			$arrayTransaction = array();
            $loan_id = $this->input->post('loan_id');
            $name 		    = $this->input->post('name');
            if(empty($loan_id)){
                
                $this->res = array(
                    'isError' => true,
                    'message'   => "Empty Loan Id",
                    'date'    => date("Y-m-d"),  
                );
            }else{

                try{
                    $loan = $this->get_single_loan($loan_id)[0];
                    /* update current loan */
                    $update_loan = array(
                                'status_id' => 6,
                                'is_reloan' => 0,
                                'void_date' => date('Y-m-d'),
                                'note'		=> $this->input->post('note')
                            );
                    $this->db->where('loan_id',$loan_id);
                    // $update_loan_res = $this->db->update('loan',$update_loan);
    
                    $sql1 = $this->db->set($update_loan)->get_compiled_update('loan');
                    array_push($arrayTransaction,$sql1);
                        
                    // update last loan
                    $update_loan2 = array(
                        'is_reloan' => 0,
                    );
                    $this->db->where('loan_id',$loan->reloan_from);
                    // $lastLoan = $this->db->update('loan',$update_loan2);
    
                    $sql2 = $this->db->set($update_loan2)->get_compiled_update('loan');
                    array_push($arrayTransaction,$sql2);
    
                    $single_loan = $this->get_single_loan($loan_id)[0];
                    $insert_logs = array(
                        'logs' => "#{$loan_id} {$single_loan->loan_product} Loan has been voided for the date of ".date("Y-m-d H:i:s").' by '. $name,
                        'loan_id' => $loan_id,
                        'borrower_id' => $single_loan->borrower_id,
                    );
                    $logs_res =$this->db->set($insert_logs)->get_compiled_insert('logs');
                    array_push($arrayTransaction,$logs_res);
    
                    // $array_bonus1 = array(8,10);
                    // if(in_array($loan->loan_product_id,)){
                    // 	$remove_def = array(
                    // 		'is_active' => 0,
                    // 	);
                    // 	$this->db->where('loan_id',$loan->reloan_from);
                    // 	// $lastLoan = $this->db->update('loan',$update_loan2);
            
                    // 	$sql3 = $this->db->set($remve_def)->get_compiled_update('def_amount_logs');
                    // 	array_push($arrayTransaction,$sql3);
                    // }
    
                    
                    if($loan->is_released == 1)
                    {
                        //set vault to void if t he loan is released
                        $vault = array(
                            'is_void' 	=> 1,
                            'void_date' => date("Y/m/d"),
                        );
    
                        $this->db->where('loan_id',$loan_id);
                        // return $this->db->update('vault',$vault);
                        $sql3 = $this->db->set($vault)->get_compiled_update('vault');
                        array_push($arrayTransaction,$sql3);
    
                        $insert_logs = array(
                            'logs' => "Remove a item on vault due to the void loan  {$loan_id} ".date("Y-m-d H:i:s").' by '. $name,
                            'loan_id' => $loan_id,
                            'borrower_id' => $single_loan->borrower_id,
                        );
                        $logs_res =$this->db->set($insert_logs)->get_compiled_insert('logs');
                        array_push($arrayTransaction,$logs_res);
                    }
                    
                    
                    if(!empty($arrayTransaction)){
                        $result = array_filter($arrayTransaction);   
                        $res = $this->mysqlTQ($result);
                        if($res){
                            $this->res = array(
                                'isError' => false,
                                'message'   => "Successfuly Disapprove Loan",
                                'date'    => date("Y-m-d"),  
                            );
                        }else{
                            $this->res = array(
                                'isError' => true,
                                'message'   => "Error Disapprove Loan",
                                'date'    => date("Y-m-d"),  
                            );
                        }
                    }else{
                        $this->res = array(
                            'isError' => true,
                            'message'   => "Error Disapprove Loan",
                            'date'    => date("Y-m-d"),  
                        );
                        // return false;exit;
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
        public function update()
		{
			$arrayTransaction = array();
            $loan_id = $this->input->post('loan_id');
            $due_date = $this->input->post('due_date');
            $name = $this->input->post('name');
            if(empty($loan_id)){
                
                $this->res = array(
                    'isError' => true,
                    'message'   => "Empty Loan Id",
                    'date'    => date("Y-m-d"),  
                );
            }
            else if(empty($due_date)){
                
                $this->res = array(
                    'isError' => true,
                    'message'   => "Empty due date",
                    'date'    => date("Y-m-d"),  
                );
            }
            else if(empty($name)){
                
                $this->res = array(
                    'isError' => true,
                    'message'   => "Empty name",
                    'date'    => date("Y-m-d"),  
                );
            }
            else{

                try{
                    /* update current loan */
                    $update_loan = array(
                        'due_date' => $due_date,
                    );
                    $this->db->where('loan_id',$loan_id);
                    // $update_loan_res = $this->db->update('loan',$update_loan);
    
                    $sql1 = $this->db->set($update_loan)->get_compiled_update('loan');
                    array_push($arrayTransaction,$sql1);
                        
    
                    $single_loan = $this->get_single_loan($loan_id)[0];
                    $insert_logs = array(
                        'logs' => "#{$loan_id} {$single_loan->loan_product} Loan has been updated for the date of ".date("Y-m-d H:i:s").' by '. $name,
                        'loan_id' => $loan_id,
                        'borrower_id' => $single_loan->borrower_id,
                    );
                    
                    $logs_res =$this->db->set($insert_logs)->get_compiled_insert('logs');
                    array_push($arrayTransaction,$logs_res);
    
                
                    if(!empty($arrayTransaction)){
                        $result = array_filter($arrayTransaction);   
                        $res = $this->mysqlTQ($result);
                        if($res){
                            $this->res = array(
                                'isError' => false,
                                'message'   => "Successfuly Updated Loan",
                                'date'    => date("Y-m-d"),  
                            );
                        }else{
                            $this->res = array(
                                'isError' => true,
                                'message'   => "Error Updated Loan",
                                'date'    => date("Y-m-d"),  
                            );
                        }
                    }else{
                        $this->res = array(
                            'isError' => true,
                            'message'   => "Error Updated Loan",
                            'date'    => date("Y-m-d"),  
                        );
                        // return false;exit;
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
        public function added_capital()
		{
            try {

                $status_id      = !empty($_GET['status_id']) ? $_GET['status_id'] : 2;
                $is_released    = !empty($_GET['is_released']) ? $_GET['is_released'] : 0;

                $sql = "SELECT  loan_add_capital.loan_add_capital_id,
                        loan_add_capital.amount,
                        loan_add_capital.interest_rate as interest,
                        loan_add_capital.released_amount,
                        loan_add_capital.deficit_amount,
                        loan_add_capital.transact_date,
                        loan_add_capital.transact_by,
                        loan_add_capital.void_date,
                        loan_add_capital.released_date,
                        loan_add_capital.due_date,
                        loan_add_capital.note as description,
                        loan.loan_id,
                        loan.loan_product_id,
                        loan.borrower_id,
                        loan_type.name as loantype,
                        loan_product.name as loan_product,
                        status.name as status,
                        CONCAT(borrower.lastname,', ',borrower.firstname,' ',borrower.middlename) as borrower,
                        CONCAT(user.lastname,', ',user.firstname,' ',user.middlename) as transact_by_name,
                        district.name as district
                FROM loan_add_capital
                LEFT JOIN loan 			ON loan.loan_id    				= loan_add_capital.loan_id
                LEFT JOIN borrower 		ON borrower.borrower_id 		= loan.borrower_id
                LEFT JOIN loan_product 	ON loan_product.loan_product_id = loan.loan_product_id
                LEFT JOIN status 		ON status.status_id 			= loan_add_capital.status_id
                LEFT JOIN user 			ON user.user_id 				= loan.status_id
                LEFT JOIN loan_type 	ON loan_type.loan_type_id 		= loan.transact_by
                LEFT JOIN district ON district.district_id = borrower.district_id ";

                if($status_id == 1 && $is_released == 1 || $status_id == 0)
                {
                    $sql .= "WHERE loan_add_capital.status_id = 1 AND loan_add_capital.is_released = 1 ";
                }
                else if($status_id == 2)
                {
                    $sql .= "WHERE loan_add_capital.status_id = 2 AND loan_add_capital.is_released = 0 ";
                }
                else if($status_id == 5)
                {
                    $sql .= "WHERE loan_add_capital.status_id = $status_id AND loan_add_capital.is_released = 1 ";
                }
                else
                {
                    $sql .= "WHERE loan_add_capital.status_id = $status_id AND loan_add_capital.is_released = 0 ";
                }

                $sql .= "AND borrower.is_active = 1";

                $data =  $this->db->query($sql)->result();

                $this->res = array(
                    'isError' => false,
                    'message' => "Success",
                    'data'    => $data,
                    'date'    => date("Y-m-d"),  
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
        public function checkifInVault($payload){
			$sql = "SELECT * FROM vault
					WHERE {$payload['primary_key']} = {$payload['id']} AND vault.is_void = 0";
			return $this->db->query($sql)->result();
		}
        /*get vault balance*/
		public function get_vault_balance()
		{
			$amount1 = "SELECT sum(vault.amount) as cashin
						FROM vault 
						WHERE vault.trasaction_type_id =1 AND vault.is_void = 0";
			$cashin = $this->db->query($amount1)->result()[0]->cashin;
			$amount2 = "SELECT sum(vault.amount) as cashout
						FROM vault 
						WHERE vault.trasaction_type_id = 2 AND vault.is_void = 0";
			$cashout = $this->db->query($amount2)->result()[0]->cashout;
			$total = $cashin - $cashout;
			return $total;
		}
        public function insertNotificaiton($notif){

			$data = array(
				'title' 	=> $notif["message"],
				'user_id' 	=> 1,
				'link' 		=> $notif["link"],
			);

			return $this->db->insert("notification",$data);
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
