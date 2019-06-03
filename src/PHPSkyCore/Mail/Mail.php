<?php

namespace PHPSkyCore\Mail;

use PHPMailer;

class Mail extends PHPMailer
{
    public function __construct($exceptions = false)
    {
        parent::__construct($exceptions);

        $this->isSMTP();
        $this->Host = MAIL['Host'];
        $this->SMTPAuth = MAIL['SMTPAuth'];
        $this->Username = MAIL['Username'];
        $this->Password = MAIL['Password'];
        $this->SMTPSecure = MAIL['SMTPSecure'];
        $this->Port = MAIL['Port'];

        $this->SMTPOptions = array(
            'ssl' => array(
                'verify_peer' => false,
                'verify_peer_name' => false,
                'allow_self_signed' => true
            )
        );
    }
}
