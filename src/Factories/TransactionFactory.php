<?php

namespace App\Factories;

use App\Entity\Customer;
use App\Entity\Transactions;
use App\options\TransactionTypes;
use DateTime;

class TransactionFactory
{
	public function createForWithdraw(Customer $customer, float $value): Transactions
	{
		return $this->createTransaction($customer, $value, TransactionTypes::WITHDRAW_TYPE);
	}

	public function createForDeposit(Customer $customer, float $value): Transactions
	{
		return $this->createTransaction($customer, $value, TransactionTypes::DEPOSIT_TYPE);
	}

	private function createTransaction(Customer $customer, float $value, string $type): Transactions
	{
		return (new Transactions())
			->setCustomer($customer)
			->setType($type)
			->setValue($value)
			->setDateCreated(new DateTime());
	}
}