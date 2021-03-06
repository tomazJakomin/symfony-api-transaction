<?php

namespace App\Controller;

use App\Entity\Customer;
use App\Repository\CustomerRepository;
use Doctrine\DBAL\Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class CustomerController extends AbstractController
{
	/**
	 * @var CustomerRepository
	 */
	private $customerRepository;

	public function __construct(CustomerRepository $customerRepository)
	{
		$this->customerRepository = $customerRepository;
	}

	/**
	 * @Route("/customers", name="add_customer", methods={"POST"})
	 */
	public function addCustomer(Request $request, ValidatorInterface $validator): JsonResponse
	{
		$data = json_decode($request->getContent(), true);

		$customer = new Customer();
		$customer
			->setFirstName($data['firstName'] ?? '')
			->setLastName($data['lastName'] ?? '')
			->setEmail($data['email'] ?? '')
			->setCountry($data['country'] ?? '')
			->setGender($data['gender'] ?? '');

		$errors = $validator->validate($customer);
		if (count($errors) > 0) {
			$errorResponse = [];
			foreach ($errors as $error) {
				$errorResponse[$error->getPropertyPath()] = $error->getMessage();
			}

			return new JsonResponse(['error' => $errorResponse]);
		}

		$customer->setBonus($this->getAssignedBonus());
		try {
			$this->customerRepository->saveCustomer($customer);
		} catch (Exception $exception) {
			return new JsonResponse(['error' => "Provided user data is not valid"], Response::HTTP_BAD_REQUEST);
		}

		return new JsonResponse(['message' => "User created"], Response::HTTP_CREATED);
	}

	/**
	 * @Route("/customers", name="updated_customer", methods={"PATCH"})
	 */
	public function updateCustomer(CustomerRepository $customerRepository): JsonResponse
	{
		$customer = new Customer();

		if (!empty($data['firstName'] ?? '')) {
			$customer->setFirstName($data['firstName']);
		}

		if (!empty($data['lastName'] ?? '')) {
		   $customer->setLastName($data['lastName']);
		}

		if (!empty($data['email'] ?? '')) {
		   $customer->setEmail($data['email']);
		}

		if (!empty($data['country'] ?? '')) {
		   $customer->setCountry($data['country']);
		}

		if (!empty($data['gender'] ?? '')) {
			$customer->setGender($data['gender']);
		}

		try {
			$customerRepository->saveCustomer($customer);
		} catch (Exception $exception) {
			return new JsonResponse(['error' => $exception->getMessage()], JsonResponse::HTTP_BAD_REQUEST);
		}

		return new JsonResponse(['message' => 'User updated'], JsonResponse::HTTP_OK);
	}

	private function getAssignedBonus(): int
	{
		return random_int(5, 20);
	}
}
