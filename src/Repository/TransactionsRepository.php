<?php

namespace App\Repository;

use App\Entity\Transactions;
use DateTime;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Transactions|null find($id, $lockMode = null, $lockVersion = null)
 * @method Transactions|null findOneBy(array $criteria, array $orderBy = null)
 * @method Transactions[]    findAll()
 * @method Transactions[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TransactionsRepository extends ServiceEntityRepository
{
	/**
	 * @var EntityManagerInterface
	 */
	private $entityManager;

	public function __construct(ManagerRegistry $registry, EntityManagerInterface $entityManager)
	{
		parent::__construct($registry, Transactions::class);
		$this->entityManager = $entityManager;
	}

	public function save(Transactions $transaction): void
	{
		$transaction->setDateCreated(new DateTime());
		
		$this->entityManager->persist($transaction);
		$this->entityManager->flush();
	}

}
