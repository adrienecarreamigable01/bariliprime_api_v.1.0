<?php
    class BorrowerModel extends CI_Model{
		public function getBorrowerSchedule($borrower_id){
            $sql = "SELECT schedules.id,
                            schedules.name as title,
                            schedules.borrower_id,
                            -- DATE_FORMAT(schedules.start,'%H:%i') as title,
                            schedules.start,
                            schedules.end,
                            schedules.description,
                            schedules.meridiem,
                            schedules.status_id
                    FROM schedules
                    WHERE schedules.is_active = 1 AND schedules.borrower_id = {$borrower_id}";
            return $this->db->query($sql)->result();
        }
        public function proc_add_borrower($image='no-image.jpg'){
			$borrower = array(
				'firstname'  		=> $this->input->post('firstname'),
				'lastname'   		=> $this->input->post('lastname'),
				'middlename'  		=> $this->input->post('middlename'),
				'district_id'  		=> $this->input->post('district'),
				'date_registered'  	=> date('Y-m-d'),
				'image' 			=> $image,
			);
			$borrower_res = $this->db->insert('borrower',$borrower);
			$borrower_id = $this->db->insert_id();
           
			if($borrower_res)
			{
				$this->proc_add_borrower_contact($borrower_id);
				$this->proc_add_borrower_details($borrower_id);
				$this->proc_add_borrower_spouse($borrower_id);
				$this->add_comaker1($borrower_id);
				$this->add_comaker2($borrower_id);
				$this->insert_logs("{$borrower['firstname']}, {$borrower['lastname']}",$borrower_id);
               
            }
            
        }
        public function insert_logs($name,$borrower_id){
            $data = array(
                'logs'          => "Added new borrower name {$name} for the date of ".date("Y-m-d H:i:s").' by'. $_SESSION['name'],
                'borrower_id'   => $borrower_id,
            );
            $insert_logs_res    = $this->db->insert("logs",$data);
        }
        public function proc_add_borrower_contact($borrower_id){
            $borrower_contact = array(
                'borrower_id' => $borrower_id,
                'mobile' 	  => $this->input->post('mobile'),
                'telephone'   => $this->input->post('landline'),
                'email'  	  => $this->input->post('email'),
            );
            return $this->db->insert('borrower_contact',$borrower_contact);
        }
        public function proc_add_borrower_details($borrower_id){
            $borrower_details = array(
                'borrower_id' 			=> $borrower_id,
                'gender' 	  			=> $this->input->post('gender'),
                'birthdate'   			=> $this->input->post('dateofbirth'),
                'present_address'  		=> $this->input->post('address'),
                'position'  	  		=> $this->input->post('position'),
                'id_name'  	  			=> $this->input->post('id_name'),
                'id_number'  	  		=> $this->input->post('id_number'),
                'income'  	  			=> $this->input->post('income'),
                'gross'  	  			=> $this->input->post('gross'),
                'net'  	  				=> $this->input->post('net'),
            );
            return $this->db->insert('borrower_details',$borrower_details);
        }
        public function proc_add_borrower_spouse($borrower_id){
            $spouse = array(
                'borrower_id'	=> $borrower_id,
                'lastname' 	  	=> $this->input->post('spouse_lastname'),
                'firstname' 	=> $this->input->post('spouse_firstname'),
                'middlename' 	=> $this->input->post('spouse_middlename'),
                'birthdate' 	=> $this->input->post('spouse_dateofbirth'),
                'landline' 		=> $this->input->post('spouse_landline'),
                'mobile' 		=> $this->input->post('spouse_mobile'),
                'email' 		=> $this->input->post('spouse_email'),
            );
            return $this->db->insert('spouse',$spouse);
        }
        public function add_comaker1($borrower_id){
            $comaker1 = array(
                'borrower_id'	=> $borrower_id,
                'lastname' 	  	=> $this->input->post('comaker1_lastname'),
                'firstname' 	=> $this->input->post('comaker1_firstname'),
                'middlename' 	=> $this->input->post('comaker1_middlename'),
                'address' 		=> $this->input->post('comaker1_dateofbirth'),
                'mobile' 		=> $this->input->post('comaker1_mobile'),
                'landline' 		=> $this->input->post('comaker1_landline'),
                'valid_id_no' 	=> $this->input->post('comaker1_valid_id'),
                'date_issued' 	=> $this->input->post('comaker1_date_issued'),
                'place_issued' 	=> $this->input->post('comaker1_place_issued'),
            );
            return $this->db->insert('comaker',$comaker1);
        }
        public function add_comaker2($borrower_id){
            $comaker2 = array(
                'borrower_id'	=> $borrower_id,
                'lastname' 	  	=> $this->input->post('comaker2_lastname'),
                'firstname' 	=> $this->input->post('comaker2_firstname'),
                'middlename' 	=> $this->input->post('comaker2_middlename'),
                'address' 		=> $this->input->post('comaker2_dateofbirth'),
                'mobile' 		=> $this->input->post('comaker2_mobile'),
                'landline' 		=> $this->input->post('comaker2_landline'),
                'valid_id_no' 	=> $this->input->post('comaker2_valid_id'),
                'date_issued' 	=> $this->input->post('comaker2_date_issued'),
                'place_issued' 	=> $this->input->post('comaker2_place_issued'),
            );
            return $this->db->insert('comaker',$comaker2);
        }
        public function activateAccount($borrower_id){
            $sql = "UPDATE borrower_account 
                    SET borrower_account.is_verified = 1
                    WHERE borrower_account.borrower_id = '{$borrower_id}'";
            $res =  $this->db->query($sql);
            if($res){
                $message = "User has been activated by {$user} for the date of {$date}";
                $resLogs = $this->insertLogs($borrower_id,$message);
               if($resLogs){
                    return true;
               }else{
                    return true;
               }
            }else{
                return true;
            }
        }
        public function insertLogs($borrower_id,$message){
            
            $sql = "INSERT INTO `logs` (`log_id`, `logs`, `log_date`, `loan_id`, `borrower_id`, `payment_id`, `capital_id`, `cashadvance_id`, `vault_id`, `released_incentive_id`, `incentive_id`, `salary_id`, `user_id`, `module_id`, `bank_id`, `expense_id`) 
                    VALUES (NULL,'$message', current_timestamp(), '0', '{$borrower_id}', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0')";
             return $this->db->query($sql);
        }
        public function overRideBorrowerPassword($borrower_id,$user){
            $date = date("Y-m-d");
            $sql = "UPDATE borrower_account 
                    SET borrower_account.password = 'Password1'
                    WHERE borrower_account.borrower_id = '{$borrower_id}'";
            $res =  $this->db->query($sql); 
            if($res){
                $message = "User password has been override by {$user} for the date of {$date}";
                $resLogs = $this->insertLogs($borrower_id,$message);
               if($resLogs){
                    return true;
               }else{
                    return true;
               }
            }else{
                return true;
            }
        }
        public function checkOldPassword($borrower_id,$password){
            $sql = "SELECT * FROM borrower_account
                    WHERE borrower_account.borrower_id = '{$borrower_id}' AND borrower_account.password = '{$password}'";
            return $this->db->query($sql)->result();
        }
        public function checkOldUsername($borrower_id,$username){
            $sql = "SELECT * FROM borrower_account
                    WHERE borrower_account.borrower_id = '{$borrower_id}' AND borrower_account.username = '{$username}'";
            return $this->db->query($sql)->result();
        }
        public function changePassword($borrower_id,$password){
            $date = date("Y-m-d");
            $sql = "UPDATE borrower_account 
                    SET borrower_account.password = '{$password}'
                    WHERE borrower_account.borrower_id = '{$borrower_id}'";
            $res =  $this->db->query($sql); 
            if($res){
                $message = "User change password for the date of {$date}";
                $resLogs = $this->insertLogs($borrower_id,$message);
               if($resLogs){
                    return true;
               }else{
                    return true;
               }
            }else{
                return true;
            }
        }
        public function changeUsername($borrower_id,$username){
            $date = date("Y-m-d");
            $sql = "UPDATE borrower_account 
                    SET borrower_account.username = '{$username}'
                    WHERE borrower_account.borrower_id = '{$borrower_id}'";
            $res =  $this->db->query($sql); 
            if($res){
                $message = "User change username for the date of {$date}";
                $resLogs = $this->insertLogs($borrower_id,$message);
               if($resLogs){
                    return true;
               }else{
                    return true;
               }
            }else{
                return true;
            }
		}
		public function get_request(){
			$sql = "SELECT borrower_requests.borrower_requests_id,borrower_requests.borrower_requests,borrower_requests.date,borrower_requests.borrower_id,borrower_requests.note,borrower_requests.status_id,CONCAT(borrower.lastname,' ,',borrower.firstname,' ',borrower.middlename) as name,status.name as 'status'
					FROM borrower_requests 
					LEFT JOIN borrower ON borrower.borrower_id = borrower_requests.borrower_id
					LEFT JOIN status ON status.status_id = borrower_requests.status_id";
			return $this->db->query($sql)->result();
		}
        public function getAccountToSync(){
            $sql = "SELECT borrower_account.borrower_id FROM borrower_account ORDER BY borrower_account_id DESC LIMIT 1";
            return $this->db->query($sql)->result();
        }
        public function addAccount($payload){
            return $this->db->insert('borrower_account',$payload);
        }
    }
?>
