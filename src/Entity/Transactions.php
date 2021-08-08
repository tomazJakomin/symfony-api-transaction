<?php

namespace App\Entity;

use App\Repository\TransactionsRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use App\options\TransactionTypes;

/**
 * @ORM\Entity(repositoryClass=TransactionsRepository::class)
 */
class Transactions
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=Customer::class, inversedBy="transactions")
     * @ORM\JoinColumn(nullable=false)
     * @Assert\NotBlank()
     */
    private $customer;

    /**
     * @ORM\Column(type="string", length=100)
     * @Assert\NotBlank
     * @Assert\Choice(callback={"App\options\TransactionTypes", "getAllOptions"})
     */
    private $type;

    /**
     * @ORM\Column(type="float")
     * @Assert\GreaterThan(0.00)
     */
    private $value;

    /**
     * @ORM\OneToOne(targetEntity=CustomerBonusTransactions::class, inversedBy="transactions", cascade={"persist", "remove"})
     */
    private $bonus;

    /**
     * @ORM\Column(type="datetime")
     */
    private $dateCreated;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCustomer(): ?Customer
    {
        return $this->customer;
    }

    public function setCustomer(?Customer $customer): self
    {
        $this->customer = $customer;

        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getValue(): ?float
    {
        return $this->value;
    }

    public function setValue(float $value): self
    {
        $this->value = $value;

        return $this;
    }

    public function getBonus(): ?CustomerBonusTransactions
    {
        return $this->bonus;
    }

    public function setBonus(?CustomerBonusTransactions $bonus): self
    {
        $this->bonus = $bonus;

        return $this;
    }

    public function getDateCreated(): ?\DateTimeInterface
    {
        return $this->dateCreated;
    }

    public function setDateCreated(\DateTimeInterface $dateCreated): self
    {
        $this->dateCreated = $dateCreated;

        return $this;
    }
}
