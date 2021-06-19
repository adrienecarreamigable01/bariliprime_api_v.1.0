<?php

	class CapitalModel extends CI_Model{
		function addCapitalDef(){
			$loan_id = $this->input->post('loan_id');
			$loan_data 	= $this->get_single_loan($loan_id)[0];
			$data = array(
				'amount' 			=> $this->remove_comma($this->input->post('capital_amount')),
				'interest_rate' 	=> $this->input->post('interest_rate'),
				'released_amount' 	=> $this->remove_comma($this->input->post('total_amount')),
				'monthly_payment' 	=> $this->remove_comma($this->input->post('monthly_payment')),
				'processing_fee' 	=> $this->remove_comma($this->input->post('processing_fee')),
				'service_charge' 	=> $this->remove_comma($this->input->post('service_charge')),
				'loan_id' 			=> $this->input->post('loan_id'),
				'term' 			=> $this->input->post('term'),
				'note' 				=> $this->input->post('description'),
				'net_interest' 		=> $this->remove_comma($this->input->post('net')),
				'transact_by' 		=> $_SESSION['user_id'],
			);
			$res = $this->db->insert('loan_add_capital',$data);
			if($res){
				$insert_logs = array(
					'logs' => "#{$loan_id} {$loan_data->loan_product} Loan added capital amount of".$data['amount']." for the date ".date("Y-m-d H:i:s").' by '. $_SESSION['name'],
					'loan_id' => $loan_id,
					'capital_id' => $this->db->insert_id(),
					'borrower_id'=> $loan_data->borrower_id
				);
				$insert_logs_res = $this->db->insert("logs",$insert_logs);

				if($insert_logs_res){
					$notif 	= array(
						'message' => 'New Capital has been added by '. $_SESSION['name'],
						'link' => 'view/addedcapital'
					);
	
					return $this->insertNotificaiton($notif);
				}else{
					return true;
				}
			}else{
				return false;
			}
		}
	}
?>
