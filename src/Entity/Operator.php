<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiResource;
use Symfony\Component\Serializer\Annotation\Groups;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ApiFilter(SearchFilter::class, properties={"yearPlan": "exact"})
 * @ApiResource( 
 * itemOperations={"get"={"security"="is_granted('ROLE_ADMIN')"}},
 * collectionOperations={"get"},
 * normalizationContext={"groups"={"operator:read"}} 
 * )
 * @ORM\Entity(repositoryClass="App\Repository\OperatorRepository")
 */
class Operator {

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @Groups({"operator:read"})
     */
    private $id;

    /**
     * @Groups({"operator:read"})
     * @ORM\Column(type="string", length=30)
     */
    private $firstName;

    /**
     * @Groups({"operator:read"})
     * @ORM\Column(type="string", length=30)
     */
    private $surname;

    /**
     * @ORM\Column(type="string", length=11, nullable=true)
     */
    private $arimrNumber;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $disable;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Parcel", mappedBy="ArimrOperator")
     */
    private $parcels;

    /**
     * @Groups("operator:read")
     * @ORM\ManyToOne(targetEntity="App\Entity\YearPlan", inversedBy="operators")
     * @ORM\JoinColumn(nullable=false)
     */
    private $yearPlan;

    public function __construct() {
        $this->parcels = new ArrayCollection();
    }

    public function getId(): ?int {
        return $this->id;
    }

    public function getFirstName(): ?string {
        return $this->firstName;
    }

    public function setFirstName(string $firstName): self {
        $this->firstName = $firstName;

        return $this;
    }

    public function getSurname(): ?string {
        return $this->surname;
    }

    public function setSurname(string $surname): self {
        $this->surname = $surname;

        return $this;
    }

    public function getArimrNumber(): ?int {
        return $this->arimrNumber;
    }

    public function setArimrNumber(string $arimrNumber): self {
        $this->arimrNumber = $arimrNumber;

        return $this;
    }

    public function getDisable(): ?bool {
        return $this->disable;
    }

    public function setDisable(bool $disable): self {
        $this->disable = $disable;

        return $this;
    }

    /**
     * @return Collection|Parcel[]
     */
    public function getParcels(): Collection {
        return $this->parcels;
    }

    /**
     * @return Collection|Parcel[]
     */
    public function getParcelsInYearPlan(YearPlan $yearPlan): Collection {
        $parcels = $this->parcels;
        $out = new ArrayCollection();
        foreach ($parcels as $parcel) {
            if ($parcel->getField()->getYearPlan() == $yearPlan) {
                $out->add($parcel);
            }
        }
        return $out;
    }

    public function addParcel(Parcel $parcel): self {
        if (!$this->parcels->contains($parcel)) {
            $this->parcels[] = $parcel;
            $parcel->setArimrOperator($this);
        }

        return $this;
    }

    public function removeParcel(Parcel $parcel): self {
        if ($this->parcels->contains($parcel)) {
            $this->parcels->removeElement($parcel);
            // set the owning side to null (unless already changed)
            if ($parcel->getArimrOperator() === $this) {
                $parcel->setArimrOperator(null);
            }
        }

        return $this;
    }

    public function getYearPlan(): ?yearPlan {
        return $this->yearPlan;
    }

    public function setYearPlan(?yearPlan $yearPlan): self {
        $this->yearPlan = $yearPlan;
        return $this;
    }

    public function getCropedPlants() {
        $plants = new ArrayCollection();
        foreach ($this->parcels as $parcel) {
            $plant = $parcel->getField()->getPlant();
            if (!$plant)
                continue;
            if (!($plants->contains($plant))) {
                $plants->add($plant);
            }
        }
        return $plants;
    }

    public function getCropArea($plant) {
        $totalSize = 0;
        foreach ($this->parcels as $parcel) {
            if ($parcel->getField()->getPlant() == $plant)
                $totalSize += $parcel->getCultivatedArea();
        }
        return $totalSize / 100;
    }

    public function getTotalArea() {
        $totalSize = 0;
        foreach ($this->parcels as $parcel) {
            $totalSize += $parcel->getCultivatedArea();
        }
        return $totalSize / 100;
    }

    public function getFuelArea() {
        $totalSize = 0;
        foreach ($this->parcels as $parcel) {
            if ($parcel->getFuelApplication())
                $totalSize += $parcel->getCultivatedArea();
        }
        return $totalSize / 100;
    }

    public function getEfaPercent() {
        $area = 0;
        $percent = 0;
        foreach ($this->parcels as $parcel) {
            $plant = $parcel->getField()->getPlant();
            if ($plant) {
                if ($plant->getEfaNitrogen()) {
                    $area += $parcel->getCultivatedArea();
                }
            }
        }
        $percent = 100 / $this->getTotalArea() * $area / 100;
        return round($percent,1,PHP_ROUND_HALF_UP);
    }

    public function NotEstabilishedPlantArea() {
        $totalSize = 0;
        foreach ($this->parcels as $parcel) {
            $plant = $parcel->getField()->getPlant();
            if (!$plant) {
                $totalSize += $parcel->getCultivatedArea();
            }
        }
        return $totalSize / 100;
    }

}
