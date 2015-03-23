<?php
require_once 'class.phpmailer.php';
class Mailer{
    function __construct(){
        $ci = get_instance();
        $ci->load->config('phpmailer');
        $mail = new PHPMailer(); // create a new object
        $mail->IsSMTP(); // enable SMTP
        $mail->SMTPDebug = 1; // debugging: 1 = errors and messages, 2 = messages only
        $mail->SMTPAuth = true; // authentication enabled
        $mail->SMTPSecure = $ci->config->item("ssl"); // secure transfer enabled REQUIRED for GMail
        $mail->Host = $ci->config->item("email_host");
        $mail->Port = $ci->config->item("email_port"); // or 587
        $mail->IsHTML(true);
        $mail->Username = $ci->config->item("email_username");
        $mail->Password = $ci->config->item("email_password");
        $mail->SetFrom($ci->config->item("email_remitente"));
        $this->mail = $mail;
    }
    
    function mail($to,$subject,$msj)
    {
        $this->mail->Subject = $subject;
        $this->mail->Body = utf8_decode($msj);
        $this->mail->AddAddress($to);
         if(!$this->mail->Send())
            {
            return "Mailer Error: " . $this->mail->ErrorInfo;
            }
            else
            {
            return "Message has been sent";
            }
    }
}
?>