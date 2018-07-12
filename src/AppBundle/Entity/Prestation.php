<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Prestation
 *
 * @ORM\Table(name="prestation")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\PrestationRepository")
 */
class Prestation
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\User", inversedBy="prestations")
     * @ORM\JoinColumn(nullable=false)
     */
    private $user;

    /**
     * @var string
     *
     * @ORM\Column(name="prestation_date", type="date")
     */
    private $prestationDate;

    /**
     * @var string
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Employer", inversedBy="prestations")
     * @ORM\JoinColumn(name="employer_id", referencedColumnName="id")
     */
    private $employer;

    /**
     * @var string
     *
     * @ORM\Column(name="place", type="string", length=255)
     */
    private $place;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="start_time", type="datetime")
     */
    private $startTime;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="end_time", type="datetime")
     */
    private $endTime;

    /**
     * @var string
     *
     * @ORM\Column(name="hours_worked", type="string")
     */
    private $hoursWorked;

    /**
     * @var string
     *
     * @ORM\Column(name="minutes_worked", type="decimal", precision=10, scale=2)
     */
    private $minutesWorked;

    /**
     * @var string
     *
     * @ORM\Column(name="gross_honorary", type="decimal", precision=10, scale=2, nullable=true)
     * @Assert\Range(min=0, max=100, minMessage="Cette valeur ne peut être en dessous de 0.")
     */
    private $grossHonorary;

    /**
     * @var string
     *
     * @ORM\Column(name="net_honorary", type="decimal", precision=10, scale=2, nullable=true)
     * @Assert\Range(min=0, max=100, minMessage="Cette valeur ne peut être en dessous de 0.")
     */
    private $netHonorary;

    /**
     * @var string
     *
     * @ORM\Column(name="gross_gains", type="decimal", precision=10, scale=2, nullable=true)
     */
    private $grossGains;

    /**
     * @var string
     *
     * @ORM\Column(name="gross_to_net", type="decimal", precision=10, scale=2, nullable=true)
     */
    private $grossToNet;

    /**
     * @var string
     *
     * @ORM\Column(name="net_gains", type="decimal", precision=10, scale=2, nullable=true)
     */
    private $netGains;

    /**
     * @var string
     *
     * @ORM\Column(name="total_net_gains", type="decimal", precision=10, scale=2)
     */
    private $totalNetGains;


    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return mixed
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param mixed $user
     * @return Prestation
     */
    public function setUser($user)
    {
        $this->user = $user;
        return $this;
    }

    /**
     * Set prestationDate
     *
     * @param \DateTime $prestationDate
     *
     * @return Prestation
     */
    public function setPrestationDate($prestationDate)
    {
        $this->prestationDate = $prestationDate;

        return $this;
    }

    /**
     * Get prestationDate
     *
     * @return \DateTime
     */
    public function getPrestationDate()
    {
        return $this->prestationDate;
    }

    /**
     * Set employer
     *
     * @param string $employer
     *
     * @return Prestation
     */
    public function setEmployer($employer)
    {
        $this->employer = $employer;

        return $this;
    }

    /**
     * Get employer
     *
     * @return string
     */
    public function getEmployer()
    {
        return $this->employer;
    }

    /**
     * Set place
     *
     * @param string $place
     *
     * @return Prestation
     */
    public function setPlace($place)
    {
        $this->place = $place;

        return $this;
    }

    /**
     * Get place
     *
     * @return string
     */
    public function getPlace()
    {
        return $this->place;
    }

    /**
     * Set startTime
     *
     * @param \DateTime $startTime
     *
     * @return Prestation
     */
    public function setStartTime($startTime)
    {
        $this->startTime = $startTime;

        return $this;
    }

    /**
     * Get startTime
     *
     * @return \DateTime
     */
    public function getStartTime()
    {
        return $this->startTime;
    }

    /**
     * Set endTime
     *
     * @param \DateTime $endTime
     *
     * @return Prestation
     */
    public function setEndTime($endTime)
    {
        $this->endTime = $endTime;

        return $this;
    }

    /**
     * Get endTime
     *
     * @return \DateTime
     */
    public function getEndTime()
    {
        return $this->endTime;
    }

    /**
     * Set hoursWorked
     *
     * @return Prestation
     */
    public function setHoursWorked($hoursWorked)
    {
        $this->hoursWorked = $hoursWorked;

        return $this;
    }

    /**
     * Get hoursWorked
     *
     * @return string
     */
    public function getHoursWorked()
    {
        return $this->hoursWorked;
    }

    /**
     * Set minutesWorked
     *
     * @param string $minutesWorked
     *
     * @return Prestation
     */
    public function setMinutesWorked($minutesWorked)
    {
        $this->minutesWorked = $minutesWorked;

        return $this;
    }

    /**
     * Get minutesWorked
     *
     * @return string
     */
    public function getMinutesWorked()
    {
        return $this->minutesWorked;
    }

    /**
     * Set grossHonorary
     *
     * @param string $grossHonorary
     *
     * @return Prestation
     */
    public function setGrossHonorary($grossHonorary)
    {
        $this->grossHonorary = $grossHonorary;

        return $this;
    }

    /**
     * Get grossHonorary
     *
     * @return string
     */
    public function getGrossHonorary()
    {
        return $this->grossHonorary;
    }

    /**
     * Set netHonorary
     *
     * @param string $netHonorary
     *
     * @return Prestation
     */
    public function setNetHonorary($netHonorary)
    {
        $this->netHonorary = $netHonorary;

        return $this;
    }

    /**
     * Get netHonorary
     *
     * @return string
     */
    public function getNetHonorary()
    {
        return $this->netHonorary;
    }

    /**
     * Set grossGains
     *
     * @param string $grossGains
     *
     * @return Prestation
     */
    public function setGrossGains($grossGains)
    {
        $this->grossGains = $grossGains;

        return $this;
    }

    /**
     * Get grossGains
     *
     * @return string
     */
    public function getGrossGains()
    {
        return $this->grossGains;
    }

    /**
     * Set grossToNet
     *
     * @param string $grossToNet
     *
     * @return Prestation
     */
    public function setGrossToNet($grossToNet)
    {
        $this->grossToNet = $grossToNet;

        return $this;
    }

    /**
     * Get grossToNet
     *
     * @return string
     */
    public function getGrossToNet()
    {
        return $this->grossToNet;
    }

    /**
     * Set netGains
     *
     * @param string $netGains
     *
     * @return Prestation
     */
    public function setNetGains($netGains)
    {
        $this->netGains = $netGains;

        return $this;
    }

    /**
     * Get netGains
     *
     * @return string
     */
    public function getNetGains()
    {
        return $this->netGains;
    }

    /**
     * Set totalNetGains
     *
     * @param string $totalNetGains
     *
     * @return Prestation
     */
    public function setTotalNetGains($totalNetGains)
    {
        $this->totalNetGains = $totalNetGains;

        return $this;
    }

    /**
     * Get totalNetGains
     *
     * @return string
     */
    public function getTotalNetGains()
    {
        return $this->totalNetGains;
    }
}

