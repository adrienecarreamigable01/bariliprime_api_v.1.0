<style>
/* @page { size: 10cm 15cm; } */
/* -------------------------------------
    GLOBAL
    A very basic CSS reset
------------------------------------- */
* {
    margin: 0;
    padding: 0;
    font-family: "Helvetica Neue", "Helvetica", Helvetica, Arial, sans-serif;
    box-sizing: border-box;
    font-size: 14px;
}

img {
    max-width: 100%;
}

body {
    -webkit-font-smoothing: antialiased;
    -webkit-text-size-adjust: none;
    width: 100% !important;
    height: 100%;
    line-height: 1.6;
}

/* Let's make sure all tables have defaults */
table td {
    vertical-align: top;
}

/* -------------------------------------
    BODY & CONTAINER
------------------------------------- */
body {
    background-color: #f6f6f6;
}

.body-wrap {
    background-color: #f6f6f6;
    width: 100%;
}

.container {
    display: block !important;
    max-width: 100% !important;
    margin: 0 auto !important;
    /* makes it centered */
    clear: both !important;
}

.content {
    max-width: 600px;
    max-width: 100% !important;
    display: block;
    padding: 0px;
}

/* -------------------------------------
    HEADER, FOOTER, MAIN
------------------------------------- */
.main {
    background: #fff;
    border: 1px solid #e9e9e9;
    border-radius: 3px;
}

.content-wrap {
    padding: 0px;
}

.content-block {
    padding: 0 0 4px;
}

.header {
    width: 100%;
    margin-bottom: 4px;
}

.footer {
    width: 100%;
    clear: both;
    color: #000000;
    padding: 4px;
	font-size:8px !important;
	position:absolute;
	bottom:0;
}
.footer a {
    color: #000000;
}
.footer p, .footer a, .footer unsubscribe, .footer td {
    font-size:8px;
}

/* -------------------------------------
    TYPOGRAPHY
------------------------------------- */
h1, h2, h3 {
    font-family: "Helvetica Neue", Helvetica, Arial, "Lucida Grande", sans-serif;
    color: #000;
    margin: 40px 0 0;
    line-height: 1.2;
    font-weight: 400;
}

h1 {
    font-size: 8px !important;
    font-weight: 500;
}

h2 {
    font-size: 8px !important;
}

h3 {
    font-size: 8px !important;
}

h4 {
    font-size: 8px !important;
    font-weight: 600;
}

p, ul, ol {
    margin-bottom: 10px;
    font-weight: normal;
}
p li, ul li, ol li {
    margin-left: 4px;
    list-style-position: inside;
}

/* -------------------------------------
    LINKS & BUTTONS
------------------------------------- */
a {
    color: #1ab394;
    text-decoration: underline;
}

.btn-primary {
    text-decoration: none;
    color: #FFF;
    background-color: #1ab394;
    border: solid #1ab394;
    border-width: 4px 10px;
    line-height: 2;
    font-weight: bold;
    text-align: center;
    cursor: pointer;
    display: inline-block;
    border-radius: 4px;
    text-transform: capitalize;
}

/* -------------------------------------
    OTHER STYLES THAT MIGHT BE USEFUL
------------------------------------- */
.last {
    margin-bottom: 0;
}

.first {
    margin-top: 0;
}

.aligncenter {
    text-align: center;
}

.alignright {
    text-align: right;
}

.alignleft {
    text-align: left;
}

.clear {
    clear: both;
}

/* -------------------------------------
    ALERTS
    Change the class depending on warning email, good email or bad email
------------------------------------- */
.alert {
    font-size: 16px;
    color: #fff;
    font-weight: 500;
    padding: 20px;
    text-align: center;
    border-radius: 3px 3px 0 0;
}
.alert a {
    color: #fff;
    text-decoration: none;
    font-weight: 500;
    font-size: 16px;
}
.alert.alert-warning {
    background: #f8ac59;
}
.alert.alert-bad {
    background: #ed5565;
}
.alert.alert-good {
    background: #1ab394;
}

/* -------------------------------------
    INVOICE
    Styles for the billing table
------------------------------------- */
.invoice {
    margin: 2px auto;
    text-align: left;
    width: 100%;
}
.invoice td {
    padding: 3px !important;
}
.invoice .invoice-items {
    width: 100%;
}
.invoice .invoice-items td {
    border-top: #eee 1px solid;
}
.invoice .invoice-items .total td {
    border-top: .5px solid #333;
    border-bottom: .5px solid #333;
    font-weight: 700;
}

/* -------------------------------------
    RESPONSIVE AND MOBILE FRIENDLY STYLES
------------------------------------- */
@media only screen and (max-width: 640px) {
    h1, h2, h3, h4 {
        font-weight: 600 !important;
        margin: 20px 0 4px !important;
    }

    h1 {
        font-size: 4px !important;
    }

    h2 {
        font-size: 4px !important;
    }

    h3 {
        font-size: 4px !important;
    }

    .container {
        width: 100% !important;
    }

    .content, .content-wrap {
        padding: 1px !important;
    }

    .invoice {
        width: 100% !important;
    }
}
<?php
	function numberFormat($number){
		$formatted =  number_format($number,2,".",",");
		return $formatted;
	}
	function numberFormatWithSign($number){
		$formatted =  number_format($number,2,".",",");
		return "Php. ".$formatted;
	}
