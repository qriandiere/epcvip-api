<?php

namespace App\Service;

use App\Entity\User;

/**
 * Class Notification
 * @package App\Service
 */
class Notification
{
    /** @var string */
    const PENDING_PRODUCT = 'pending';

    /**
     * @param string $type
     * @param User $user
     * @return \App\Entity\Notification
     */
    public function new(
        string $type, User $user
    )
    {
        $notification = (new \App\Entity\Notification())
            ->setType($type)
            ->setUser($user);
        return $notification;
    }
}