<?php

namespace App\DataFixtures;

use App\Entity\Customer;
use App\Entity\Product;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Encoder\PasswordEncoderInterface;

/**
 * Class SampleFixtures
 * @package App\DataFixtures
 */
class SampleFixtures extends Fixture
{
    /** @var PasswordEncoderInterface $passwordEncoder */
    private $passwordEncoder;
    /** @var EntityManagerInterface $em */
    private $em;
    /**
     *
     */
    const FIRST_NAMES = [
        'James',
        'Tom',
        'Danny',
        'Bill',
        'Patrick',
        'Jacques',
        'Pierre',
        'Paul'
    ];
    /**
     *
     */
    const LAST_NAMES = [
        'Dean',
        'Brady',
        'Pacino',
        'Belichick',
        'Brel',
        'Chirac',
        'Dupont',
        'Dupuis'
    ];
    /**
     *
     */
    const COUNTS = [
        'users_user' => 10,
        'users_reviewer' => 2,
        'users_admin' => 1,
        'customers' => 10,
        'products_by_customer' => 2
    ];
    /**
     *
     */
    const PRODUCT_NAMES = [
        'billboard',
        'magazine',
        'tv',
        'internet'
    ];

    /**
     *
     */
    private function users()
    {
        for ($i = 1; $i <= self::COUNTS['users_user']; $i++) {
            $this->user('ROLE_USER', $i);
        }
    }

    /**
     *
     */
    private function reviewers()
    {
        for ($i = 1; $i <= self::COUNTS['users_reviewer']; $i++) {
            $this->user('ROLE_REVIEWER', $i);
        }
    }

    /**
     *
     */
    private function admins()
    {
        for ($i = 1; $i <= self::COUNTS['users_admin']; $i++) {
            $this->user('ROLE_ADMIN', $i);
        }
    }

    /**
     * @param string $role
     * @param int $i
     */
    private function user(string $role, int $i)
    {
        $password = $this->passwordEncoder->encodePassword(
            'user_' . $i . '_password', 'epcvip'
        );
        $user = (new User())
            ->setUsername('user_' . $role . '_' . $i)
            ->setRoles([$role])
            ->setPassword($password);
        $this->em->persist($user);
    }

    /**
     * @throws \Exception
     */
    private function customers()
    {
        for ($i = 1; $i <= self::COUNTS['customers']; $i++) {
            //Only 28 days so we're sure to not have invalid dates
            $dateOfBirth = rand(1950 - 2000) . '-' . rand(1, 12) . '-' . rand(1, 28);
            $customer = (new Customer())
                ->setDateOfBirth(new \DateTime($dateOfBirth))
                ->setFirstName(self::FIRST_NAMES[rand(0, count(self::FIRST_NAMES))])
                ->setLastName(self::LAST_NAMES[rand(0, count(self::FIRST_NAMES))])
                ->setUuid("ecpvip-uuid-$i");
            $this->em->persist($customer);
            $this->products($customer);
        }
        $this->em->flush();
    }

    /**
     * @param Customer $customer
     */
    private function products(Customer $customer)
    {
        for ($i = 1; $i <= self::COUNTS['products_by_customer']; $i++) {
            $group1 = rand(1000, 9999);
            $group2 = rand(1000, 9999);
            $product = (new Product())
                ->setName(self::PRODUCT_NAMES[rand(0, count(self::PRODUCT_NAMES))])
                ->setIssn('issn-' . $group1 . '-' . $group2)
                ->setCustomer($customer);
            $this->em->persist($product);
        }
    }

    /**
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        $this->em = $manager;
        $this->users();
        $this->reviewers();
        $this->admins();
        $manager->flush();
    }
}