<?php

declare(strict_types=1);

namespace App\Service;

use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;

/**
 * send mails.
 */
class MailService
{
    private $mailer;
    private $sender_email;

    public function __construct(MailerInterface $mailer, string $sender_email)
    {
        $this->mailer = $mailer;
        $this->sender_email = $sender_email;
    }

    /**
     * Send Email.
     *
     * @param string  $to       the recepiant
     * @param string  $subject  email ubject
     * @param string  $template email template
     * @param mixed[] $context  variables in the template
     * @param string  $replyTo
     */
    public function sendEmail(string $to, string $subject, string $template, array $context, string $replyTo = null): void
    {
        $from = $this->sender_email;

        $email = new TemplatedEmail();
        $email
            ->from($from)
            ->to($to)
            ->subject($subject)
            ->htmlTemplate($template)
            ->context($context)
        ;
        if ($replyTo) {
            $email->replyTo($replyTo);
        }
        $this->mailer->send($email);
    }
}
