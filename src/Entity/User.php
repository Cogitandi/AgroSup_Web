<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 * @UniqueEntity(fields={"email"}, message="There is already an account with this email")
 */
class User implements UserInterface
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=180, unique=true)
     */
    private $email;

    /**
     * @ORM\Column(type="json")
     */
    private $roles = [];

    /**
     * @var string The hashed password
     * @ORM\Column(type="string")
     */
    private $password;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Operator", mappedBy="user")
     */
    private $operators;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\YearPlan", mappedBy="user")
     */
    private $yearPlans;

    public function __construct()
    {
        $this->operators = new ArrayCollection();
        $this->yearPlans = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUsername(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getPassword(): string
    {
        return (string) $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getSalt()
    {
        // not needed when using the "bcrypt" algorithm in security.yaml
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    /**
     * @return Collection|Operator[]
     */
    public function getOperators(): Collection
    {
        $out = new ArrayCollection();
        foreach( $this->operators as $operator ) {
            if($operator->getDisable() == false )
                $out->add($operator);
        }
        return $out;
    }

    public function addOperator(Operator $operator): self
    {
        if (!$this->operators->contains($operator)) {
            $this->operators[] = $operator;
            $operator->setUser($this);
        }

        return $this;
    }

    public function removeOperator(Operator $operator): self
    {
        if ($this->operators->contains($operator)) {
            $this->operators->removeElement($operator);
            // set the owning side to null (unless already changed)
            if ($operator->getUser() === $this) {
                $operator->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|YearPlan[]
     */
    public function getYearPlans(): Collection
    {
        return $this->yearPlans;
    }

    public function addYearPlan(YearPlan $yearPlan): self
    {
        if (!$this->yearPlans->contains($yearPlan)) {
            $this->yearPlans[] = $yearPlan;
            $yearPlan->setUser($this);
        }

        return $this;
    }

    public function removeYearPlan(YearPlan $yearPlan): self
    {
        if ($this->yearPlans->contains($yearPlan)) {
            $this->yearPlans->removeElement($yearPlan);
            // set the owning side to null (unless already changed)
            if ($yearPlan->getUser() === $this) {
                $yearPlan->setUser(null);
            }
        }

        return $this;
    }
}
