<?php

namespace App\Entity;

use App\Repository\NewsletterRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=NewsletterRepository::class)
 */
class Newsletter
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $email;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $token;

    /**
     * @ORM\Column(type="boolean")
     */
    private $actiu;

    /**
     * @ORM\Column(type="datetime")
     */
    private $DataAlta;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $DataBaixa;

    /**
     * @ORM\Column(type="boolean")
     */
    private $Baixa;

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

    public function getToken(): ?string
    {
        return $this->token;
    }

    public function setToken(string $token): self
    {
        $this->token = $token;

        return $this;
    }

    public function isActiu(): ?bool
    {
        return $this->actiu;
    }

    public function setActiu(bool $actiu): self
    {
        $this->actiu = $actiu;

        return $this;
    }

    public function getDataAlta(): ?\DateTimeInterface
    {
        return $this->DataAlta;
    }

    public function setDataAlta(\DateTimeInterface $DataAlta): self
    {
        $this->DataAlta = $DataAlta;

        return $this;
    }

    public function getDataBaixa(): ?\DateTimeInterface
    {
        return $this->DataBaixa;
    }

    public function setDataBaixa(?\DateTimeInterface $DataBaixa): self
    {
        $this->DataBaixa = $DataBaixa;

        return $this;
    }

    public function isBaixa(): ?bool
    {
        return $this->Baixa;
    }

    public function setBaixa(bool $Baixa): self
    {
        $this->Baixa = $Baixa;

        return $this;
    }
}
