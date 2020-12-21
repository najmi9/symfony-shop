<?php

namespace App\Entity;

use App\Repository\OrderRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=OrderRepository::class)
 * @ORM\Table(name="`order`")
 */
class Order
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
    private $identifiant;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $status;

    /**
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $approveLink;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $amount;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $payee;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="orders")
     * @ORM\JoinColumn(nullable=false)
     */
    private $user;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $updatedAt;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $facilitatorAccessToken;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $payeerId;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $paymentId;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $billingToken;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIdentifiant(): string
    {
        return $this->identifiant;
    }

    public function setIdentifiant(string $ID): self
    {
        $this->identifiant = $ID;

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getApproveLink(): ?string
    {
        return $this->approveLink;
    }

    public function setApproveLink(string $approveLink): self
    {
        $this->approveLink = $approveLink;

        return $this;
    }

    public function getAmount(): ?string
    {
        return $this->amount;
    }

    public function setAmount(string $amount): self
    {
        $this->amount = $amount;

        return $this;
    }

    public function getPayee(): ?string
    {
        return $this->payee;
    }

    public function setPayee(string $payee): self
    {
        $this->payee = $payee;

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

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?\DateTimeInterface $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    public function getFacilitatorAccessToken(): ?string
    {
        return $this->facilitatorAccessToken;
    }

    public function setFacilitatorAccessToken(?string $facilitatorAccessToken): self
    {
        $this->facilitatorAccessToken = $facilitatorAccessToken;

        return $this;
    }

    public function getPayeerId(): ?string
    {
        return $this->payeerId;
    }

    public function setPayeerId(?string $payeerId): self
    {
        $this->payeerId = $payeerId;

        return $this;
    }

    public function getPaymentId(): ?string
    {
        return $this->paymentId;
    }

    public function setPaymentId(?string $paymentId): self
    {
        $this->paymentId = $paymentId;

        return $this;
    }

    public function getBillingToken(): ?string
    {
        return $this->billingToken;
    }

    public function setBillingToken(?string $billingToken): self
    {
        $this->billingToken = $billingToken;

        return $this;
    }
}
