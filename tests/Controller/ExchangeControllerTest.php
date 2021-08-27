<?php

namespace App\Tests\Controller;

use App\Controller\ExchangeController;
use App\Entity\Customer;
use App\Entity\Transactions;
use App\Factories\BonusFactory;
use App\Factories\TransactionFactory;
use App\Repository\CustomerRepository;
use App\Repository\TransactionsRepository;
use App\Services\BonusCalculationServiceInterface;
use DateTime;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class ExchangeControllerTest extends TestCase
{
	private $mockedRequest;

	private $mockedCustomerRepo;

	private $mockedTransactionRepo;

	private $mockedValidator;

	private $mockedBonusCalculator;

	private $mockedTransactionFactory;

	private $mockedBonusFactory;

	private $testObject;

	protected function setUp(): void
	{
		parent::setUp();

		$this->mockedRequest = $this->createMock(Request::class);
		$this->mockedCustomerRepo = $this->createMock(CustomerRepository::class);
		$this->mockedTransactionRepo = $this->createMock(TransactionsRepository::class);
		$this->mockedValidator = $this->createMock(ValidatorInterface::class);
		$this->mockedBonusCalculator = $this->createMock(BonusCalculationServiceInterface::class);
		$this->mockedTransactionFactory = $this->createMock(TransactionFactory::class);
		$this->mockedBonusFactory = $this->createMock(BonusFactory::class);

		$this->testObject = new ExchangeController();
	}

	public function testDepositWithoutParams(): void
	 {

		 $result = $this->testObject->depositMoney(
		 	$this->mockedRequest,
		    $this->mockedCustomerRepo,
		    $this->mockedTransactionRepo,
		    $this->mockedValidator,
		    $this->mockedBonusCalculator,
		    $this->mockedTransactionFactory,
		    $this->mockedBonusFactory);

	 	$this->assertEquals((new JsonResponse(['error' => 'Customer not found'], JsonResponse::HTTP_NOT_FOUND)), $result);
	 }

	public function testDepositSuccess(): void
	{
		$this->mockedRequest
			->method('getContent')
			->willReturn(json_encode(['customerId' => 1, 'value' => 22.1]));

		$this->mockedValidator
			->method('validate')
			->willReturn([]);

		$transaction = $this->createMock(Transactions::class);
		$transaction
			->method('getValue')
			->willReturn(22.1);

		$mockCustomer = $this->createMock(Customer::class);
		$mockCustomer
			->method('getBalance')
			->willReturn(200);

		$mockCustomer
			->method('getId')
			->willReturn(33);

		$currentTime = new DateTime();
		$mockCustomer
			->method('getVersion')
			->willReturn($currentTime);

		$this->mockedTransactionFactory
			->method('createForDeposit')
			->with($mockCustomer, 22.1)
			->willReturn($transaction);

		$this->mockedCustomerRepo
			->method('find')
			->willReturn($mockCustomer);

		$this->mockedCustomerRepo
			->expects($this->once())
			->method('increaseAmount')
			->with(33, 22.1, $currentTime);

		$this->mockedTransactionRepo
			->expects($this->once())
			->method('save');
		
		$result = $this->testObject->depositMoney(
			$this->mockedRequest,
			$this->mockedCustomerRepo,
			$this->mockedTransactionRepo,
			$this->mockedValidator,
			$this->mockedBonusCalculator,
			$this->mockedTransactionFactory,
			$this->mockedBonusFactory);

		$this->assertEquals((new JsonResponse(['message' => 'success'])), $result);
	}
}