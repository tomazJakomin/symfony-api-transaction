<?php

namespace App\Factories;

use App\Entity\Customer;
use App\Entity\CustomerBonusTransactions;

class BonusFactory
{
	public function createBonusFrom(Customer $customer, float $value): CustomerBonusTransactions
	{
		return (new CustomerBonusTransactions())
			->setCustomer($customer)
			->setAmount($value);
	}
}