<?php

declare(strict_types=1);

namespace App\Security\Exception;

use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;

//
// Custom Exception will throw it when the user doesn't have verified email.
// it will be displayed on the top of the form as a flash message, because it will be stroed in the
// session like other flashes.
//
class NotVerifiedEmailException extends CustomUserMessageAuthenticationException
{
    /**
     * @param string[] $messageData
     */
    public function __construct(
        string $message = 'This account has not verified Email',
        array $messageData = [],
        int $code = 0,
        \Throwable $previous = null
    ) {
        parent::__construct($message, $messageData, $code, $previous);
    }
}
