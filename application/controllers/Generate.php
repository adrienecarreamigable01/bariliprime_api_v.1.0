<?php
	ini_set('memory_limit', '-1');
	date_default_timezone_set('Asia/Manila');
	use Dompdf\Dompdf;
	use Dompdf\Options;
	class generate extends CI_Controller{
		public function __construct() {
			parent::__construct();
			$this->load->library('Pdf',NULL,'pdf');
		}
	  	public function monthly_receipt()
		{
			// print_r($_GET);exit;
			ini_set('max_execution_time', 300); //300 seconds = 5 minutes

			if(!empty($_GET)){
				
				// print_r($data['data']['data']);exit;
				// $this->load->view('report/monthly_receipt',$data);
				// $paper = 'A8';
				// $orientation = "landscape";
				// $dompdf = new Dompdf();
				// $customPaper = array(0,0,4.25,6.5);
				// $dompdf->setPaper($paper, $orientation);
				$name= "Receipt";
				$data['title'] = $name;
				$data['data'] = json_decode($_GET['accounts'],true);
				// print_r($data['data']);exit;
				// $this->pdf->load_view('report/monthly_receipt',$data);
				// $this->pdf->render();
				// $this->pdf->stream($name."-payment-report.pdf", array('Attachment'=>0));

				$this->pdf->load_view5_portrait($name,'report/monthly_receipt',$data);
			}else{
				print_r("Input Required");
			}
			
		}
	  	public function paymentReport()
		{
            $from       = date("Y-m-d",strtotime($_GET['from']));
            $to       = date("Y-m-d",strtotime($_GET['to']));
            
			ini_set('max_execution_time', 300); //300 seconds = 5 minutes
			$paper = 'letter';
			$orientation = "landscape";
			$dompdf = new Dompdf();
	  		$dompdf->setPaper($paper, $orientation);
			$name= "Payment Report";
            $data['title'] = $name;
            $url = "http://bariliprime-api-v1.doitcebu.com/report/interestPaymentReport?from=$from&to=$to";
			$data['data'] = $this->httpRequest("get",$url);
			// print_r($data['data']['data']);exit;
			$this->pdf->load_view('report/paymentreport',$data);
			$this->pdf->render();
			$this->pdf->stream($name."-payment-report.pdf", array('Attachment'=>0));
			
		}
	  	public function monthlyPaymentReport()
		{
            $from       = date("Y-m-d",strtotime($_GET['from']));
            $to       = date("Y-m-d",strtotime($_GET['to']));
            
			ini_set('max_execution_time', 300); //300 seconds = 5 minutes
			$paper = 'letter';
			$orientation = "landscape";
			$dompdf = new Dompdf();
	  		$dompdf->setPaper($paper, $orientation);
			$name= "Montly Interest Report";
            $data['title'] = $name;
			$url = "http://bariliprime-api-v1.doitcebu.com/report/monthlyInterestPaymentReport?from=$from&to=$to";
			$data['data'] = $this->httpRequest("get",$url);
			$this->pdf->load_view('report/paymentreport',$data);
			$this->pdf->render();
			$this->pdf->stream($name."-monthly-interest-payment-report.pdf", array('Attachment'=>0));
			
		}
	  	public function loanReportReport()
		{
            $from       = date("Y-m-d",strtotime($_GET['from']));
            $to       = date("Y-m-d",strtotime($_GET['to']));
            
			ini_set('max_execution_time', 300); //300 seconds = 5 minutes
			$paper = 'letter';
			$orientation = "landscape";
			$dompdf = new Dompdf();
	  		$dompdf->setPaper($paper, $orientation);
			$name= "Loan Report";
            $data['title'] = $name;
            $url = "http://bariliprime-api-v1.doitcebu.com/report/getLoanReports?from=$from&to=$to";
			$data['data'] = $this->httpRequest("get",$url);
			// print_r($data['data']['data']);exit;
			$this->pdf->load_view('report/loanreport',$data);
			$this->pdf->render();
			$this->pdf->stream($name."-loan-report.pdf", array('Attachment'=>0));
			
		}
		private function httpRequest($request,$url){
			if($request == "get"){

				$connection = curl_init();

				curl_setopt($connection, CURLOPT_URL, $url);
				curl_setopt($connection, CURLOPT_RETURNTRANSFER, true);

				$data = curl_exec($connection);
				curl_close($connection);
				$array = json_decode($data, true);
				return $array;
				
			}else{

			}

		}
	  	public function interestReport(){

            $from       = date("Y-m-d",strtotime($_GET['from']));
            $to       	= date("Y-m-d",strtotime($_GET['to']));
            
			ini_set('max_execution_time', 300); //300 seconds = 5 minutes
			$paper 			= 'letter';
			$orientation 	= "landscape";
			$dompdf 		= new Dompdf();
	  		$dompdf->setPaper($paper, $orientation);
			$name = "Interest Report";
            $data['title'] = $name;
			$url = "http://bariliprime-api-v1.doitcebu.com/report/getInterestEarReport?from=$from&to=$to";
			$data['data'] = $this->httpRequest("get",$url);
	
			$this->pdf->load_view('report/interestreport',$data);
			$this->pdf->render();
			$this->pdf->stream($name."-interest-report.pdf", array('Attachment'=>0));
			
		}
	  	public function salaryReport(){

			$date 	        = isset($_GET['date']) ? date("Y-m",strtotime($_GET['date'])) : date("Y-m-d");
			$district_id 	= isset($_GET['district_id']) ? $_GET['district_id'] : 1;
            
			ini_set('max_execution_time', 300); //300 seconds = 5 minutes
			$paper 			= 'letter';
			$orientation 	= "landscape";
			$dompdf 		= new Dompdf();
	  		$dompdf->setPaper($paper, $orientation);
			$name = "Salary Report";
            $data['title'] = $name;
			$url = "http://bariliprime-api-v1.doitcebu.com/report/borrowerSalaryReport?date=$date&district_id=$district_id";
			$data['data'] = $this->httpRequest("get",$url);
			$this->pdf->load_view('report/salaryreport',$data);
			$this->pdf->render();
			$this->pdf->stream($name."-salary-report.pdf", array('Attachment'=>0));
			
		}
	  	public function incentiveReport(){
			$date 	        = isset($_GET['date']) ? date("Y-m",strtotime($_GET['date'])) : date("Y-m-d");
			$district_id 	= isset($_GET['district_id']) ? $_GET['district_id'] : 1;
            
			ini_set('max_execution_time', 300); //300 seconds = 5 minutes
			$paper 			= 'letter';
			$orientation 	= "landscape";
			$dompdf 		= new Dompdf();
	  		$dompdf->setPaper($paper, $orientation);
			$name = "Incentive Report";
			$data['title'] = $name;
			$url = "http://bariliprime-api-v1.doitcebu.com/report/borrowerIncentiveReport?date=$date&district_id=$district_id";
			$data['data'] = $this->httpRequest("get",$url);
			$this->pdf->load_view('report/incentivereport',$data);
			$this->pdf->render();
			$this->pdf->stream($name."-incentive-report.pdf", array('Attachment'=>0));
		}
	  	public function minivault_report(){

			if(!isset($_GET['user_id'])){
				die("Please select a user");
			}else{
				$date 	        = isset($_GET['date']) ? date("Y-m-d",strtotime($_GET['date'])) : date("Y-m-d");
				$user_id 		= $_GET['user_id'];
				
				ini_set('max_execution_time', 300); //300 seconds = 5 minutes
				$paper 			= 'letter';
				$orientation 	= "landscape";
				$dompdf 		= new Dompdf();
				  $dompdf->setPaper($paper, $orientation);
				$name = "Minivault Report";
				$data['title'] = $name;
				$url = "http://bariliprime-api-v1.doitcebu.com/report/cashierReport?date=$date&user_id=$user_id";
				$data['data'] = $this->httpRequest("get",$url);
				$this->pdf->load_view('report/minivault_report',$data);
				$this->pdf->render();
				$this->pdf->stream($name."-incentive-report.pdf", array('Attachment'=>0));
			}
		}
	}
 ?>
