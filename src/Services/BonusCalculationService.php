<?php

namespace App\Services;

class BonusCalculationService implements BonusCalculationServiceInterface
{
	private const BONUS_NUMBER_OF_TRANSACTIONS = 3;

	public function isEligibleForBonus(int $numberOfTransactions): bool
	{
		if ($numberOfTransactions === 0) {
			return false;
		}

		return $numberOfTransactions % self::BONUS_NUMBER_OF_TRANSACTIONS === 0;
	}

	public function getCalculatedBonus(float $valueForBonus, int $customerDiscount): float
	{
		return ($valueForBonus * $customerDiscount) / 100;
	}
}