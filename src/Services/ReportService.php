<?php

namespace App\Services;

use DateInterval;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Query\ResultSetMappingBuilder;

class ReportService implements ReportServiceInterface
{
	private const REPORT_QUERY_FOR_TIME_PERIOD = "
	  SELECT
	       DATE(transactions.date_created) as date,
	       c.country,
	       sum(case when transactions.type = 'deposit' then 1 else 0 end) as deposits,
	       sum(case when transactions.type = 'withdraw' then 1 else 0 end) as withdraws,
	       count(distinct transactions.customer_id) as uniqueCustomers,
		   sum(case when transactions.type = 'deposit' then transactions.value else 0 end) as depositAmount,
		   sum(case when transactions.type = 'withdraw' then transactions.value else 0 end) as withdrawAmount
	FROM transactions
	INNER JOIN customer c on transactions.customer_id = c.id
	WHERE transactions.date_created between :fromDate and :toDate
	GROUP BY c.country, DATE(transactions.date_created)
	";

	/**
	 * @var EntityManagerInterface
	 */
	private $entityManager;

	public function __construct(EntityManagerInterface $entityManager)
	{
		$this->entityManager = $entityManager;
	}

	public function exportDataForLastWeek(): array
	{
		$res = $this->entityManager->createNativeQuery(self::REPORT_QUERY_FOR_TIME_PERIOD, $this->getResultMapper());

		$today = new DateTime();
		$toDate = $today->format('Y-m-d H:i:s');
		$fromDate = $today->sub($this->getOneWeekInterval())->format('Y-m-d H:i:s');

		$res->setParameters(
			[
				'fromDate' => $fromDate,
				'toDate'   => $toDate,
			]
		);

		$res->execute();
		
		return $res->getArrayResult();
	}

	private function getResultMapper(): ResultSetMappingBuilder
	{
		$mapper = new ResultSetMappingBuilder($this->entityManager);
		$mapper
			->addScalarResult('country', 'Country')
			->addScalarResult('date', 'Date')
			->addScalarResult('uniqueCustomers', 'Unique customers')
			->addScalarResult('deposits', 'No of Deposits')
			->addScalarResult('depositAmount', 'Total deposit amount')
			->addScalarResult('withdraws', 'No of Withdrawals')
			->addScalarResult('withdrawAmount', 'Total withdraw amount');

		return $mapper;
	}

	private function getOneWeekInterval(): DateInterval
	{
		return new DateInterval('P1W');
	}
}