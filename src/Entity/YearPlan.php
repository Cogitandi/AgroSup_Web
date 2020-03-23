<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Serializer\Annotation\Groups;
use ApiPlatform\Core\Annotation\ApiResource;

/**
 * @ApiResource( 
 * itemOperations={"get"={"security"="is_granted('ROLE_ADMIN')"}},
 * collectionOperations={"get"={"security"="is_granted('ROLE_USER')"}},
 * normalizationContext={"groups"={"yearPlan:read"}} 
 * )
 * @ORM\Entity(repositoryClass="App\Repository\YearPlanRepository")
 */
class YearPlan {

    /**
     * @Groups({"yearPlan:read"})
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @Groups({"yearPlan:read", "user:read"})
     * @ORM\Column(type="integer")
     */
    private $startYear;

    /**
     * @Groups({"yearPlan:read", "user:read"})
     * @ORM\Column(type="integer")
     */
    private $endYear;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $isClosed;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="yearPlans")
     * @ORM\JoinColumn(nullable=false)
     * @Groups({"yearPlan:read", "user:read"})
     */
    private $user;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Parcel", mappedBy="yearPlan", orphanRemoval=true)
     */
    private $parcels;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Field", mappedBy="yearPlan")
     */
    private $fields;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Operator", mappedBy="yearPlan")
     */
    private $operators;

    public function __construct() {
        $this->parcels = new ArrayCollection();
        $this->fields = new ArrayCollection();
        $this->operators = new ArrayCollection();
    }

    public function getId(): ?int {
        return $this->id;
    }

    public function getStartYear(): ?int {
        return $this->startYear;
    }

    public function setStartYear(int $startYear): self {
        $this->startYear = $startYear;

        return $this;
    }

    public function getEndYear(): ?int {
        return $this->endYear;
    }

    public function setEndYear(int $endYear): self {
        $this->endYear = $endYear;

        return $this;
    }

    public function getIsClosed(): ?bool {
        return $this->isClosed;
    }

    public function setIsClosed(bool $isClosed): self {
        $this->isClosed = $isClosed;

        return $this;
    }

    public function getUser(): ?user {
        return $this->user;
    }

    public function setUser(?user $user): self {
        $this->user = $user;

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
            $parcel->setYearPlan($this);
        }

        return $this;
    }

    public function removeParcel(Parcel $parcel): self {
        if ($this->parcels->contains($parcel)) {
            $this->parcels->removeElement($parcel);
            // set the owning side to null (unless already changed)
            if ($parcel->getYearPlan() === $this) {
                $parcel->setYearPlan(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Field[]
     */
    public function getFields(): Collection {
//        $out = new ArrayCollection();
//        foreach ($this->fields as $field) {
//            if ($field->getDisabled() == NULL) {
//                $out . add($field);
//            }
//        }
        return $this->fields;
    }

    public function addField(Field $field): self {
        if (!$this->fields->contains($field)) {
            $this->fields[] = $field;
            $field->setYearPlan($this);
        }

        return $this;
    }

    public function removeField(Field $field): self {
        if ($this->fields->contains($field)) {
            $this->fields->removeElement($field);
            // set the owning side to null (unless already changed)
            if ($field->getYearPlan() === $this) {
                $field->setYearPlan(null);
            }
        }

        return $this;
    }

    /**
     * @Assert\IsTrue(message="Nie możesz stworzyć dwóch planów do jednego roku")
     */
    public function isUniqueStartYear() {
        $yearPlanGiven = $this;
        $yearPlans = $this->getUser()->getYearPlans();
        foreach ($yearPlans as $yearPlan) {
            if (($yearPlan != $yearPlanGiven) && ($yearPlan->getStartYear() == $yearPlanGiven->getStartYear()))
                return false;
        }
        return true;
    }

    /**
     * @return Collection|Operator[]
     */
    public function getOperators(): Collection {
        $out = new ArrayCollection();

        foreach ($this->operators as $operator) {
            if (!$operator->getDisable())
                $out->add($operator);
        }
        return $out;
    }

    public function addOperator(Operator $operator): self {
        if (!$this->operators->contains($operator)) {
            $this->operators[] = $operator;
            $operator->setYearPlan($this);
        }

        return $this;
    }

    public function removeOperator(Operator $operator): self {
        if ($this->operators->contains($operator)) {
            $this->operators->removeElement($operator);
            // set the owning side to null (unless already changed)
            if ($operator->getYearPlan() === $this) {
                $operator->setYearPlan(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Plant[]
     */
    public function getPlants(): Collection {
        $plants = new ArrayCollection();
        foreach ($this->fields as $field) {
            $plant = $field->getPlant();
            if (!$plant)
                continue;
            if (!($plants->contains($plant))) {
                $plants->add($plant);
            }
        }
        return $plants;
    }

    public function getFieldAmount() {
        return $this->fields->count();
    }

}
