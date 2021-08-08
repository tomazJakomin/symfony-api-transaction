<?php

namespace App\Entity;

use App\Repository\CustomerRepository;
use DateTimeInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=CustomerRepository::class)
 */
class Customer
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank
     */
    private $firstName;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank
     */
    private $lastName;

    /**
     * @ORM\Column(type="string", length=30, nullable=true)
     * @Assert\NotBlank
     */
    private $gender;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank
     */
    private $country;

    /**
     * @ORM\Column(type="string", length=255, unique=true)
     * @Assert\NotBlank
     */
    private $email;

    /**
     * @ORM\OneToMany(targetEntity=CustomerBonusTransactions::class, mappedBy="customer", orphanRemoval=true)
     */
    private $customerBonusTransactions;

    /**
     * @ORM\OneToMany(targetEntity=Transactions::class, mappedBy="customer", orphanRemoval=true)
     */
    private $transactions;

    /**
     * @ORM\Column(type="integer")
     */
    private $bonus;

	/**
	 * @ORM\Column(type="float")
	 */
    private $balance;

	/**
	 * @ORM\Version @ORM\Column(type="datetime")
	 */
	private $version;

    public function __construct()
    {
        $this->customerBonusTransactions = new ArrayCollection();
        $this->transactions = new ArrayCollection();
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

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(string $lastName): self
    {
        $this->lastName = $lastName;

        return $this;
    }

    public function getGender(): ?string
    {
        return $this->gender;
    }

    public function setGender(?string $gender): self
    {
        $this->gender = $gender;

        return $this;
    }

    public function getCountry(): ?string
    {
        return $this->country;
    }

    public function setCountry(string $country): self
    {
        $this->country = $country;

        return $this;
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

	public function toArray()
                                                	{
                                                		return [
                                                			'id' => $this->getId(),
                                                			'firstName' => $this->getFirstName(),
                                                			'lastName' => $this->getLastName(),
                                                			'email' => $this->getEmail(),
                                                			'country' => $this->getCountry()
                                                		];
                                                	}

    /**
     * @return Collection|CustomerBonusTransactions[]
     */
    public function getCustomerBonusTransactions(): Collection
    {
        return $this->customerBonusTransactions;
    }

    public function addCustomerBonusTransaction(CustomerBonusTransactions $customerBonusTransaction): self
    {
        if (!$this->customerBonusTransactions->contains($customerBonusTransaction)) {
            $this->customerBonusTransactions[] = $customerBonusTransaction;
            $customerBonusTransaction->setCustomer($this);
        }

        return $this;
    }

    public function removeCustomerBonusTransaction(CustomerBonusTransactions $customerBonusTransaction): self
    {
        if ($this->customerBonusTransactions->removeElement($customerBonusTransaction)) {
            // set the owning side to null (unless already changed)
            if ($customerBonusTransaction->getCustomer() === $this) {
                $customerBonusTransaction->setCustomer(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Transactions[]
     */
    public function getTransactions(): Collection
    {
        return $this->transactions;
    }

    public function addTransaction(Transactions $transaction): self
    {
        if (!$this->transactions->contains($transaction)) {
            $this->transactions[] = $transaction;
            $transaction->setCustomer($this);
        }

        return $this;
    }

    public function removeTransaction(Transactions $transaction): self
    {
        if ($this->transactions->removeElement($transaction)) {
            // set the owning side to null (unless already changed)
            if ($transaction->getCustomer() === $this) {
                $transaction->setCustomer(null);
            }
        }

        return $this;
    }

    public function getBonus(): ?int
    {
        return $this->bonus;
    }

    public function setBonus(int $bonus): self
    {
        $this->bonus = $bonus;

        return $this;
    }

	/**
	 * @return float
	 */
	public function getBalance()
	{
		return $this->balance;
	}

	/**
	 * @param float $balance
	 */
	public function setBalance(float $balance): Customer
	{
		$this->balance = $balance;

		return $this;
	}

	/**
	 * @return DateTimeInterface
	 */
	public function getVersion(): DateTimeInterface
	{
		return $this->version;
	}
}
