<?php

namespace App\Entity;

use App\Repository\BlogRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=BlogRepository::class)
 */
class Blog
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @var string
     *
     * @ORM\Column(name="title", type="string", length=255, nullable=true)
     */
    private $title;

    /**
     * @var string
     *
     * @ORM\Column(name="templatetype", type="string", length=255, nullable=true)
     */
    private $templatetype;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_at", type="datetime", nullable=true)
     */
    private $DataPublicació;

    /**
     * @var booleam
     *
     * @ORM\Column(name="activada", type="boolean", nullable=true)
     * @Assert\Regex("/[0-1]+/")
     */
    private $activada;

    /**
     * @var booleam
     *
     * @ORM\Column(name="finalitzada", type="boolean", nullable=true)
     * @Assert\Regex("/[0-1]+/")
     */
    private $finalitzada;

     /**
     * @var \Users
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="Blog")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     * })
     */
    private $user;

     /**
     * @ORM\OneToMany(targetEntity="App\Entity\Contingut", mappedBy="entradaid")
     */
    private $continguts;

    public function __construct()
    {
        $this->continguts = new ArrayCollection();
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getTemplatetype(): ?string
    {
        return $this->templatetype;
    }

    public function setTemplatetype(string $templatetype): self
    {
        $this->templatetype = $templatetype;

        return $this;
    }


    public function getDataPublicació(): ?\DateTime
    {
        return $this->DataPublicació;
    }

    public function setDataPublicació(\DateTime $DataPublicació): self
    {
        $this->DataPublicació = $DataPublicació;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function isActivada(): ?bool
    {
        return $this->activada;
    }

    public function setActivada(bool $activada): self
    {
        $this->activada = $activada;

        return $this;
    }

    public function isFinalitzada(): ?bool
    {
        return $this->finalitzada;
    }

    public function setFinalitzada(bool $finalitzada): self
    {
        $this->finalitzada = $finalitzada;

        return $this;
    }

    /**
     * @return Collection|Contingut[]
     */
    public function getContinguts(): Collection
    {
        return $this->continguts;
    }

    public function addContingut(Contingut $contingut): self
    {
        if (!$this->continguts->contains($contingut)) {
            $this->continguts[] = $contingut;
            $contingut->setEntradaid($this);
        }

        return $this;
    }

    public function removeContingut(Contingut $contingut): self
    {
        if ($this->continguts->removeElement($contingut)) {
            // set the owning side to null (unless already changed)
            if ($contingut->getEntradaid() === $this) {
                $contingut->setEntradaid(null);
            }
        }

        return $this;
    }
}
