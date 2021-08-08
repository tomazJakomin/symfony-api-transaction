<?php

namespace App\Repository;

use App\Entity\CustomerBonusTransactions;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method CustomerBonusTransactions|null find($id, $lockMode = null, $lockVersion = null)
 * @method CustomerBonusTransactions|null findOneBy(array $criteria, array $orderBy = null)
 * @method CustomerBonusTransactions[]    findAll()
 * @method CustomerBonusTransactions[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CustomerBonusTransactionsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CustomerBonusTransactions::class);
    }
}
