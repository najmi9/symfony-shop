<?php

declare(strict_types=1);

namespace App\EventListener;

use App\Entity\User;
use App\Service\MailService;
use Doctrine\Persistence\Event\LifecycleEventArgs;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Csrf\TokenGenerator\TokenGeneratorInterface;

class UserListener
{
    protected TokenStorageInterface $tokenStorage;
    private MailService $mailer;
    private TokenGeneratorInterface $tokenGenerator;

    public function __construct(
        TokenStorageInterface $tokenStorage,
        MailService $mailer,
        TokenGeneratorInterface $tokenGenerator
    ) {
        $this->tokenStorage = $tokenStorage;
        $this->mailer = $mailer;
        $this->tokenGenerator = $tokenGenerator;
    }

    public function preUpdate(User $user, LifecycleEventArgs $event): void
    {
        $user->setUpdatedAt(new \DateTime());
    }

    public function prePersist(User $user, LifecycleEventArgs $event): void
    {
        $token = $this->tokenGenerator->generateToken();
        $user->setCreatedAt(new \DateTime())
            ->setUpdatedAt(new \DateTime())
            ->setConfirmationToken($token)
        ;
    }

    public function postPersist(User $user, LifecycleEventArgs $event): void
    {
        $this->mailer->sendEmail(
            $user->getEmail(),
            'Symfony App - Email Confirmation',
            'security/emails/confirm_email.html.twig',
            [
                'username' => $user->getName(),
                'token' => $user->getConfirmationToken(),
            ]
        );
    }
}
