<?php

namespace AppBundle\Service;

use AppBundle\Entity\User;

class Mailer
{
    protected $mailer;
    protected $templating;
    private $from = "thomas.essig711@gmail.com";
    private $reply = "thomas.essig711@gmail.com";
    private $name = "ManaPresta";

    public function __construct(\Swift_Mailer $mailer, \Twig_Environment $templating)
    {
        $this->mailer = $mailer;
        $this->templating = $templating;
    }

    protected function sendMail($to, $subject, $body)
    {
        $mail = \Swift_Message::newInstance();

        $mail
            ->setFrom($this->from)
            ->setTo($to)
            ->setSubject($subject)
            ->setBody($body)
            ->setReplyTo($this->reply)
            ->setContentType('text/html');

        $this->mailer->send($mail);
    }

    public function accountActivationEmail(User $user)
    {
        $subject = "$this->name - Activation du compte";
        $to = $user->getEmail();
        $body = $this->templating->render('mail/accountActivation.html.twig', ['user' => $user]);
        $this->sendMail($to, $subject, $body);
    }

    public function forgotPasswordEmail(User $user)
    {
        $subject = "$this->name - Réinitialisation de votre mot de passe";
        $to = $user->getEmail();
        $body = $this->templating->render('mail/forgotPasswordEmail.html.twig', ['user' => $user]);
        $this->sendMail($to, $subject, $body);
    }
}