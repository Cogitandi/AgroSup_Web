<?php

namespace App\Entity;

use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\OperatorRepository")
 */
class Operator
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=30)
     */
    private $firstName;

    /**
     * @ORM\Column(type="string", length=30)
     */
    private $surname;

    /**
     * @ORM\Column(type="string", length=11, nullable=true)
     */
    private $arimrNumber;

    /**
     * @ORM\Column(type="boolean")
     */
    private $disable;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="operators")
     * @ORM\JoinColumn(nullable=false)
     */
    private $user;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Parcel", mappedBy="ArimrOperator")
     */
    private $parcels;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\yearPlan", inversedBy="operators")
     * @ORM\JoinColumn(nullable=false)
     */
    private $yearPlan;

    public function __construct()
    {
        $this->parcels = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(string $firstName): self
    {
        $this->firstName = $firstName;

        return $this;
    }

    public function getSurname(): ?string
    {
        return $this->surname;
    }

    public function setSurname(string $surname): self
    {
        $this->surname = $surname;

        return $this;
    }

    public function getArimrNumber(): ?int
    {
        return $this->arimrNumber;
    }

    public function setArimrNumber(string $arimrNumber): self
    {
        $this->arimrNumber = $arimrNumber;

        return $this;
    }

    public function getDisable(): ?bool
    {
        return $this->disable;
    }

    public function setDisable(bool $disable): self
    {
        $this->disable = $disable;

        return $this;
    }

    public function getUser(): ?user
    {
        return $this->user;
    }

    public function setUser(?user $user): self
    {
        $this->user = $user;

        return $this;
    }

    /**
     * @return Collection|Parcel[]
     */
    public function getParcels(): Collection
    {
        return $this->parcels;
    }
    /**
     * @return Collection|Parcel[]
     */
     public function getParcelsInYearPlan(YearPlan $yearPlan): Collection
    {
         $parcels = $this->parcels;
         $out = new ArrayCollection();
         foreach($parcels as $parcel) {
             if( $parcel->getField()->getYearPlan() == $yearPlan) {
                 $out->add($parcel);
             }
         }
         return $out;
    }

    public function addParcel(Parcel $parcel): self
    {
        if (!$this->parcels->contains($parcel)) {
            $this->parcels[] = $parcel;
            $parcel->setArimrOperator($this);
        }

        return $this;
    }

    public function removeParcel(Parcel $parcel): self
    {
        if ($this->parcels->contains($parcel)) {
            $this->parcels->removeElement($parcel);
            // set the owning side to null (unless already changed)
            if ($parcel->getArimrOperator() === $this) {
                $parcel->setArimrOperator(null);
            }
        }

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
}
