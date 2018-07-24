<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Employer;
use AppBundle\Entity\Prestation;
use AppBundle\Service\Calculator;
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
     */
    public function perMonth(Calculator $calculator)
    {
        $currentYear = date('Y');
        $previousYear = date('Y') - 1;

        $em = $this->getDoctrine()->getManager();

        $monthlyPrestas = [];
        $previousMonthlyPrestas = [];

        for ($i=1; $i<13; $i++) {
            if ($i<10) {
                $monthlyPrestas[] = $em->getRepository(Prestation::class)->findAllByMonth("$currentYear-0$i-01", "$currentYear-0$i-31", $this->getUser());
                $previousMonthlyPrestas[] = $em->getRepository(Prestation::class)->findAllByMonth("$previousYear-0$i-01", "$previousYear-0$i-31", $this->getUser());
            } else {
                $monthlyPrestas[] = $em->getRepository(Prestation::class)->findAllByMonth("$currentYear-$i-01", "$currentYear-$i-31", $this->getUser());
                $previousMonthlyPrestas[] = $em->getRepository(Prestation::class)->findAllByMonth("$previousYear-$i-01", "$previousYear-$i-31", $this->getUser());
            }
        }

        $months = ['Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin', 'Juillet', 'Août', 'Septembre', 'Octobre', 'Novembre', 'Décembre'];

        // CURRENT YEAR GAINS-HOURS
        $monthsHours = [];
        $monthsGains = [];

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

            $decimalTimeWorked = (($hours*3600) + ($minutes*60) + $secondes) / 3600;
            $monthsHours[$key] = $calculator->convertTime($decimalTimeWorked);
            $monthsGains[$key] = $monthGains;
        }

        // PREVIOUS YEAR GAINS-HOURS
        $previousMonthsHours = [];
        $previousMonthsGains = [];

        foreach ($monthlyPrestas as $key => $prestas) {
            $previousMonthGains = 0;
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

            $decimalTimeWorked = (($hours*3600) + ($minutes*60) + $secondes) / 3600;
            $previousMonthsHours[$key] = $calculator->convertTime($decimalTimeWorked);
            $previousMonthsGains[$key] = $previousMonthGains;
        }

        return $this->render('earning/perMonth.html.twig', [
            'currentYear' => $currentYear,
            'previousYear' => $previousYear,
            'monthlyPrestas' => $monthlyPrestas,
            'previousMonthlyPrestas' =>$previousMonthlyPrestas,
            'months' => $months,
            'monthsGains' => $monthsGains,
            'monthsHours' => $monthsHours,
            'previousMonthsGains' => $previousMonthsGains,
            'previousMonthsHours' => $previousMonthsHours,
        ]);
    }

    /**
     * @Route("/employer", name="earning_per_employer")
     * @Method("GET")
     */
    public function perEmployer(Calculator $calculator)
    {
        $currentYear = date('Y');
        $previousYear = date('Y') - 1;

        $em = $this->getDoctrine()->getManager();

        $employers = $em->getRepository(Employer::class)->findBy([
            'user' => $this->getUser(),
        ]);

        $prestasPerEmployer = [];
        $previousPrestasPerEmployer = [];

        foreach ($employers as $employer) {
            $prestasPerEmployer[] = $em->getRepository(Prestation::class)->findAllByEmployer("$currentYear-01-01", "$currentYear-12-31", $employer, $this->getUser());
            $previousPrestasPerEmployer[] = $em->getRepository(Prestation::class)->findAllByEmployer("$previousYear-01-01", "$previousYear-12-31", $employer, $this->getUser());
        }

        $monthsHours = [];
        $monthsGains = [];

        foreach ($prestasPerEmployer as $key => $prestas) {
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

            $decimalTimeWorked = (($hours*3600) + ($minutes*60) + $secondes) / 3600;
            $monthsHours[$key] = $calculator->convertTime($decimalTimeWorked);
            $monthsGains[$key] = $monthGains;
        }

        return $this->render('earning/perEmployer.html.twig', [
            'currentYear' => $currentYear,
            'previousYear' => $previousYear,
            'previousPrestasPerEmployer' => $previousPrestasPerEmployer,
            'prestasPerEmployer' => $prestasPerEmployer,
            'employers' => $employers,
            'monthsGains' => $monthsGains,
            'monthsHours' => $monthsHours,
        ]);
    }

    /**
     * @Route("/month_employer", name="earning_per_month_employer")
     * @Method("GET")
     */
    public function perMonthEmployer(Calculator $calculator)
    {
        $currentYear = date('Y');
        $previousYear = date('Y') - 1;

        $em = $this->getDoctrine()->getManager();

        $employers = $em->getRepository(Employer::class)->findBy([
            'user' => $this->getUser(),
        ]);

        $months = ['Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin', 'Juillet', 'Août', 'Septembre', 'Octobre', 'Novembre', 'Décembre'];
        $keys = [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12];

        $months = array_combine($keys, $months);

        $monthlyPrestasPerEmployer = [];
        $previousMonthlyPrestasPerEmployer = [];

        for ($i=1; $i<13; $i++) {
            foreach ($employers as $employer) {
                if (!isset($monthlyPrestasPerEmployer[$i]) && !isset($previousMonthlyPrestasPerEmployer[$i])) {
                    $monthlyPrestasPerEmployer[$i] = [];
                    $previousMonthlyPrestasPerEmployer[$i] = [];
                }

                if ($i<10) {
                    $monthlyPrestasPerEmployer[$i][] = $em->getRepository(Prestation::class)->findAllByMonthAndEmployer("$currentYear-0$i-01", "$currentYear-0$i-31", $employer, $this->getUser());
                    $previousMonthlyPrestasPerEmployer[$i][] = $em->getRepository(Prestation::class)->findAllByMonthAndEmployer("$previousYear-0$i-01", "$previousYear-0$i-31", $employer, $this->getUser());
                } else {
                    $monthlyPrestasPerEmployer[$i][] = $em->getRepository(Prestation::class)->findAllByMonthAndEmployer("$currentYear-$i-01", "$currentYear-$i-31", $employer, $this->getUser());
                    $previousMonthlyPrestasPerEmployer[$i][] = $em->getRepository(Prestation::class)->findAllByMonthAndEmployer("$previousYear-$i-01", "$previousYear-$i-31", $employer, $this->getUser());
                }
            }
        }

        // CURRENT YEAR GAINS-HOURS
        $monthsHours = [];
        $monthsGains = [];

        foreach ($monthlyPrestasPerEmployer as $key => $prestasPerEmployer) {
            foreach ($prestasPerEmployer as $x => $prestas) {
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

                $decimalTimeWorked = (($hours*3600) + ($minutes*60) + $secondes) / 3600;
                $monthsHours[$key][$x] = $calculator->convertTime($decimalTimeWorked);
                $monthsGains[$key][$x] = $monthGains;
            }
        }

        // PREVIOUS YEAR GAINS-HOURS
        $previousMonthsHours = [];
        $previousMonthsGains = [];

        foreach ($monthlyPrestasPerEmployer as $key => $prestasPerEmployer) {
            foreach ($prestasPerEmployer as $x => $prestas) {
                $previousMonthGains = 0;
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

                $decimalTimeWorked = (($hours*3600) + ($minutes*60) + $secondes) / 3600;
                $previousMonthsHours[$key][$x] = $calculator->convertTime($decimalTimeWorked);
                $previousMonthsGains[$key][$x] = $previousMonthGains;
            }
        }

        return $this->render('earning/perMonthEmployer.html.twig', [
            'currentYear' => $currentYear,
            'previousYear' => $previousYear,
            'monthlyPrestasPerEmployer' => $monthlyPrestasPerEmployer,
            'previousMonthlyPrestasPerEmployer' => $previousMonthlyPrestasPerEmployer,
            'months' => $months,
            'monthsGains' => $monthsGains,
            'monthsHours' => $monthsHours,
            'previousMonthsGains' => $previousMonthsGains,
            'previousMonthsHours' => $previousMonthsHours,
        ]);
    }
}
