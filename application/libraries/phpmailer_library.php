<?php
class Phpmailer_library 
{
    public function __construct(){
        log_message('Debug', 'PHPMailer class is loaded.');
    }
	public function load(){
        require_once(APPPATH."third_party/Phpmailer/src/Exception.php");
        require_once(APPPATH."third_party/Phpmailer/src/OAuth.php");
        require_once(APPPATH."third_party/Phpmailer/src/PHPMailer.php");
        require_once(APPPATH."third_party/Phpmailer/src/POP3.php");
        require_once(APPPATH."third_party/Phpmailer/src/SMTP.php");
        $objMail = new \PHPMailer\PHPMailer\PHPMailer;
        return $objMail;
    }
}
?>