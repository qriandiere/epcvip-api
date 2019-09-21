<?php

namespace App\Service;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Class Token
 * @package App\Service
 */
class Token
{
    /** @var string */
    const AUTHENTICATION = 'authentication';
    /** @var EntityManagerInterface $em */
    private $em;

    /**
     * TokenService constructor.
     * @param EntityManagerInterface $em
     */
    public function __construct(
        EntityManagerInterface $em
    )
    {
        $this->em = $em;
    }

    /**
     * @param User $user
     * @param string $type
     * @return \App\Entity\Token
     */
    public function new(
        User $user,
        string $type
    )
    {
        $token = (new \App\Entity\Token())
            ->setValue(sha1(uniqid() . time() . $user->getId()))
            ->setType($type);
        $this->em->persist($token);
        $this->em->flush();
        return $token;
    }
}