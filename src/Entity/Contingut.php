<?php

namespace App\Entity;

use App\Repository\ContingutRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ContingutRepository::class)
 */
class Contingut
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=10000)
     */
    private $contingut;

    /**
     * @ORM\Column(type="integer")
     */
    private $ordre;

    
     /**
      * @ORM\ManyToOne(targetEntity="App\Entity\Blog", inversedBy="continguts")
      * @ORM\JoinColumn(onDelete="CASCADE")
      */
    private $entradaid;

    

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getContingut(): ?string
    {
        return $this->contingut;
    }

    public function setContingut(string $contingut): self
    {
        $this->contingut = $contingut;
        return $this;
    }

    public function getOrdre(): ?int
    {
        return $this->ordre;
    }

    public function setOrdre(int $ordre): self
    {
        $this->ordre = $ordre;
        return $this;
    }

    public function getEntradaid(): ?Blog
    {
        return $this->entradaid;
    }

    public function setEntradaid(?Blog $entradaid): self
    {
        $this->entradaid = $entradaid;
        return $this;
    }
}