?>
</style>
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
<table class="body-wrap">
    <tbody><tr>
        <!-- <td></td> -->
        <td class="container" width="100%">
            <div class="content">
                <table class="main" width="100%" cellpadding="0" cellspacing="0" style="font-size:8px !important;margin-top:10px;">
                    <tbody>
						<tr>
							<td class="content-wrap aligncenter">
								<table width="100%" cellpadding="0" cellspacing="0">
									<tbody>
										<tr>
											<td class="content-block text-center mt-3">
												<h2>BARILIPRIME LENDING CORPORATION.</h2>
											</td>
										</tr>
										<tr>
											<td class="content-block">
												<table class="invoice" style="font-size:5px !important;">
													<tbody>
														<tr>
															<td style="width:100%">
																<span class="text-left">Name: <?php echo strtoupper($data['borrower']) ?></span>
																<span class="float-right">Date: <?php echo date("Y-m-d",strtotime($data['date'])) ?></span>
															</td>
														</tr>
														<tr>
															<td>
																<table class="invoice-items" cellpadding="0" cellspacing="0">
																	<tbody>
																	<?php
																		$total = 0;
																		$net = 0;
																		$def_payment = 0;
																		foreach ($data['data'] as $key => $value) {
																			$v = json_decode($value);
																			$total  += $v->amount;
																			echo "<tr>
																				<td width='50%'>".$v->payment_type."(".$v->name.")"."</td>
																				<td width='50%' class='text-right'>".numberFormat($v->amount,2,".",",")."</td>
																			</tr>";
																		}
																		$total += $data['insurance'];
																		echo "<tr>
																			<td class='text-left' width='50%'>Insurance</td>
																			<td class='text-right' width='50%'>".numberFormat($data['insurance'])."</td>
																		</tr>";
																		echo '<tr class="total">
																				<td class="alignright" width="50%">Total: </td>
																				<td class="text-right" width="50%">'.numberFormat($total,2,".",",").'</td>
																			</tr>';
																		echo "<tr>
																			<td width='50%'>Salary</td>
																			<td width='50%' class='text-right'>".numberFormat($data['salary'])."</td>
																		</tr>";
																		$net = $data['salary'] - $total;
																		$balance = $net;
																		// echo $net > 0 ? '<tr class="total">
																		// 		<td class="alignright" width=50%">Net: </td>
																		// 		<td class="text-right text-success right" width="50%">'.numberFormat($net,2,".",",").'</td>
																		// 	</tr>' : '<tr class="total">
																		// 	<td class="alignright" width="50%">Net: </td>
																		// 	<td class="text-right text-danger" width="50%">'.numberFormat($net,2,".",",").'</td>
																		// </tr>';

																		if($data['advance'] > 0){
																			
																			echo "<tr>
																				<td class='alignright' width='50%'>Advance</td>
																				<td class='text-right' width='50%'>".numberFormat($data['advance'])."</td>
																			</tr>";
																			
																			$balance = $data['salary'] + $data['advance'] - $total;
																		}

																		if($data['def_payment'] > 0){

																			$remaingDefAmount = $data['salary'] + $data['advance'] - $total;
																			echo '<tr class="total">
																					<td class="alignright" width=50%">Remaining Deficit Amount: </td>
																					<td class="text-right text-danger right" width="50%">'.numberFormat($remaingDefAmount,2,".",",").'</td>
																			</tr>';

																			$def_payment = $data['def_payment'];
																			echo '<tr class="total">
																					<td class="alignright" width=50%">Deficit Payment: </td>
																					<td class="text-right text-muted right" width="50%">'.numberFormat($def_payment,2,".",",").'</td>
																			</tr>';

																			$balance = ($data['salary'] + $data['advance'] + $def_payment)  - $total;
																		}

																		
																		
																		echo '<tr class="total">
																				<td class="alignright" width=50%"></td>
																				<td class="text-right text-success right" width="50%">'.numberFormatWithSign((int)$balance,2,".",",").'</td>
																		</tr>';
																	?>
																</tbody></table>
															</td>
														</tr>
													</tbody>
												</table>
											</td>
										</tr>
									<tr>
										<td class="content-block">
											<table style="width:100%;font-size:4px !important;">
												<tr>
													<td style="width:50%" class="text-center">
														<span style="text-decoration:underline"> <?php echo strtoupper($data['by']) ?> </span>
														<div>Prepared By:</div>
													</td>
													<td style="width:50%;" class="text-center">
														<span style="text-decoration:underline"> <?php echo strtoupper($data['borrower']) ?> </span>
														<div>Payor:</div>
													</td>
												</tr>
											</table>
										</td>
									</tr>
								</tbody>
							</table>
							</td>
						</tr>
					</tbody>
				</table>
                <div class="footer">
                    <table width="100%" style="font-size:4px !important;">
                        <tbody>
						<tr>
                            <td class="aligncenter content-block">
								<div>Email: <a href="mailto:">bariliprime.lending2010@gmail.com</a></div>
								<div>Contact us: @ 470-9651</div>
								<div>Schedule: @ <a href="https://meeow.doitcebu.com">Meeow</a></div>
							</td>
                        </tr>
                    </tbody></table>
                </div></div>
        </td>
        <td></td>
    </tr>
</tbody></table>
