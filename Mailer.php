<?php

namespace WestFrome;

use Swift_Message;
use Swift_SmtpTransport;
use Swift_Mailer;

require_once 'vendor/autoload.php';

class Mailer {
    /** @var Swift_Mailer */
    private $mailer;

    /** @var string */
    private $to;

    /** @var string */
    private $fromEmail;

    /** @var string */
    private $fromName;

    /** @var string */
    private $subject;

    public function __construct($config)
    {
        $transport = (new Swift_SmtpTransport($config['host'], $config['port'], $config['security']))
            ->setUsername($config['user'])
            ->setPassword($config['pass']);

        $this->to = $config['to'];
        $this->subject = $config['subject'];
        $this->fromEmail = $config['from'][0];
        $this->fromName = $config['from'][1];

        $this->mailer = new Swift_Mailer($transport);
    }

    public function send($name, $email, $text)
    {
        $cleanedMessage = filter_var($text, FILTER_SANITIZE_EMAIL);

        $body = $cleanedMessage . "\n\n" . "From $name <$email>";

        $message = (new Swift_Message($this->subject))
            ->setFrom([$this->fromEmail => $this->fromName])
            ->setTo($this->to)
            ->setBody($body);

        return $this->mailer->send($message);
    }
}