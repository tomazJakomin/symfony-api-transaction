<?php

namespace App\Entity;

use App\Repository\CustomerBonusTransactionsRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=CustomerBonusTransactionsRepository::class)
 */
class CustomerBonusTransactions
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=Customer::class, inversedBy="customerBonusTransactions")
     * @ORM\JoinColumn(nullable=false)
     */
    private $customer;

    /**
     * @ORM\Column(type="float")
     */
    private $amount;

    /**
     * @ORM\OneToOne(targetEntity=Transactions::class, mappedBy="bonus", cascade={"persist", "remove"})
     */
    private $transactions;

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

    public function getAmount(): ?float
    {
        return $this->amount;
    }

    public function setAmount(float $amount): self
    {
        $this->amount = $amount;

        return $this;
    }

    public function getTransactions(): ?Transactions
    {
        return $this->transactions;
    }

    public function setTransactions(?Transactions $transactions): self
    {
        // unset the owning side of the relation if necessary
        if ($transactions === null && $this->transactions !== null) {
            $this->transactions->setBonus(null);
        }

        // set the owning side of the relation if necessary
        if ($transactions !== null && $transactions->getBonus() !== $this) {
            $transactions->setBonus($this);
        }

        $this->transactions = $transactions;

        return $this;
    }
}
