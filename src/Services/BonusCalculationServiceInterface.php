<?php

namespace App\Services;

interface BonusCalculationServiceInterface
{
	public function isEligibleForBonus(int $numberOfTransactions): bool;

	public function getCalculatedBonus(float $valueForBonus, int $customerDiscount): float;
}