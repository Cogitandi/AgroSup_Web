<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiResource;
use Symfony\Component\Serializer\Annotation\Groups;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ApiFilter(SearchFilter::class, properties={"yearPlan": "exact"})
 * @ApiResource( 
 * itemOperations={"get"={"security"="is_granted('ROLE_ADMIN')"}},
 * collectionOperations={"get"={"security"="is_granted('ROLE_USER')"}},
 * normalizationContext={"groups"={"parcel:read"}} 
 * )
 * @ORM\Entity(repositoryClass="App\Repository\ParcelRepository")
 */
class Parcel
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @Groups({"parcel:read"})
     */
    private $id;

    /**
     * @Groups({"parcel:read"})
     * @ORM\Column(type="string", length=10)
     */
    private $parcelNumber;

    /**
     * @Groups({"parcel:read"})
     * @ORM\Column(type="integer")
     */
    private $cultivatedArea;

    /**
     * @Groups({"parcel:read"})
     * @ORM\Column(type="boolean")
     */
    private $fuelApplication;

    /**
     * @Groups({"parcel:read"})
     * @ORM\ManyToOne(targetEntity="App\Entity\Operator", inversedBy="parcels")
     */
    private $ArimrOperator;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\YearPlan", inversedBy="parcels")
     * @ORM\JoinColumn(nullable=false)
     */
    private $yearPlan;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Field", inversedBy="parcels")
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
    public function getCultivatedAreaHa(): ?float
    {
        return round(($this->cultivatedArea/100),2);
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
