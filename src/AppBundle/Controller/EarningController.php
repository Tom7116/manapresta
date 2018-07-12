<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Prestation;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * Earning controller.
 *
 * @Route("earning")
 */
class EarningController extends Controller
{
    /**
     * @Route("/", name="earning_index")
     * @Method("GET")
     */
    public function index()
    {
        return $this->redirectToRoute('earning_per_month');
    }

    /**
     * @Route("/month", name="earning_per_month")
     * @Method("GET")
     * @throws \Exception
     */
    public function perMonth()
    {
        $user = $this->getUser();
        $em = $this->getDoctrine()->getManager();

        $currentYear = date('Y');

        for ($i=1; $i<13; $i++) {
            if ($i<10) {
                $monthlyPrestas[] = $em->getRepository(Prestation::class)->findAllPerMonth("$currentYear-0$i-01", "$currentYear-0$i-31", $user);
            } else {
                $monthlyPrestas[] = $em->getRepository(Prestation::class)->findAllPerMonth("$currentYear-$i-01", "$currentYear-$i-31", $user);
            }

        }

        $months = ['Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin', 'Juillet', 'Août', 'Septembre', 'Octobre', 'Novembre', 'Décembre'];

        foreach ($monthlyPrestas as $key => $prestas) {
            $monthGains = 0;
            $hours = 0;
            $minutes = 0;
            $secondes = 0;
            foreach ($prestas as $presta) {
                $monthGains += $presta->getTotalNetGains();

                list($h, $m, $s) = explode(':', $presta->getHoursWorked());
                $hours += $h;
                $minutes += $m;
                $secondes += $s;
            }
            $totalSecondes = ($hours*3600) + ($minutes*60) + $secondes;
            $monthsHours[$key] = round($totalSecondes/3600);
            $monthsGains[$key] = $monthGains;
        }

        return $this->render('earning/perMonth.html.twig', [
            'monthlyPrestas' => $monthlyPrestas,
            'currentYear' => $currentYear,
            'months' => $months,
            'monthsGains' => $monthsGains,
            'monthsHours' => $monthsHours,
        ]);
    }

    /**
     * @Route("/employer", name="earning_per_employer")
     * @Method("GET")
     */
    public function perEmployer()
    {
        return $this->render('earning/perEmployer.html.twig');
    }

    /**
     * @Route("/month_employer", name="earning_per_month_employer")
     * @Method("GET")
     */
    public function perMonthEmployer()
    {
        return $this->render('earning/perMonthEmployer.html.twig');
    }
}
