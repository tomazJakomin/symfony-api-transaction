<?php

namespace App\Controller;

use App\Entity\Customer;
use App\Entity\CustomerBonusTransactions;
use App\Entity\Transactions;
use App\Repository\CustomerRepository;
use App\Repository\TransactionsRepository;
use App\Services\BonusCalculationServiceInterface;
use DateTime;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class ExchangeController extends AbstractController
{
	/**
	 * @Route("/exchange", name="exchange")
	 */
	public function index(): Response
	{
		return $this->json([
			                   'message' => 'Welcome to your new controller!',
			                   'path'    => 'src/Controller/ExchangeController.php',
		                   ]);
	}

	/**
	 * @Route("/exchange/deposit", name="deposit_money", methods={"POST"})
	 */
	public function depositMoney(
		Request $request,
		CustomerRepository $customerRepository,
		TransactionsRepository $transactionsRepository,
		ValidatorInterface $validator,
		BonusCalculationServiceInterface $bonusCalculationService
	): Response {
		$data = json_decode($request->getContent(), true);
		$customer = $customerRepository->find($data['customerId'] ?? 0);

		$transaction = new Transactions();
		$transaction
			->setCustomer($customer)
			->setType('deposit')
			->setValue($data['value'] ?? 0.00)
			->setDateCreated(new DateTime());

		$errors = $validator->validate($transaction);

		if (count($errors) > 0) {
			$errorResponse = [];
			foreach ($errors as $error) {
				$errorResponse[$error->getPropertyPath()] = $error->getMessage();
			}

			return new JsonResponse(['error' => $errorResponse]);
		}

		$number = $transactionsRepository->count(['customer' => $customer->getId()]);
		if ($bonusCalculationService->isEligibleForBonus($number + 1)) {
			$valueBonus = $bonusCalculationService->getCalculatedBonus(
				$transaction->getValue(),
				$customer->getBonus()
			);

			$bonusTransaction = new CustomerBonusTransactions();
			$bonusTransaction
				->setCustomer($customer)
				->setAmount($valueBonus);

			$transaction->setBonus($bonusTransaction);
		}

		try {
			$customerRepository->increaseAmount($customer->getId(), $transaction->getValue(), $customer->getVersion());
			$transactionsRepository->save($transaction);
		} catch (Exception $exception) {
			return new JsonResponse(['error' => $exception->getMessage()], JsonResponse::HTTP_LOCKED);
		}

		return new JsonResponse(['message' => 'Successful deposit']);
	}

	/**
	 * @Route("/exchange/withdraw", name="withdraw_money", methods={"POST"})
	 */
	public function withdrawMoney(
		Request $request,
		CustomerRepository $customerRepository,
		TransactionsRepository $transactionsRepository,
		ValidatorInterface $validator
	): Response {

		$data = json_decode($request->getContent(), true);
		$value = $data['value'] ?? 0.0;
		$customerId = $data['customerId'] ?? 0;

		$customer = $customerRepository->find($customerId);

		if ($customer === null) {
			return new JsonResponse(['message' => "Customer not found"], Response::HTTP_NOT_FOUND);
		}

		$transaction = new Transactions();
		$transaction
			->setCustomer($customer)
			->setType('withdraw')
			->setValue($value);

		$errors = $validator->validate($transaction);

		if (count($errors) > 0) {
			$errorResponse = [];
			foreach ($errors as $error) {
				$errorResponse[$error->getPropertyPath()] = $error->getMessage();
			}

			return new JsonResponse(['message' => $errorResponse]);
		}

		if (empty($value) || $value <= 0.0 || $customer->getBalance() < $value) {
			return new JsonResponse(['message' => "This amount can not be withdraw"], Response::HTTP_BAD_REQUEST);
		}

		try {
			$customerRepository->decreaseAmount($customerId, $value, $customer->getVersion());
			$transactionsRepository->save($transaction);
		} catch (Exception $exception) {
			return new JsonResponse(['message' => $exception->getMessage()]);
		}

		return new JsonResponse(['message' => 'money was withdrawn successfully']);
	}

}
