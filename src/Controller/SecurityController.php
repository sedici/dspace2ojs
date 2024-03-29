<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;




class SecurityController extends AbstractController
{
    /**
     * @Route("/", name="app_login")
     */
    public function login(AuthenticationUtils $authenticationUtils, AuthorizationCheckerInterface $authorizationChecker): Response
    {
        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();

        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();
        if ( $authorizationChecker->isGranted('ROLE_USER'))
            return $this->redirectToRoute('my_files');
        else
            return $this->render('security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error ,]);
    }
    /**
     * @Route("/logout", name="app_logout")
     */
    public function logout()
    {
        // controller can be blank: it will never be executed!
        //   throw new \Exception('Don\'t forget to activate logout in security.yaml');
        return $this->redirectToRoute('app_login');
    }
}
