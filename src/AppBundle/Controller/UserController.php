<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use AppBundle\Form\Type\UserType;
use AppBundle\Entity\User;


class UserController extends Controller
{
    /**
     * @Route("/register", name="user_register")
     * @Template
     */
    public function registerAction(Request $request)
    {
        $user = new User;
        $form = $this-> createForm(UserType::class, $user);

        $form->handleRequest($request);

        if ($form->isValid()) {
            if ($user->getName() === 'admin') {
                $user->setRoles(array_merge($user->getRoles(),['ROLE_ADMIN']));
            }
            $user->setPassword(password_hash($user->getPassword(), PASSWORD_DEFAULT));
            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();
            return $this->redirectToRoute('user_login');
        }

        return ['form' => $form->createView()];
    }

    /**
    * @Route("/login", name="user_login")
    * @Template
    */
    public function loginAction(){
        $form = $this
            ->createForm(UserType::class, new User)
            ->createView();
        return [ 'form' => $form];
    }

    /**
    * @Route("/check_login")
    */
    public function checkPathAction(){}

    /**
    * @Route("/logout", name="user_logout")
    */
    public function logoutPathAction(){}
}
