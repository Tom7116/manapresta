<?php

namespace AppBundle\Repository;

use AppBundle\Entity\Employer;
use AppBundle\Entity\User;

/**
 * PrestationRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class PrestationRepository extends \Doctrine\ORM\EntityRepository
{
    /**
     * Used in PrestationController(index)
     *
     * @param User $user
     * @return array
     */
    public function findAllOrderByDate(User $user)
    {
        return $this->getEntityManager()
            ->createQuery("SELECT p FROM AppBundle:Prestation p WHERE p.user = :user ORDER BY p.prestationDate")
            ->setParameter(':user', $user)
            ->getResult();
    }

    /**
     * Used in EmployerController(showPrestations), EarningController(perEmployer)
     *
     * @param Employer $employer
     * @param User $user
     * @return array
     */
    public function findAllByEmployer(Employer $employer,User $user)
    {
        return $this->getEntityManager()
            ->createQuery("SELECT p FROM AppBundle:Prestation p WHERE p.employer = :employer AND p.user = :user ORDER BY p.prestationDate")
            ->setParameter(':employer', $employer)
            ->setParameter(':user', $user)
            ->getResult();
    }

    /**
     * Used in EarningController(perMonth)
     *
     * @param $start
     * @param $end
     * @param User $user
     * @return array
     */
    public function findAllByMonth($start, $end, User $user)
    {
        return $this->getEntityManager()
            ->createQuery("SELECT p FROM AppBundle:Prestation p WHERE p.prestationDate >= :startDate AND p.prestationDate <= :endDate AND p.user = :user ORDER BY p.prestationDate")
            ->setParameter(':startDate', $start)
            ->setParameter(':endDate', $end)
            ->setParameter(':user', $user)
            ->getResult();
    }

    /**
     * Used in EarningController(perMonthEmployer)
     *
     * @param $start
     * @param $end
     * @param Employer $employer
     * @param User $user
     * @return array
     */
    public function findAllByMonthAndEmployer($start, $end, Employer $employer, User $user)
    {
        return $this->getEntityManager()
            ->createQuery("SELECT p FROM AppBundle:Prestation p WHERE p.prestationDate >= :startDate AND p.prestationDate <= :endDate AND p.user = :user AND p.employer = :employer ORDER BY p.prestationDate")
            ->setParameter(':startDate', $start)
            ->setParameter(':endDate', $end)
            ->setParameter(':employer', $employer)
            ->setParameter(':user', $user)
            ->getResult();
    }
}
