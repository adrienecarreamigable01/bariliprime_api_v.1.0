<!DOCTYPE html>
<html>
<head>
	<title>Payroll of Mr/Ms <?php echo $payroll[0]->name; ?></title>
	<link rel="stylesheet" href="<?php echo FCPATH.'assets/dashboard/css/bootstrap.min.css'?>" media="all" />
	<!-- <link rel="stylesheet" href="<?php echo FCPATH.'assets/dashboard/css/styles.css'?>" media="all" /> -->
	<style type="text/css">
		body{
			font-weight: none;
			font-size: 11pt;
		}
		.table tr td{
			border: 1px solid black;
			font-size: 10pt;
			height: 20px;
			font-weight: bold;
		}
		.table-condensed > thead > tr > th,
        .table-condensed > tbody > tr > th,
        .table-condensed > tfoot > tr > th,
        .table-condensed > thead > tr > td,
        .table-condensed > tbody > tr > td,
        .table-condensed > tfoot > tr > td {
          padding: 1px !important;
        }
        @media print {
		 body{
		 	margin-top: 0px !important;
		 }
		}
	</style>
</head>
<body>	
	<div class="row">
		<div class="col-12">
			<table>
				<tr>
					<td style="width: 50px;">
						<img style="width: 100px;height: 80px;" src="https://bariliprime.doitcebu.com/assets/img/Logo.png">
					</td>
					<td style="width: 430px;" class="text-center">
						<div class="col-12 text-center">
							<h1>Payslip</h1>
						</div>
					</td>
					<!-- <td>
						<p style="line-height: 1em;font-size: 10px;">
							<b>Office Number: 470-9651</b>
							<b>Home Number: 470-9276</b>
							<b>SEC Registration Number:</b>
							<b>CS201633364/ CEO 38935</b>
							<b>TIN: 483-109-532-000</b>
						</p>
					</td> -->
				</tr>
			</table>
		</div>
	</div>
	<div class="row">
		<div class="col-12">
			<p>Hilarion Alquizola StreetBarili, Cebu</p>
		</div>
	</div>
	<div class="row">
		<div class="col-12">
			<table style="border: 1px solid black;">
				<tr style="background-color: #77a09d">
					<td class="text-center" style="width: 355px;border: 1px solid black;">
						<b>Name:</b>		<?php echo $payroll[0]->name; ?> <br>
					</td>
					<td class="text-center" style="width: 355px;border: 1px solid black;">
						<b>Date:</b>		<?php echo $payroll[0]->payroll_date; ?> <br>
					</td>
					<td class="text-center" style="width: 355px;border: 1px solid black;">
						<b>Employee ID:</b>		<?php echo $payroll[0]->id_number; ?> <br>
					</td>
				</tr>
				<tr>
					<td colspan="3">
						<div class="row">
							<div class="col-12">
								<table style="width:100%;">
									<thead>
										<tr style="background-color: #ccc">
											<th>Earnings</th>
											<th>Adjustments</th>
											<th>Deductions</th>
											<th>Computations</th>
										</tr>
									</thead>
									<tbody>
										<tr tyle="background-color: #77a09d">
											<td style="width:25%;">
												<b>Basic Pay</b> : <?php echo number_format($payroll[0]->basepay * $payroll[0]->no_of_days,2,'.',','); ?><br>
												<b>No of days </b> : <?php echo $payroll[0]->no_of_days; ?>
											</td>
											<td style="width:25%;">
												<b>Overtime</b> : <?php echo number_format($payroll[0]->overtime,2,'.',','); ?><br>
												<b>Paid Leaves</b> : <?php echo number_format($payroll[0]->paid_leaves,2,'.',','); ?><br>
												<b>Transport Allowance</b> : <?php echo number_format($payroll[0]->transport_allowance,2,'.',','); ?><br>
												<b>Bonus</b> : <?php echo number_format($payroll[0]->bonus,2,'.',','); ?><br>
												<b>Medical Allowance</b> : <?php echo number_format($payroll[0]->medical_allowance,2,'.',','); ?><br>
												<b>Other Allowance</b> : <?php echo number_format($payroll[0]->other_allowance,2,'.',','); ?><br>
											</td>
											<td style="width:25%;">
												<b>SSS</b> : <?php echo number_format($payroll[0]->sss,2,'.',','); ?><br>
												<b>Pagibig</b> : <?php echo number_format($payroll[0]->pagibig,2,'.',','); ?><br>
												<b>Philhealth</b> : <?php echo number_format($payroll[0]->philhealth,2,'.',','); ?><br>
												<b>Unpaid Leave</b> : <?php echo number_format($payroll[0]->unpaid_leave,2,'.',','); ?><br>
												<b>Cash Advance</b> : <?php echo number_format($payroll[0]->cash_advance,2,'.',','); ?><br>
											</td>
											<td style="width:25%;">
												<?php
													$total_pay = ($payroll[0]->basepay * $payroll[0]->no_of_days) + $payroll[0]->overtime + $payroll[0]->paid_leaves + $payroll[0]->transport_allowance + $payroll[0]->bonus + $payroll[0]->medical_allowance + $payroll[0]->other_allowance;
													$total_deduction =  $payroll[0]->sss + $payroll[0]->pagibig + $payroll[0]->philhealth + $payroll[0]->unpaid_leave + $payroll[0]->cash_advance;
													$net_pay = $total_pay - $total_deduction;
												?>
												<b>Salary Income</b> : <?php echo number_format($payroll[0]->basepay * $payroll[0]->no_of_days,2,'.',','); ?><br>
												<div style="background-color: #ccc"><b>Salary Adj</b> : <?php echo number_format($total_pay,2,'.',','); ?></div><br>
												----------------------------------------------------<br>
												<div style="background-color: #ccc"><b>Deductions</b> : <?php echo number_format($total_deduction,2,'.',','); ?></div><br>
												----------------------------------------------------<br>
												<div style="background-color: #ccc">
													<b>Net Pay</b> : <?php echo number_format($net_pay,2,'.',','); ?>
												</div>
												<br>

												<img width="100" src="<?php echo $qr; ?>" alt="">

											</td>
										</tr>
									</tbody>
								</table>
							</div>
						</div>
					</td>
				</tr>
				<tr>
					<td>
						
					</td>
					<td>
						
					</td>
					<td classs="text-right">
						<div class="text-right">
							<b>Net Pay</b> : <?php echo number_format($net_pay,2,'.',','); ?>
						</div>
					</td>
				</tr>
			</table>
		</div>
	</div>
</body>
</html>
