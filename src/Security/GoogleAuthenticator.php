<?php

declare(strict_types=1);

namespace App\Security;

use App\Repository\UserRepository;
use App\Security\Exception\NotVerifiedEmailException;
use App\Service\GoogleService;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Guard\AbstractGuardAuthenticator;
use Symfony\Component\Security\Http\Util\TargetPathTrait;

/**
 * Authenticate a user from google.
 */
class GoogleAuthenticator extends AbstractGuardAuthenticator
{
    use TargetPathTrait; //to redirect the user after authentication automatically from whree it was

    private GoogleService $googleService; // get user data
    private UserRepository $userRepo; // find or create the new user
    private RouterInterface $router;

    public function __construct(GoogleService $googleService, UserRepository $userRepo, RouterInterface $router)
    {
        $this->googleService = $googleService;
        $this->userRepo = $userRepo;
        $this->router = $router;
    }

    public function supports(Request $request)
    {
        // this authenticator will be called only if there are $code and $state=google varaibles in the url.
        return $request->query->get('code') && 'google' === $request->query->get('state');
    }

    public function getCredentials(Request $request)
    {
        // get the user data
        return $this->googleService->loadData(
            [
                'code' => $request->query->get('code'),
                'state' => $request->query->get('state'),
            ]
        );
    }

    public function getUser($credentials, UserProviderInterface $userProvider)
    {
        return $this->userRepo->findOrCreateUserFromGoogle($credentials);
    }

    public function checkCredentials($credentials, UserInterface $user)
    {
        if (!$credentials['email_verified']) {
            throw new NotVerifiedEmailException();
        }

        return true;
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception)
    {
        if ($request->hasSession()) {
            $request->getSession()->set(Security::AUTHENTICATION_ERROR, $exception);
        }

        return new RedirectResponse($this->router->generate('app_login'));
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, $providerKey)
    {
        $targetPath = $this->getTargetPath($request->getSession(), $providerKey);

        return new RedirectResponse($targetPath ?? $this->router->generate('home'));
    }

    public function start(Request $request, AuthenticationException $authException = null): Response
    {
        return new RedirectResponse($this->router->generate('app_login'));
    }

    public function supportsRememberMe(): bool
    {
        return true;
    }
}
