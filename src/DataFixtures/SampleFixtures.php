<?php

namespace App\DataFixtures;

use App\Doctrine\EnumStatusDefaultType;
use App\Doctrine\EnumStatusExtendedType;
use App\Entity\Customer;
use App\Entity\Product;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * Class SampleFixtures
 * @package App\DataFixtures
 */
class SampleFixtures extends Fixture
{

    /** @var UserPasswordEncoderInterface $passwordEncoder */
    private $passwordEncoder;
    /** @var EntityManagerInterface $em */
    private $em;
    /** @var \DateTime $now */
    private $now;
    /** @var User $author */
    private $author;
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
     * SampleFixtures constructor.
     * @param UserPasswordEncoderInterface $passwordEncoder
     * @throws \Exception
     */
    public function __construct(UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
        $this->now = new \DateTime();
    }

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
        $username = strtolower(str_replace('ROLE_', '', $role));
        $user = (new User())
            ->setUsername($username . $i)
            ->setRoles([$role])
            ->setCreatedAt($this->now)
            ->setStatus(EnumStatusDefaultType::STATUS_ACTIVE);
        $password = $this->passwordEncoder->encodePassword(
            $user, 'password'
        );
        $user
            ->setPassword($password);
        $this->em->persist($user);
        if ($i === 1) {
            $this->author = $user;
        }
    }

    /**
     * @throws \Exception
     */
    private function customers()
    {
        for ($i = 1; $i <= self::COUNTS['customers']; $i++) {
            //Only 28 days so we're sure to not have invalid dates
            $dateOfBirth = rand(1950, 2000) . '-' . rand(1, 12) . '-' . rand(1, 28);
            $customer = (new Customer())
                ->setDateOfBirth(new \DateTime($dateOfBirth))
                ->setFirstName(self::FIRST_NAMES[rand(0, count(self::FIRST_NAMES) - 1)])
                ->setLastName(self::LAST_NAMES[rand(0, count(self::FIRST_NAMES) - 1)])
                ->setCreatedAt($this->now)
                ->setStatus(EnumStatusExtendedType::STATUS_PENDING)
                ->setAuthor($this->author);
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
                ->setName(self::PRODUCT_NAMES[rand(0, count(self::PRODUCT_NAMES) - 1)])
                ->setIssn('issn-' . $group1 . '-' . $group2)
                ->setCustomer($customer)
                ->setCreatedAt($this->now)
                ->setStatus(EnumStatusExtendedType::STATUS_PENDING)
                ->setAuthor($this->author);
            $this->em->persist($product);
        }
    }

    /**
     * @param ObjectManager $manager
     * @throws \Exception
     */
    public function load(ObjectManager $manager)
    {
        $this->em = $manager;
        $this->users();
        $manager->flush();
        $this->reviewers();
        $manager->flush();
        $this->admins();
        $manager->flush();
        $this->customers();
        $manager->flush();
    }
}