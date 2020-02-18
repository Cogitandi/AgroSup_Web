<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass="App\Repository\PlantRepository")
 */
class Plant {

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @Groups("read")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=100)
     * @Groups("read")
     */
    private $name;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $efaNitrogen;

    public function getId(): ?int {
        return $this->id;
    }

    public function getName(): ?string {
        if ($this->efaNitrogen) {
            return $this->name . "(EFA)";
        } else {
            return $this->name;
        }
    }

    public function setName(?string $name): self {
        $this->name = $name;

        return $this;
    }

    public function getEfaNitrogen(): ?bool {
        return $this->efaNitrogen;
    }

    public function setEfaNitrogen(?bool $efaNitrogen): self {
        $this->efaNitrogen = $efaNitrogen;

        return $this;
    }

}
