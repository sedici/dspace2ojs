<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\User;
use App\Form\RegisterType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserController extends AbstractController
{
    /**
     * @Route("/user", name="user")
     */
    public function index()
    {
        return $this->render('user/index.html.twig', [
            'controller_name' => 'UserController',
        ]);
    }
     /**
     * @Route("/user/register", name="user_register")
     */
    public function register(Request $request, UserPasswordEncoderInterface $encoder)
    {
        $user= new User();
        $form= $this->createForm( RegisterType::class , $user);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $encode = $encoder->encodePassword($user,$user->getPassword());
            $user->setPassword($encode);
            $em= $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();
        }
        return $this->render('user/register.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
