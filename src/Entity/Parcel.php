<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ParcelRepository")
 */
class Parcel
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=10)
     */
    private $parcelNumber;

    /**
     * @ORM\Column(type="integer")
     */
    private $cultivatedArea;

    /**
     * @ORM\Column(type="boolean")
     */
    private $fuelApplication;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Operator", inversedBy="parcels")
     */
    private $ArimrOperator;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\yearPlan", inversedBy="parcels")
     * @ORM\JoinColumn(nullable=false)
     */
    private $yearPlan;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\field", inversedBy="parcels")
     */
    private $field;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getParcelNumber(): ?string
    {
        return $this->parcelNumber;
    }

    public function setParcelNumber(string $parcelNumber): self
    {
        $this->parcelNumber = $parcelNumber;

        return $this;
    }

    public function getCultivatedArea(): ?int
    {
        return $this->cultivatedArea;
    }

    public function setCultivatedArea(int $cultivatedArea): self
    {
        $this->cultivatedArea = $cultivatedArea;

        return $this;
    }

    public function getFuelApplication(): ?bool
    {
        return $this->fuelApplication;
    }

    public function setFuelApplication(bool $fuelApplication): self
    {
        $this->fuelApplication = $fuelApplication;

        return $this;
    }

    public function getArimrOperator(): ?Operator
    {
        return $this->ArimrOperator;
    }

    public function setArimrOperator(?Operator $ArimrOperator): self
    {
        $this->ArimrOperator = $ArimrOperator;

        return $this;
    }

    public function getYearPlan(): ?yearPlan
    {
        return $this->yearPlan;
    }

    public function setYearPlan(?yearPlan $yearPlan): self
    {
        $this->yearPlan = $yearPlan;

        return $this;
    }

    public function getField(): ?field
    {
        return $this->field;
    }

    public function setField(?field $field): self
    {
        $this->field = $field;

        return $this;
    }
}
