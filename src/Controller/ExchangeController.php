<?php

namespace App\Controller;

use App\Factories\BonusFactory;
use App\Factories\TransactionFactory;
use App\Repository\CustomerRepository;
use App\Repository\TransactionsRepository;
use App\Services\BonusCalculationServiceInterface;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Swagger\Annotations as OA;
use Swagger\Annotations as SWG;

class ExchangeController extends AbstractController
{
	/**
	 * @Route("/exchange/deposit", name="deposit_money", methods={"POST"}, requirements={})
	 * @OA\Response(
	 *     response="200",
	 *     description="success",
	 * ),
	 * @OA\Response(
	 *     response="404",
	 *     description="customer not found",
	 * ),
	 * @OA\Response(
	 *     response="400",
	 *     description="value is not allowed",
	 * ),
	 * @OA\Response(
	 *     response="423",
	 *     description="data integrity constrain",
	 * )
	 *
	 * @SWG\Parameter (
	 *     name="customerId",
	 *     in="body",
	 *     @SWG\Schema(
	 *      type="integer"
	 *      )
	 *     ),
	 * @SWG\Parameter (
	 *     name="value",
	 *     in="body",
	 *     @SWG\Schema(
	 *      type="number"
	 *      )
	 * )
	 */
	public function depositMoney(
		Request $request,
		CustomerRepository $customerRepository,
		TransactionsRepository $transactionsRepository,
		ValidatorInterface $validator,
		BonusCalculationServiceInterface $bonusCalculationService,
		TransactionFactory $transactionFactory,
		BonusFactory $bonusFactory
	): Response {
		$data = json_decode($request->getContent(), true);
		$customer = $customerRepository->find($data['customerId'] ?? 0);

		if ($customer === null) {
			return new JsonResponse(['error' => "Customer not found"], Response::HTTP_NOT_FOUND);
		}

		$value = $data['value'] ?? 0.00;
		if (empty($value) || $value <= 0.00) {
			return new JsonResponse(['error' => "This amount can not be deposited"], Response::HTTP_BAD_REQUEST);
		}

		$transaction = $transactionFactory->createForDeposit($customer, $value);
		$errors = $validator->validate($transaction);

		if (count($errors) > 0) {
			$errorResponse = [];
			foreach ($errors as $error) {
				$errorResponse[$error->getPropertyPath()] = $error->getMessage();
			}

			return new JsonResponse(['error' => $errorResponse]);
		}
		$numberOfTransactions = $transactionsRepository->count(['customer' => $customer->getId()]);
		if ($bonusCalculationService->isEligibleForBonus($numberOfTransactions + 1)) {
			$valueBonus = $bonusCalculationService->getCalculatedBonus(
				$transaction->getValue(),
				$customer->getBonus()
			);

			$bonusTransaction = $bonusFactory->createBonusFrom($customer, $valueBonus);
			$transaction->setBonus($bonusTransaction);
		}

		try {
			$customerRepository->increaseAmount($customer->getId(), $transaction->getValue(), $customer->getVersion());
			$transactionsRepository->save($transaction);
		} catch (Exception $exception) {
			return new JsonResponse(['error' => $exception->getMessage()], JsonResponse::HTTP_LOCKED);
		}

		return new JsonResponse(['message' => 'success']);
	}

	/**
	 * @Route("/exchange/withdraw", name="withdraw_money", methods={"POST"})
	 * @OA\Response(
	 *     response="200",
	 *     description="success",
	 * ),
	 * @OA\Response(
	 *     response="404",
	 *     description="customer not found",
	 * ),
	 * @OA\Response(
	 *     response="400",
	 *     description="value is not correct",
	 * ),
	 * @OA\Response(
	 *     response="423",
	 *     description="data integrity constrain",
	 * )
	 *
	 * @SWG\Parameter (
	 *     name="customerId",
	 *     in="body",
	 *     @SWG\Schema(
	 *      type="integer"
	 *      )
	 *     ),
	 * @SWG\Parameter (
	 *     name="value",
	 *     in="body",
	 *     @SWG\Schema(
	 *      type="number"
	 *      )
	 * )
	 */
	public function withdrawMoney(
		Request $request,
		CustomerRepository $customerRepository,
		TransactionsRepository $transactionsRepository,
		ValidatorInterface $validator,
		TransactionFactory $transactionFactory
	): Response {
		$data = json_decode($request->getContent(), true);
		$value = $data['value'] ?? 0.0;
		$customerId = $data['customerId'] ?? 0;

		$customer = $customerRepository->find($customerId);

		if ($customer === null) {
			return new JsonResponse(['error' => "Customer not found"], Response::HTTP_NOT_FOUND);
		}

		$transaction = $transactionFactory->createForWithdraw($customer, $value);
		$errors = $validator->validate($transaction);

		if (count($errors) > 0) {
			$errorResponse = [];
			foreach ($errors as $error) {
				$errorResponse[$error->getPropertyPath()] = $error->getMessage();
			}

			return new JsonResponse(['error' => $errorResponse]);
		}

		if (empty($value) || $value <= 0.0 || $customer->getBalance() < $value) {
			return new JsonResponse(['error' => "This amount can not be withdraw"], Response::HTTP_BAD_REQUEST);
		}

		try {
			$customerRepository->decreaseAmount($customerId, $value, $customer->getVersion());
			$transactionsRepository->save($transaction);
		} catch (Exception $exception) {
			return new JsonResponse(['error' => $exception->getMessage()], JsonResponse::HTTP_LOCKED);
		}

		return new JsonResponse(['message' => 'success']);
	}

}
