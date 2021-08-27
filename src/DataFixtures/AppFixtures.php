<?php

namespace App\DataFixtures;

use App\Entity\Customer;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
	public function load(ObjectManager $manager)
	{
		for ($i = 0; $i < 20; $i++) {
			$customer = new Customer();
			$customer
				->setFirstName('customer' . $i)
				->setLastName('faker' . $i)
				->setEmail('fake' . $i . '@gmail.com')
				->setCountry('Germany')
				->setGender('male')
				->setBonus(random_int(5, 20));
			$manager->persist($customer);
		}
		$manager->flush();
	}
}
