<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Prestation;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class MemberSpaceController extends Controller
{
    /**
     * @Route("/member", name="member")
     * @Method("GET")
     */
    public function memberSpace(TokenStorageInterface $storage)
    {
        $isActive = $this->getUser()->isActive();

        if (!$isActive) {
            $storage->setToken(null);
            $flashType = 'danger';
            $flashMessage = 'Vous devez confirmer votre adresse mail pour activer votre compte.';
            $this->addFlash($flashType, $flashMessage);
            return $this->redirectToRoute('homepage');
        }

        $em = $this->getDoctrine()->getManager();

        $prestationsNotPaid =  $em ->getRepository(Prestation::class)->findNotPaidOrderByDate($this->getUser());

        return $this->render('member/index.html.twig', [
            'prestationsNotPaid' => $prestationsNotPaid,
        ]);
    }
}