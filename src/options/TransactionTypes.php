<?php

namespace App\options;

class TransactionTypes
{
	 public const DEPOSIT_TYPE = 'deposit';
	 public const WITHDRAW_TYPE = 'withdraw';

	 public static function getAllOptions(): array
	 {
	 	return [
	 	    self::DEPOSIT_TYPE,
	      self::WITHDRAW_TYPE,
	    ];
	 }
}