<?php
    $total_in = 0;
    $total_out = 0;
    $return_cash = 0;
?>
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <table style="width:100%">
                <tr>
                    <td style="width: 60%;font-size: 20px;" class="text-center">
                        <span id="center-text">
                            <div>Barili Prime Lending Corporation</div>
                            <div>For the Period <?php echo $_GET['date'] ; ?></div>
                            <div>Printed on <?php echo date('Y-m-d').' at '.date("h:i:sa"); ?></div>
                        </span>
                    </td>
                    <td  style="width: 40%">
                        <div class="float-right">
                            <div style="font-size: 10px;" class="text-left">Office Number: 470-9651</div>
                            <div style="font-size: 10px;" class="text-left">Home Number: 470-9276</div>
                            <div style="font-size: 10px;" class="text-left">SEC Registration Number:CS201633364/ CEO 38935</div>
                            <div style="font-size: 10px;" class="text-left">TIN: 483-109-532-000</div>
                        </div>
                    </td>
                </tr>
            </table>
        </div>
    </div>
    <hr>
    <div>
        <div class="row">
            <div class="col-12">
                <h4 class="text-center">Mini Vault Report for the date of <?php echo date("F d Y",strtotime($_GET['date'])) ; ?> </h4>
            </div>
        </div>
        <table class="table text-center table-sm table-bordered" id="td" style="font-size:10px;">
            <thead class="bg-info text-white">
                <tr>
                    <th>Date</th>
                    <th>In</th>
                    <th>Out</th>
                    <th>Transact By</th>
                    <th>Description</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($data['data'] as $key => $value) {?>
                <tr>
                    <td><?php echo $value['date_added'] ?></td>
                    <?php 
                        if($value['trasaction_type_id'] == 1){
                            $total_in    += $value['amount'];
                            $return_cash += $value['amount'];
                            echo "<td class=".$value['color'].">".number_format($value['amount'],2,".",",")."</td>";
                            echo "<td>-</td>";
                        }else{
                            $total_out   += $value['amount'];
                            $return_cash -= $value['amount'];
                            echo "<td>-</td>";
                            echo "<td class=".$value['color'].">".number_format($value['amount'],2,".",",")."</td>";
                        }
                    ?>
                    <td><?php echo $value['trasnsactby'] ?></td>
                    <td><?php echo $value['description'] ?></td>
                </tr>
            <?php } ?>
            </tbody>
            <tfoot class="text-right">
                <tr class="bg-success text-white">
                    <th colspan="5">Total Cash In <?php echo "Php ".number_format($total_in,2,".",","); ?></th>
                </tr>
                <tr class="bg-danger text-white">
                    <th colspan="5"> ( - ) Total Cash Out <?php echo "Php ".number_format($total_out,2,".",","); ?></th>
                </tr>
                <tr>
                    <th colspan="5">Total Cash Return <?php echo "Php ".number_format($return_cash,2,".",","); ?></th>
                </tr>
            </tfoot>
        </table>
        <br>
        <br>
        <br>
        <br>
        <div class="row">
            <div class="col-12">
                <table style="width:100%;font-size:15px;padding:10px;">
                    <tr>
                        <td>
                            <table class="text-center">
                                <tr>
                                    <td style="border-bottom:1px solid #ccc;"></td>
                                </tr>
                                <tr>
                                    <td>Cashier</td>
                                </tr>
                            </table>
                        </td>
                        <td>
                            <table class="text-center" style="position:absolute;right:250px">
                                <tr>
                                    <td>______________________</td>
                                </tr>
                                <tr>
                                    <td>Verified By</td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
</div> 