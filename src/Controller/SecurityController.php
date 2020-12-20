<?php

namespace App\Controller;

use App\Service\MailService;
use App\Form\EmailFormType;
use App\Repository\UserRepository;
use App\Form\ResetPasswordType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Csrf\TokenGenerator\TokenGeneratorInterface;

/**
 * @Route("/auth")
 */
class SecurityController extends AbstractController
{
    /**
     * @Route("/login", name="app_login")
     */
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // if ($this->getUser()) {
        //     return $this->redirectToRoute('target_path');
        // }

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }

    /**
     * @Route("/logout", name="app_logout")
     */
    public function logout()
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }

    /**
     * Sign in with google Button
     * redirect to the google user account to accept to give us (scopes) profile, email and other
     * information.
     *
     * @Route("/google", name="google")
     */
    public function google(UrlGeneratorInterface $generator): Response
    {
        $url = $generator->generate('home', [], UrlGeneratorInterface::ABSOLUTE_URL);

        return new RedirectResponse('https://accounts.google.com/o/oauth2/v2/auth?client_id=' . $this->getParameter('google_id') . '&redirect_uri=' . $url . '&response_type=code&scope=openid email profile&state=google&access_type=offline');
    }


    /**
     * send an email to the user in order to reset his password or confirm his email.
     *
     * @param string $tomail take "send" if this func send email to confirm email else take null and this functiion send email to reset password
     *
     * @Route("/send-email/{tomail}", name="send_mail", methods={"GET", "POST"})
     */
    public function sendEmail(
        string $tomail = null,
        Request $request,
        UserRepository $userRepo,
        MailService $mailer,
        TokenGeneratorInterface $tokenGenerator,
        EntityManagerInterface $em
    ): Response {
        $form = $this->createForm(EmailFormType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $email = $form->get('email')->getData();
            /** @var User $user */
            $user = $userRepo->findOneByEmail($email);
            if (!empty($user)) {
                //generate a token
                $token = $tokenGenerator->generateToken();
                $user->setConfirmationToken($token);
                $em->persist($user);
                $em->flush();
                // to confirm email
                if ($tomail) {
                    $subject = 'Email Confirmation.';
                    // forget password
                } else {
                    $subject = 'Forgot Password Request';
                }
                // to, subject, template, context
                $mailer->sendEmail(
                    $email,
                    $subject,
                    'security/emails/send_email.html.twig',
                    [
                        'username' => $user->getName(),
                        'token' => $token,
                        'tomail' => $tomail,
                    ]
                );
                $this->addFlash('success', 'Email sended successfuly to you.');

                return $this->redirectToRoute('app_login');
            }
            $this->addFlash('danger', 'Email Not Found');
        }

        return $this->render('security/user_email.html.twig', [
            'form' => $form->createView(),
            'tomail' => $tomail,
        ]);
    }

    /**
     * @Route("/reset-password/{token}", name="reset_password", methods={"GET", "POST"})
     */
    public function resetPassword(
        Request $request,
        string $token,
        UserRepository $userRepo,
        EntityManagerInterface $em,
        UserPasswordEncoderInterface $encoder
    ): Response {
        $user = $userRepo->findOneBy(['confirmationToken' => $token]);
        if (!$user) {
            $this->addFlash('danger', 'User Not Found');

            return $this->redirectToRoute('app_login');
        }
        $form = $this->createForm(ResetPasswordType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user->setPassword($encoder->encodePassword($user, $form->get('password')->getData()))
                ->setConfirmationToken(null);
            $em->persist($user);
            $em->flush();
            $this->addFlash('success', 'Password updated Successfly');

            return $this->redirectToRoute('app_login');
        }

        return $this->render('security/reset_password.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * confirm user email.
     *
     * @Route("/confirm-email/{token}", name="email_confirmation", methods={"GET"})
     */
    public function confirmEmail(
        string $token,
        EntityManagerInterface $em,
        UserRepository $userRepo
    ): Response {
        $user = $userRepo->findOneBy(['confirmationToken' => $token]);
        if (!$user) {
            $this->addFlash('danger', 'User Not Found');

            return $this->redirectToRoute('app_register');
        }
        $user->setConfirmationToken(null)
            ->setEnabled(true);
        $em->persist($user);
        $em->flush();
        $this->addFlash('success', 'Email Confirmed Successfly.');

        return $this->redirectToRoute('app_login');
    }
}
