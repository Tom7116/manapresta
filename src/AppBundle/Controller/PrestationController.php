<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Prestation;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;

/**
 * Prestation controller.
 *
 * @Route("prestation")
 */
class PrestationController extends Controller
{
    /**
     * Lists all prestation entities.
     *
     * @Route("/", name="prestation_index")
     * @Method("GET")
     */
    public function index()
    {
        $em = $this->getDoctrine()->getManager();

        $prestations = $em->getRepository('AppBundle:Prestation')->findAllOrderByDate($this->getUser());

        return $this->render('prestation/index.html.twig', array(
            'prestations' => $prestations,
        ));
    }

    /**
     * Creates a new prestation entity.
     *
     * @Route("/new", name="prestation_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        $prestation = new Prestation();
        $form = $this->createForm(
            'AppBundle\Form\PrestationType',
            $prestation,
            ['user' => $this->getUser()]
        );
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $prestation->setUser($this->getUser());

            $endTime = $form->getData()->getEndTime();
            $startTime = $form->getData()->getStartTime();
            $grossHonorary = $form->getData()->getGrossHonorary();
            $netHonorary = $form->getData()->getNetHonorary();

            $timeDifference = $startTime->diff($endTime);
            $hoursWorked = $timeDifference->h . ':' . $timeDifference->i . ':' . $timeDifference->s;
            list($h, $i, $s) = explode(":", $hoursWorked);
            $minutesWorked = ($h * 60) + $i;
            $secondesWorked = ($h * 3600) + ($i * 60) + $s;
            $grossGains = $minutesWorked * ($grossHonorary / 60);
            $grossToNet = $grossGains - (23 / 100 * $grossGains);
            $netGains = $minutesWorked * ($netHonorary / 60);
            $totalNetGains = $grossToNet + $netGains;

            $hours = gmdate('H:i:s', $secondesWorked);

            $prestation
                ->setPrestationDate($form->getData()->getStartTime())
                ->setHoursWorked($hours)
                ->setMinutesWorked($minutesWorked)
                ->setGrossGains($grossGains)
                ->setGrossToNet($grossToNet)
                ->setNetGains($netGains)
                ->setTotalNetGains($totalNetGains);


            $em = $this->getDoctrine()->getManager();
            $em->persist($prestation);
            $em->flush();

            return $this->redirectToRoute('prestation_index');
        }

        return $this->render('prestation/new.html.twig', array(
            'prestation' => $prestation,
            'form' => $form->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing prestation entity.
     *
     * @Route("/{id}/edit", name="prestation_edit")
     * @Method({"GET", "POST"})
     */
    public function edit(Request $request, Prestation $prestation)
    {
        $deleteForm = $this->createDeleteForm($prestation);
        $editForm = $this->createForm('AppBundle\Form\PrestationType', $prestation, ['user' => $this->getUser()]);
        $editForm->handleRequest($request);

        $previousPath = $request->server->get('HTTP_REFERER');

        if ($editForm->isSubmitted() && $editForm->isValid()) {

            $endTime = $editForm->getData()->getEndTime();
            $startTime = $editForm->getData()->getStartTime();
            $grossHonorary = $editForm->getData()->getGrossHonorary();
            $netHonorary = $editForm->getData()->getNetHonorary();

            $timeDifference = $startTime->diff($endTime);
            $hoursWorked = $timeDifference->h . ':' . $timeDifference->i . ':' . $timeDifference->s;
            list($h, $i, $s) = explode(":", $hoursWorked);
            $minutesWorked = ($h * 60) + $i;
            $secondesWorked = ($h * 3600) + ($i * 60) + $s;
            $grossGains = $minutesWorked * ($grossHonorary / 60);
            $grossToNet = $grossGains - (23 / 100 * $grossGains);
            $netGains = $minutesWorked * ($netHonorary / 60);
            $totalNetGains = $grossToNet + $netGains;

            $hours = gmdate('H:i:s', $secondesWorked);


            $prestation
                ->setPrestationDate($editForm->getData()->getStartTime())
                ->setHoursWorked($hours)
                ->setMinutesWorked($minutesWorked)
                ->setGrossGains($grossGains)
                ->setGrossToNet($grossToNet)
                ->setNetGains($netGains)
                ->setTotalNetGains($totalNetGains);

            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('prestation_index');
        }

        return $this->render('prestation/edit.html.twig', array(
            'prestation' => $prestation,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
            'previousPath' => $previousPath,
        ));
    }

    /**
     * Deletes a prestation entity.
     *
     * @Route("/{id}", name="prestation_delete")
     * @Method({"GET", "POST"})
     */
    public function delete(Request $request, Prestation $prestation)
    {
        $em = $this->getDoctrine()->getManager();
        $em->remove($prestation);
        $em->flush();

        return $this->redirect($request->server->get('HTTP_REFERER'));
    }

    /**
     * Creates a form to delete a prestation entity.
     *
     * @param Prestation $prestation The prestation entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Prestation $prestation)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('prestation_delete', array('id' => $prestation->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }

    /**
     * Change isPaid attribut.
     *
     * @Route("/{id}/is_paid", name="is_paid")
     * @Method("GET")
     */
    public function isPaid(Request $request, Prestation $prestation)
    {
        if ($prestation->isPaid()) {
            $prestation->setIsPaid(false);
        } else {
            $prestation->setIsPaid(true);
        }

        $this->getDoctrine()->getManager()->flush();

        return $this->redirect($request->server->get('HTTP_REFERER'));
    }
}
