<?php

namespace App\Services;

interface ReportServiceInterface
{
	public function exportDataForLastWeek(): array;
}