<?php

namespace AppBundle\Controller;

use AppBundle\Entity\User;
use AppBundle\Service\Mailer;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="homepage")
     */
    public function index(Request $request, AuthenticationUtils $authenticationUtils)
    {
        if (!$this->getUser()) {
            $error = $authenticationUtils->getLastAuthenticationError();

            $lastUsername = $authenticationUtils->getLastUsername();

            return $this->render('security/login.html.twig', array(
                'last_username' => $lastUsername,
                'error'         => $error,
            ));
        }

        return $this->redirectToRoute('member');
    }

    /**
     * @Route("/password/new", name="forgotPassword")
     * @Method({"GET", "POST"})
     */
    public function forgotPassword(Request $request, Mailer $mailer)
    {
        $user = new User();
        $form = $this->createForm('AppBundle\Form\ForgotPasswordType', $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $userForm = $form->getData();
            $emailForm = $userForm->getEmail();

            $em = $this->getDoctrine()->getManager();
            $user = $em->getRepository(User::class)->findOneBy([
                'email' => $emailForm,
            ]);

            if ($user) {
                /** @var User $user */
                // fp = forgotPassword (route name)
                $user->setToken(uniqid('fp'));

                $em->persist($user);
                $em->flush();

                $mailer->forgotPasswordEmail($user);

                $this->addFlash('success', 'Un email de réinitialisation de mot de passe vous a été transmis à l\'adresse suivante : ' . $emailForm);
            } else {
                $this->addFlash('danger', 'Cet email ne correspond à aucun utilisateur !');
            }

            return ($this->redirectToRoute('forgotPassword'));
        }

        return $this->render('security/forgotPassword.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/password/update/{slug}", name="updatePassword", defaults={"slug" = null})
     * @Method({"GET", "POST"})
     */
    public function updatePassword(Request $request, UserPasswordEncoderInterface $encoder)
    {
        $user = new User();
        $form = $this->createForm('AppBundle\Form\UpdatePasswordType', $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $userForm = $form->getData();
            $plainPassword = $userForm->getPassword();

            $tokenUrl = $request->get('slug');

            $em = $this->getDoctrine()->getManager();
            $user = $em->getRepository(User::class)->findOneBy(
                ['token' => $tokenUrl]
            );

            if($user) {
                /** @var User $user */
                $encoded = $encoder->encodePassword($user, $plainPassword);
                $user->setPassword($encoded);

                $user->setToken(null);

                $em->persist($user);
                $em->flush();

                $this->addFlash('success', 'Votre mot de passe est modifié !');
            } else {
                $this->addFlash('danger', 'Le mot de passe ne peut être modifié !');
            }

            return ($this->redirectToRoute('homepage'));
        }

        return $this->render('security/updatePassword.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }
}
