<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiResource;
use Symfony\Component\Serializer\Annotation\Groups;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ApiFilter(SearchFilter::class, properties={"yearPlan": "exact"})
 * @ApiResource( 
 * itemOperations={"get"},
 * collectionOperations={"get"={"security"="is_granted('ROLE_USER')"}},
 * normalizationContext={"groups"={"field:read"}} 
 * )
 * @ORM\Entity(repositoryClass="App\Repository\FieldRepository")
 */
class Field {

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @Groups({"field:read"})
     * 
     */
    private $id;

    /**
     * @Groups({"field:read"})
     * @ORM\Column(type="string", length=100)
     */
    
    private $name;
    
        /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $number;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\YearPlan", inversedBy="fields")
     * @ORM\JoinColumn(nullable=false)
     * @Groups({"field:read"})
     */
    private $yearPlan;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Parcel", cascade={"persist"}, mappedBy="field", orphanRemoval=true)
     */
    private $parcels;

    /**
     * @Groups({"field:read"})
     * @ORM\Column(type="string", length=20, nullable=true)
     */
    private $plantVariety;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Plant")
     * @Groups({"field:read"})
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

    public function getPlantVariety(): ?string {
        return $this->plantVariety;
    }

    public function setPlantVariety(?string $plant_variety): self {
        $this->plantVariety = $plant_variety;

        return $this;
    }

    public function getPlant(): ?Plant {
        return $this->plant;
    }

    public function setPlant(?Plant $plant): self {
        $this->plant = $plant;

        return $this;
    }

    public function getParcelAmount() {
        return $this->parcels->count();
    }

    public function getFieldArea() {
        $area = 0;
        foreach ($this->parcels as $parcel) {
            $area += $parcel->getCultivatedArea();
        }
        return $area/100;
    }

    public function getNumber(): ?int
    {
        return $this->number;
    }

    public function setNumber(int $number): self
    {
        $this->number = $number;

        return $this;
    }
        public function setNewNumber(): self
    {
         $fields = $this->yearPlan->getFields();
         $max = 1;
         foreach($fields as $field) {
             $current = $field->getNumber();
             $max = $current > $max ? $current : $max; 
         }
         $this->number = $max+1;
         return $this;
        
    }

}
