<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\FieldRepository")
 */
class Field {

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=30)
     */
    private $name;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\yearPlan", inversedBy="fields")
     * @ORM\JoinColumn(nullable=false)
     */
    private $yearPlan;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Parcel", cascade={"persist"}, mappedBy="field", orphanRemoval=true)
     */
    private $parcels;

    /**
     * @ORM\Column(type="string", length=20, nullable=true)
     */
    private $plantVariety;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Plant")
     */
    private $plant;


    public function __construct() {
        $this->parcels = new ArrayCollection();
    }

    public function getId(): ?int {
        return $this->id;
    }

    public function getName(): ?string {
        return $this->name;
    }

    public function setName(string $name): self {
        $this->name = $name;

        return $this;
    }

    public function getYearPlan(): ?yearPlan {
        return $this->yearPlan;
    }

    public function setYearPlan(?yearPlan $yearPlan): self {
        $this->yearPlan = $yearPlan;

        return $this;
    }

    /**
     * @return Collection|Parcel[]
     */
    public function getParcels(): Collection {
        return $this->parcels;
    }

    public function addParcel(Parcel $parcel): self {

        if (!$this->parcels->contains($parcel)) {
            $this->parcels[] = $parcel;
            $parcel->setField($this);
        }

        return $this;
    }

    public function removeParcel(Parcel $parcel): self {
        if ($this->parcels->contains($parcel)) {
            $this->parcels->removeElement($parcel);
            // set the owning side to null (unless already changed)
            if ($parcel->getField() === $this) {
                $parcel->setField(null);
            }
        }

        return $this;
    }

    public function getPlantVariety(): ?string
    {
        return $this->plantVariety;
    }

    public function setPlantVariety(?string $plant_variety): self
    {
        $this->plantVariety = $plant_variety;

        return $this;
    }

    public function getPlant(): ?Plant
    {
        return $this->plant;
    }

    public function setPlant(?Plant $plant): self
    {
        $this->plant = $plant;

        return $this;
    }


}
