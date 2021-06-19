
<?php
    /**
     * @author  Adriene Care Llanos Amigable <adrienecarreamigable01@gmail.com>
     * @version 0.1.0
    */ 
    class capital extends CI_Controller{
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
		public function add_capital_def()
		{

			$data = $this->add_capital_def->add_capital_def();
			if($data)
			{
				$result['message'] = "success";
			}
			else
			{
				$result['message'] = "error";
			}
			echo json_encode($result);
		}
        public function all()
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
                        loan_add_capital.is_released,
                        loan_add_capital.due_date,
                        loan_add_capital.status_id,
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
        public function count()
		{
            try {

                $status_id      = !empty($_GET['status_id']) ? $_GET['status_id'] : 2;
                $is_released    = !empty($_GET['is_released']) ? $_GET['is_released'] : 0;

                $sql = "SELECT  count(loan_add_capital.loan_add_capital_id) as total
                        FROM loan_add_capital
                        LEFT JOIN loan 	ON loan.loan_id = loan_add_capital.loan_id ";

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

                $sql .= "AND loan.is_active = 1";

                $result =  $this->db->query($sql)->result();

                $this->res = array(
                    'isError' => false,
                    'message' => "Success",
                    'data'    => !empty($result) ? $result[0]->total : 0,
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
        public function get_single_capital($capital_id)
		{
			$sql = "SELECT loan_add_capital.loan_add_capital_id,loan_add_capital.amount,loan_add_capital.monthly_payment,loan_add_capital.released_amount,loan_add_capital.interest_rate,loan_add_capital.deficit_amount,
				           loan_add_capital.loan_id,loan_add_capital.transact_date,loan_add_capital.transact_by,loan_add_capital.processing_fee,loan_add_capital.released_by,
				           loan_add_capital.released_date,loan_add_capital.due_date,loan_add_capital.is_released,loan_add_capital.is_void,loan_add_capital.void_date,loan_add_capital.note,
				           loan_add_capital.status_id, loan.principal_amount,loan.total_amount,loan.borrower_id,loan.loan_product_id,loan.term,CONCAT(user.firstname,' ',user.lastname) as user,
						   CONCAT(borrower.lastname,', ',borrower.firstname,' ',borrower.middlename) as 'name',loan_product.name as loan_product
				   FROM loan_add_capital
				   LEFT JOIN loan ON loan.loan_id 				= loan_add_capital.loan_id
				   LEFT JOIN borrower 		ON borrower.borrower_id 		= loan.borrower_id
				   LEFT JOIN user 			ON user.user_id 				= loan_add_capital.transact_by
				   LEFT JOIN loan_product 	ON loan_product.loan_product_id = loan.loan_product_id
                   WHERE loan_add_capital.loan_add_capital_id = {$capital_id}";
			return $this->db->query($sql)->result();
		}
        public function approve()
		{
            try {
                if(empty($_POST['capital_id'])){
                    $this->res = array(
                        'isError' => true,
                        'message'   => "Empty / Null Capital Id",
                        'date'    => date("Y-m-d"),  
                    );
                }
                if(empty($_POST['name'])){
                    $this->res = array(
                        'isError' => true,
                        'message'   => "Empty name",
                        'date'    => date("Y-m-d"),  
                    );
                }
                else{
                    $capital_id 			= $_POST['capital_id'];
                    $date_released 			= !empty($_POST['date_released']) ? $_POST['date_released'] : date("Y-m-d");
                    $capital_data 			= $this->get_single_capital($capital_id)[0];
                    // $incentive_product = array(3,4,5,6,11,8,9,10,13,14);
                    $incentive_product = array(3,4,5,11,8,9,10,13,14);

                    if(in_array($capital_data->loan_product_id,$incentive_product)){
                        $due_date  				= date('Y-m-d', strtotime("+".$capital_data->term." months", strtotime($date_released)));
                    }else{
                        $due_date  				= date('Y-m-d', strtotime("+2 months", strtotime($date_released)));
                    }
                    
                    
                    
                    $capital = array(
                        'status_id'		 	=> 1,
                        'released_date'		=> $date_released,
                        'due_date'			=> $due_date ,
                    );
                    $this->db->where('loan_add_capital_id',$capital_id);
                    $res =  $this->db->update('loan_add_capital',$capital);
                    if($res){
                        $notif 	= array(
                            'message' => 'One Capital has been approved by '. $_POST['name'],
                            'link' => 'borrower/loan/'.$capital_data->loan_id
                        );

                        $resn = $this->insertNotificaiton($notif);

                        if($resn){
                            $loan_data 	= $this->get_single_loan($capital_data->loan_id)[0];
                            $insert_logs = array(
                                'logs' => "Loan {$loan_data->loan_product} has one approve a capital {$capital_id} ".date("Y-m-d H:i:s").' by '. $_POST['name'],
                                'capital_id' => $capital_id,
                                'loan_id' => $capital_data->loan_id,
                                'borrower_id' => $capital_data->borrower_id,
                            );
                            $this->db->insert('logs',$insert_logs);
                        }

                        $this->res = array(
                            'isError'   => false,
                            'message'   => "Successfully approve capital",
                            'date'      => date("Y-m-d"),  
                        );

                    }else{
                        $this->res = array(
                            'isError' => true,
                            'message'   => "Error on approve capital",
                            'date'    => date("Y-m-d"),  
                        );
                    }
                }
                
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
        public function release()
		{
            try{
                if(empty($_POST['capital_id'])){
                    $this->res = array(
                        'isError' => true,
                        'message'   => "Empty / NULL capital id",
                        'date'    => date("Y-m-d"),  
                    );
                }
                else if(empty($_POST['userId'])){
                    $this->res = array(
                        'isError' => true,
                        'message'   => "Empty / NULL user id",
                        'date'    => date("Y-m-d"),  
                    );
                }
                else if(empty($_POST['name'])){
                    $this->res = array(
                        'isError' => true,
                        'message'   => "Empty / NULL name",
                        'date'    => date("Y-m-d"),  
                    );
                }
                else{

                    $capital_id 			= $_POST['capital_id'];
                    
                    $capital_data 			= $this->get_single_capital($capital_id)[0];
                    $payload = array(
                        'primary_key' 	=> 'add_capital_id',
                        'id' 			=> $capital_id,
                    );

                    $inVault  		= $this->checkifInVault($payload);
                
                    $vault_balance  = $this->get_vault_balance();

                    if($vault_balance < $capital_data->released_amount)
                    {
                        $this->res = array(
                            'isError' => true,
                            'message'   => "Insufficient amount",
                            'date'    => date("Y-m-d"),  
                        );
                    }
                    else if(!empty($inVault)){
                        $this->res = array(
                            'isError' => true,
                            'message'   => "Capital already released, Please reload your browser",
                            'date'    => date("Y-m-d"),  
                        );
                    }
                    else
                    {
                        $capital_result = $this->update_capital($capital_id);
                        $allow_change_auto_product = array(1,3,4,5,9,11,8,10,13,14);
           
                        if($capital_result)
                        {
                            $capital_data = $this->get_single_capital($capital_id)[0];
                            
                            if($capital_data->loan_product_id != 1)
                            {
                                $loan_data = $this->get_single_loan($capital_data->loan_id)[0];
                                $loan = array(
                                    'total_amount' 		=> $loan_data->total_amount + $capital_data->amount,
                                );
                                if(in_array($loan_data->loan_product_id,$allow_change_auto_product)){
                                    $loan['monthly_payment'] = $loan_data->monthly_payment + $capital_data->monthly_payment;
                                }
                                $this->db->where('loan_id',$capital_data->loan_id);
                                $loan_result = $this->db->update('loan',$loan);


                                if($loan_result)
                                {
                                    //insert data to vault
                                    $vault = array(
                                            'trasaction_type_id' 	=> 2,
                                            'vault_type' 			=> 6,
                                            'add_capital_id' 		=> $capital_id,
                                            'user_id' 				=> $_POST['userId'],
                                            'description' 			=> "Add ".$capital_data->loan_product." Capital Amount of mr/ms ".$capital_data->name,
                                            'amount' 				=> $capital_data->released_amount,
                                    );

                                    $res = $this->db->insert('vault',$vault);
                                    if($res){

                                        $notif 	= array(
                                            'message' => 'One Capital has been released by '. $_POST['name'],
                                            'link' => 'borrower/loan/'.$capital_data->loan_id
                                        );
                        
                                        $r =  $this->insertNotificaiton($notif);
                                        if($r)
                                        {
                                            $loan_data 	= $this->get_single_loan($capital_data->loan_id)[0];
                                            $insert_logs = array(
                                                'logs' => "Loan {$loan_data->loan_product} has been released Capital # {$capital_id} for the date of ".date("Y-m-d H:i:s").' by '. $_POST['name'],
                                                'capital_id' => $capital_id,
                                                'loan_id' => $capital_data->loan_id,
                                                'borrower_id' => $capital_data->borrower_id,
                                            );
                                            $this->db->insert('logs',$insert_logs);

                                            $this->res = array(
                                                'isError' => false,
                                                'message'   => "Successfully Release Capital",
                                                'date'    => date("Y-m-d"),  
                                            );
                                            
                                        }
                                        else
                                        {
                                            $this->res = array(
                                                'isError' => true,
                                                'message'   => "Error Release Capital",
                                                'date'    => date("Y-m-d"),  
                                            );
                                        }
                                    }
                                    
                                }
                                else{
                                    $this->res = array(
                                        'isError' => true,
                                        'message'   => "Error getting loan result",
                                        'date'    => date("Y-m-d"),  
                                    );
                                }
                            }
                            else
                            {
                                // $ca_data = $this->get_single_ca($capital_data->loan_id)[0];

                                // $ca = array(
                                //     'amount' 			=> ($ca_data->amount + $capital_data->amount),
                                //     'monthly_payment' 	=> ($ca_data->amount + $capital_data->amount) * .03,
                                //     'due_date' 			=> date('Y-m-d', strtotime("+2 months", strtotime($ca_data->due_date))),
                                // );
                                // $this->db->where('loan_id',$capital_data->loan_id);
                                // $ca_result = $this->db->update('loan_cashadvance',$ca);


                                // if($ca_result)
                                // {
                                //     //insert data to vault
                                //     $vault = array(
                                //             'trasaction_type_id' 	=> 2,
                                //             'vault_type' 			=> 6,
                                //             'user_id' 				=> $_SESSION['user_id'],
                                //             'description' 			=> "Add ".$capital_data->loan_product." Capital Amount of mr/ms ".$capital_data->name,
                                //             'amount' 				=> $capital_data->released_amount,
                                //     );

                                //     $res = $this->db->insert('vault',$vault);
                                //     if($res)
                                //     {
                                //         return "success";
                                //     }
                                //     else
                                //     {
                                //         return "error";
                                //     }
                                // }
                                // else{
                                //     return "error";
                                // }
                            }
                        }
                        else{
                            $this->res = array(
                                'isError' => true,
                                'message'   => "Error Getting Capital Result",
                                'date'    => date("Y-m-d"),  
                            );
                        }
                    }
                }
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
		public function update()
		{
			$arrayTransaction = array();
            $capital_id = $this->input->post('capital_id');
            $due_date = $this->input->post('due_date');
            $name = $this->input->post('name');
            if(empty($capital_id)){
                
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
                    $this->db->where('loan_add_capital_id',$capital_id);
                    // $update_loan_res = $this->db->update('loan',$update_loan);
    
                    $sql1 = $this->db->set($update_loan)->get_compiled_update('loan_add_capital');
                    array_push($arrayTransaction,$sql1);
                        
    
					$capital_data = $this->get_single_capital($capital_id)[0];
					$insert_logs = array(
						'logs'          => "Update a capital {$capital_id} ".date("Y-m-d H:i:s").' by '. $_POST['name'],
						'capital_id'    => $capital_id,
						'loan_id'       => $capital_data->loan_id,
						'borrower_id'   => $capital_data->borrower_id,
					);

                    $logs_res =$this->db->set($insert_logs)->get_compiled_insert('logs');
                    array_push($arrayTransaction,$logs_res);
    
                    if(!empty($arrayTransaction)){
                        $result = array_filter($arrayTransaction);   
                        $res = $this->mysqlTQ($result);
                        if($res){
                            $this->res = array(
                                'isError' => false,
                                'message'   => "Successfuly Updated Capital",
                                'date'    => date("Y-m-d"),  
                            );
                        }else{
                            $this->res = array(
                                'isError' => true,
                                'message'   => "Error Updated Capital",
                                'date'    => date("Y-m-d"),  
                            );
                        }
                    }else{
                        $this->res = array(
                            'isError' => true,
                            'message'   => "Error Updated Capital",
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
        public function void()
		{
            if(empty($_POST['capital_id'])){
                $this->res = array(
                    'isError' => true,
                    'message'   => "Empty | Null Capital Id",
                    'date'    => date("Y-m-d"),  
                );
            }
            else if(empty($_POST['name'])){
                $this->res = array(
                    'isError' => true,
                    'message'   => "Empty | Null Name",
                    'date'    => date("Y-m-d"),  
                );
            }
            else{
                try{
                    $capital_id = $_POST['capital_id'];
                    $update_loan_capital = array(
                                'status_id' => 6,
                                'void_date' => date('Y-m-d'),
                                'note'		=> $this->input->post('note')
                            );
                    $this->db->where('loan_add_capital_id',$capital_id);
                    $res =  $this->db->update('loan_add_capital',$update_loan_capital);
                    if($res){
                        $capital_data = $this->get_single_capital($capital_id)[0];
                        $insert_logs = array(
                            'logs'          => "Void a capital  {$capital_id} ".date("Y-m-d H:i:s").' by '. $_POST['name'],
                            'capital_id'    => $capital_id,
                            'loan_id'       => $capital_data->loan_id,
                            'borrower_id'   => $capital_data->borrower_id,
                        );

                        $this->db->insert('logs',$insert_logs);

                    }

                    $this->res = array(
                        'isError'   => false,
                        'message'   => "Successfully Disapprove Capital",
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
            }

            $this->displayJSON($this->res);
		}
        public function update_capital($capital_id)
		{
			$capital_data 	= $this->get_single_capital($capital_id)[0];
			$sql = array(
				'is_released' => 1,
			);
			$this->db->where('loan_add_capital_id',$capital_id);
			$update_capital = $this->db->update('loan_add_capital',$sql);
			if($update_capital){
				return $this->get_single_loan($capital_data->loan_id)[0];
			}else{
				return false;
			}
		}
        public function checkifInVault($payload){
			$sql = "SELECT * FROM vault
					WHERE {$payload['primary_key']} = {$payload['id']} AND vault.is_void = 0";
			return $this->db->query($sql)->result();
        }
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
