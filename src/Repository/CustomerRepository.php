<?php

namespace App\Repository;

use App\Entity\Customer;
use DateTimeInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\DBAL\Exception;
use Doctrine\DBAL\LockMode;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\Persistence\ManagerRegistry;
use InvalidArgumentException;

/**
 * @method Customer|null find($id, $lockMode = null, $lockVersion = null)
 * @method Customer|null findOneBy(array $criteria, array $orderBy = null)
 * @method Customer[]    findAll()
 * @method Customer[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CustomerRepository extends ServiceEntityRepository
{
	/**
	 * @var EntityManagerInterface
	 */
	private $entityManager;

	public function __construct(ManagerRegistry $registry, EntityManagerInterface $entityManager)
	{
		parent::__construct($registry, Customer::class);
		$this->entityManager = $entityManager;
	}

	public function saveCustomer(Customer $customer): void
	{
		$this->entityManager->persist($customer);
		$this->entityManager->flush();
	}

	/**
	 * Using optimistic locking for ensuring data integrity on concurrent data writes
	 *
	 * @param int               $customerId
	 * @param float             $amount
	 * @param DateTimeInterface $version
	 *
	 * @throws Exception
	 */
	public function decreaseAmount(int $customerId, float $amount, DateTimeInterface $version): void
	{
		try {
			/** @var Customer $customer */
			$customer = $this->entityManager->find(Customer::class, $customerId, LockMode::OPTIMISTIC, $version);
			if ($customer->getBalance() < $amount) {
				throw new InvalidArgumentException("That amount is too big");
			}
			
			$balance = bcsub($customer->getBalance(), $amount, 2);

			$this->entityManager->persist($customer->setBalance($balance));
		} catch (OptimisticLockException $e) {
			throw new Exception("The amount has been modified since the last read");
		}
	}

	/**
	 * Using optimistic locking for ensuring data integrity on concurrent data writes
	 *
	 * @param int               $customerId
	 * @param float             $amount
	 * @param DateTimeInterface $version
	 *
	 * @throws Exception
	 */
	public function increaseAmount(int $customerId, float $amount, DateTimeInterface $version): void
	{
		try {
			/** @var Customer $customer */
			$customer = $this->entityManager->find(Customer::class, $customerId, LockMode::OPTIMISTIC, $version);

			$balance = bcadd($amount, $customer->getBalance(), 2);

			$this->entityManager->persist($customer->setBalance($balance));
		} catch (OptimisticLockException $e) {
			throw new Exception("The amount has been modified since the last read");
		}
	}
	
}
