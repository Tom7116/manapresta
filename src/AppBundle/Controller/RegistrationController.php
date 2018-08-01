<?php

namespace AppBundle\Controller;

use AppBundle\Entity\User;
use AppBundle\Form\UserType;
use AppBundle\Service\Mailer;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class RegistrationController extends Controller
{
    /**
     * @Route("/register", name="register")
     */
    public function register(Request $request, UserPasswordEncoderInterface $passwordEncoder, Mailer $mailer)
    {
        if (!$this->getUser()) {
            $user = new User();
            $form = $this->createForm(UserType::class, $user);
            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
                $entityManager = $this->getDoctrine()->getManager();

                $password = $passwordEncoder->encodePassword($user, $user->getPassword());
                $user->setPassword($password);

                // r = register (route name)
                $user->setToken(uniqid('r'));
                $entityManager->persist($user);
                $entityManager->flush();

                $mailer->accountActivationEmail($user);

                $userEmail = $user->getEmail();
                $this->addFlash('success', 'Vous êtes enregistré ! Afin de finaliser votre inscription, un email de confirmation vous a été transmis à l\'adresse suivante : ' . $userEmail);

                return $this->redirectToRoute('homepage');
            }

            return $this->render(
                'security/register.html.twig',
                array('form' => $form->createView())
            );
        }

        return $this->redirectToRoute('member');
    }

    /**
     * @Route("/registration_confirmation/{slug}", name="registrationConfirmation")
     */
    public function registrationConfirmation(Request $request)
    {
        $tokenUrl = $request->get('slug');

        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository(User::class)->findOneBy([
            'token' => $tokenUrl,
        ]);

        if($user) {
            $user->setToken(null);

            $user->setIsActive(true);

            $em->persist($user);
            $em->flush();

            $this->addFlash('success', 'Votre compte est activé !');
        } else {
            $this->addFlash('danger', 'Impossible d\'activer le compte.');
        }

        return ($this->redirectToRoute('homepage'));
    }
}
