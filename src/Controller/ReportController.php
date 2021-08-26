<?php

namespace App\Controller;

use App\Services\ReportServiceInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ReportController extends AbstractController
{
    /**
     * @Route("/report", name="report", methods={"GET"})
     */
    public function index(ReportServiceInterface $exportService): Response
    {
	     $reportResults = $exportService->exportDataForLastWeek();

	     return new JsonResponse(['data' => $reportResults]);
    }
}
