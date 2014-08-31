<?php

require_once APPPATH . 'libraries/phpmailer/class.phpmailer.php';

/**
 * @property PHPMailer $mail
 */
class Mailer {

    private $mail;
    
    protected $senderEmail = 'olek17@gmail.com';
    protected $senderName = 'OffCalendar';

    function __construct() {

        $this->mail = new PHPMailer();
        $this->mail->IsSMTP();
        $this->mail->SMTPDebug = 1;
        $this->mail->SMTPAuth = true;
        $this->mail->SMTPSecure = "tls";
        $this->mail->CharSet = "UTF-8";

        $this->mail->Host = 'smtp.gmail.com';
        $this->mail->Port = 587;
        $this->mail->Username = 'olek17';
        $this->mail->Password = 'aN17blek21';
    }

    function sendMessage($to, $subject, $message, $senderName = false) {

        $this->mail->ClearAllRecipients();
        $this->mail->ClearAttachments();
        $this->mail->ClearCustomHeaders();
        $this->mail->ClearReplyTos();
        $this->mail->AltBody = '';

        $this->mail->AddAddress($to);

        if ($senderName !== false) {
            $this->mail->SetFrom($this->senderEmail, $senderName);
        } else {
            $this->mail->SetFrom($this->senderEmail, $this->senderName);
        }

        $this->mail->Subject = $subject;
        $this->mail->MsgHTML($message);
        $this->mail->Send();

        if ($this->mail->IsError()) {
            throw new Exception($this->mail->ErrorInfo);
        }
    }

}
