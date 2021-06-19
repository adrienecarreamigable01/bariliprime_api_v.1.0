<?php
defined('BASEPATH') OR exit('No direct script access allowed');
    class email extends CI_Controller{
        /* Global Variables */
        private $res = array();

        public function __construct() {
            ini_set('max_execution_time', 300); //300 seconds = 5 minutes
			parent::__construct();
            // $this->load->library("Phpmailerlib");
            $this->load->library('phpmailer_library',NULL,'phpmailer_library');
        }

        public function manualSend(){

            if(empty($_POST['email'])){
                $this->res = array(
                    'isError' => true,
                    'message' => "Empty email",
                    'data'    => $_POST['email'],
                    'date'    => date("Y-m-d"),
                );
            }
            else if($_POST['email'] == "N/A@YAHOO.COM"){
                $this->res = array(
                    'isError' => true,
                    'message' => "Email not allowed ".$_POST['email'],
                    'data'    => $_POST['email'],
                    'date'    => date("Y-m-d"),
                );
            }
            else if(empty($_POST['body'])){
                $this->res = array(
                    'isError' => true,
                    'message' => "Empty body",
                    'data'    => $_POST['email'],
                    'date'    => date("Y-m-d"),
                );
            }
            else{


                $email       = $_POST['email'];
                $bodyMessage = $_POST['body'];
                $subj        = !empty($_POST['subject']) ? $_POST['subject'] : "Barili Prime Notification";
                $cc        = !empty($_POST['cc']) ? $_POST['cc'] : "";
                
                $html = $this->htmlDraft($bodyMessage,$subj);
                $send = $this->send($html,$email,$subj,$cc);
                if($send){
                    $this->res = array(
                        'isError' => false,
                        'message' => "Successfuly Send",
                        'data'    => $_POST['email'],
                        'date'    => date("Y-m-d"),
                    );
                }else{
                    $this->res = array(
                        'isError' => true,
                        'message' => "Error Sending Email",
                        'data'    => $_POST['email'],
                        'date'    => date("Y-m-d"),
                    );
                }
            }

            $this->displayJSON($this->res);
        
        }
        public function htmlDraft($body,$subject){
            $html ='
                <!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
                    <html xmlns="http://www.w3.org/1999/xhtml">
                    
                    <head>
                    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
                    <title>A Simple Responsive HTML Email</title>
                    <style type="text/css">
                    body {margin: 0; padding: 0; min-width: 100%!important;}
                    img {height: auto;}
                    .content {width: 100%; max-width: 600px;}
                    .header {padding: 40px 30px 20px 30px;}
                    .innerpadding {padding: 30px 30px 30px 30px;}
                    .borderbottom {border-bottom: 1px solid #f2eeed;}
                    .subhead {font-size: 15px; color: #ffffff; font-family: sans-serif; letter-spacing: 10px;}
                    .h1, .h2, .bodycopy {color: #153643; font-family: sans-serif;}
                    .h1 {font-size: 33px; line-height: 38px; font-weight: bold;}
                    .h2 {padding: 0 0 15px 0; font-size: 24px; line-height: 28px; font-weight: bold;}
                    .bodycopy {font-size: 16px; line-height: 22px;}
                    .button {text-align: center; font-size: 18px; font-family: sans-serif; font-weight: bold; padding: 0 30px 0 30px;}
                    .button a {color: #ffffff; text-decoration: none;}
                    .footer {padding: 20px 30px 15px 30px;}
                    .footercopy {font-family: sans-serif; font-size: 14px; color: #ffffff;}
                    .footercopy a {color: #ffffff; text-decoration: underline;}
                    
                    @media only screen and (max-width: 550px), screen and (max-device-width: 550px) {
                    body[yahoo] .hide {display: none!important;}
                    body[yahoo] .buttonwrapper {background-color: transparent!important;}
                    body[yahoo] .button {padding: 0px!important;}
                    body[yahoo] .button a {background-color: #e05443; padding: 15px 15px 13px!important;}
                    body[yahoo] .unsubscribe {display: block; margin-top: 20px; padding: 10px 50px; background: #2f3942; border-radius: 5px; text-decoration: none!important; font-weight: bold;}
                    }
                    
                    /*@media only screen and (min-device-width: 601px) {
                        .content {width: 600px !important;}
                        .col425 {width: 425px!important;}
                        .col380 {width: 380px!important;}
                        }*/
                    
                    </style>
                    </head>
                    
                    <body yahoo bgcolor="#f6f8f1">
                    <table width="100%" bgcolor="#f6f8f1" border="0" cellpadding="0" cellspacing="0">
                    <tr>
                    <td>
                        <!--[if (gte mso 9)|(IE)]>
                        <table width="600" align="center" cellpadding="0" cellspacing="0" border="0">
                            <tr>
                            <td>
                        <![endif]-->     
                        <table bgcolor="#ffffff" class="content" align="center" cellpadding="0" cellspacing="0" border="0">
                        <tr>
                            <td bgcolor="#c7d8a7" class="header">
                            <!--[if (gte mso 9)|(IE)]>
                                <table width="425" align="left" cellpadding="0" cellspacing="0" border="0">
                                <tr>
                                    <td>
                            <![endif]-->
                            <table class="col425" align="left" border="0" cellpadding="0" cellspacing="0" style="width: 100%; max-width: 425px;">  
                                <tr>
                                <td height="70">
                                    <table width="100%" border="0" cellspacing="0" cellpadding="0">
                                    <tr>
                                        <td class="subhead" style="padding: 0 0 0 3px;">
                                        '.$subject.'
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="h1" style="padding: 5px 0 0 0;">
                                            Barili Prime Lending Corp.
                                        </td>
                                    </tr>
                                    </table>
                                </td>
                                </tr>
                            </table>
                            <!--[if (gte mso 9)|(IE)]>
                                    </td>
                                </tr>
                            </table>
                            <![endif]-->
                            </td>
                        </tr>
                        <tr style="background: url("https://png.pngtree.com/thumb_back/fw800/background/20201012/pngtree-snowy-mountain-christmas-party-image_412987.jpg"); background-size:cover;background-repeat:no-repeat;background-position: center center;  ">
                            <td class="innerpadding borderbottom">
                            <table width="100%" border="0" cellspacing="0" cellpadding="0" >
                                <tr>
                                </tr>
                                <tr>
                                <td class="bodycopy">
                                    '.$body.'
                                </td>
                                </tr>
                            </table>
                            </td>
                        </tr>
                       
                        <tr>
                            <td class="footer" bgcolor="#44525f">
                            <table width="100%" border="0" cellspacing="0" cellpadding="0">
                                <tr>
                                <td align="center" class="footercopy">
                                    &copy; <a href="https://meeow.doitcebu.com">meeow.doitcebu.com</a> 2020 <br/>
                                    <a href="m.me/116367786862437" class="unsubscribe"><font color="#ffffff">Message us at</font></a> 
                                    <span class="hide">Meeow</span>
                                </td>
                                </tr>
                                <tr>
                                <td align="center" style="padding: 20px 0 0 0;">
                                    <table border="0" cellspacing="0" cellpadding="0">
                                    <tr>
                                        <td width="37" style="text-align: center; padding: 0 10px 0 10px;">
                                        <a href="m.me/116367786862437">
                                            MEEOW
                                        </a>
                                    </tr>
                                    </table>
                                </td>
                                </tr>
                            </table>
                            </td>
                        </tr>
                        </table>
                        <!--[if (gte mso 9)|(IE)]>
                            </td>
                            </tr>
                        </table>
                        <![endif]-->
                        </td>
                    </tr>
                    </table>
                    </body>
                    </html>
                ';
            return $html;
        }
        public function emailConfirm(){
            $config = [
                'protocol'      => 'smtp',
                'smtp_host'     =>'smtp.gmail.com',
                'smtp_user'     =>'bariliprime.lending2010@gmail.com',
                'smtp_pass'     =>'Giovergara2010!',
                'smtp_port'     =>'465',
                'validate'      =>'true',
                'encrypt'       =>'ssl',
                'from_name'     => 'Barili Prime Lending Corp.',
                'from_email'    =>'bariliprime.lending2010@gmail.com',
                'reply'         =>'bariliprime.lending2010@gmail.com',
            ];
            return $config;
        }
        public function send($body,$recipient = 'bariliprime.lending2010@gmail.com',$subject = "Barili Prime Notification",$cc){
            $default = $this->emailConfirm();
            // print_r($default);exit;

            $send = $this->phpmailer_library->load();

            // $send->SMTPDebug = 1; // Enable verbose debug output
            $send->SMTPDebug = 0; 
            $send->isSMTP(); // Set mailer to use SMTP
            $send->Host = $default['smtp_host'];
            $send->SMTPAuth = true; // Enable SMTP authentication
            $send->Username = $default['smtp_user']; // SMTP username
            $send->Password = $default['smtp_pass']; // SMTP password
            $send->SMTPSecure = $default['encrypt']; // Enable TLS encryption, `ssl` also accepted
            $send->Port = $default['smtp_port'];
            $send->SMTPOptions = array(
                'ssl' => array(
                    'verify_peer' => false,
                    'verify_peer_name' => false,
                    'allow_self_signed' => true
                )
            );
            $send->isHTML(true);
            $send->AddReplyTo($default['reply']);
            $send->setFrom($default['from_email'],$default['from_name']);
            $send->addAddress($recipient);
            $send->Subject = $subject.' '.date("F d, Y H:i:s");
            $send->Body = $body;

            if(!empty($cc)){
                $cc = explode(",", $cc);
                for ($i=0; $i < sizeof($cc); $i++) { 
                    $send->AddCC($cc[$i]);
                }
            }
           
            
            if($send->send()){
               return true;
            }else{
                return false;
            }
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
    }
?>
