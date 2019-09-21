<?php

namespace App\Security\Voter;

use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Class UserVoter
 * @package App\Security\Voter
 */
class UserVoter extends Voter
{
    /** @var Security $security */
    private $security;

    /**
     * CustomerVoter constructor.
     * @param Security $security
     */
    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    /** @var string */
    const CREATE = 'create';
    /** @var string */
    const EDIT = 'edit';
    /** @var string */
    const VIEW = 'view';
    /** @var string */
    const DELETE = 'delete';

    /**
     * @param string $attribute
     * @param mixed $subject
     * @return bool
     */
    protected function supports($attribute, $subject)
    {
        return in_array($attribute, [
                self::CREATE,
                self::EDIT,
                self::VIEW,
                self::DELETE,
            ]) and $subject instanceof User;
    }

    /**
     * @param string $attribute
     * @param mixed $subject
     * @param TokenInterface $token
     * @return bool
     */
    protected function voteOnAttribute($attribute, $subject, TokenInterface $token)
    {
        $user = $token->getUser();
        if (!$user instanceof UserInterface) {
            return false;
        }
        switch ($attribute) {
            case self::CREATE:
                return $this->canCreate($subject, $user);
                break;
            case self::EDIT:
                return $this->canEdit($subject, $user);
                break;
            case self::VIEW:
                return $this->canView($subject, $user);
                break;
            case self::DELETE:
                return $this->canDelete($subject, $user);
                break;
        }

        return false;
    }

    /**
     * @param User $user
     * @param UserInterface $loggedInUser
     * @return bool
     */
    private function canDelete(User $user, UserInterface $loggedInUser)
    {
        //Allow an admin or the logged in user
        return $this->security->isGranted('ROLE_ADMIN') or $user === $loggedInUser;
    }

    /**
     * @param User $user
     * @param UserInterface $loggedInUser
     * @return bool
     */
    private function canEdit(User $user, UserInterface $loggedInUser)
    {
        //Allow an admin or the logged in user
        return $this->security->isGranted('ROLE_ADMIN') or $user === $loggedInUser;
    }

    /**
     * @param User $user
     * @param UserInterface $loggedInUser
     * @return bool
     */
    private function canCreate(User $user, UserInterface $loggedInUser)
    {
        //Allow an admin
        return $this->security->isGranted('ROLE_ADMIN');
    }

    /**
     * @param User $user
     * @param UserInterface $loggedInUser
     * @return bool
     */
    private function canView(User $user, UserInterface $loggedInUser)
    {
        //Allow an admin or the logged in user
        return $this->security->isGranted('ROLE_ADMIN') or $user === $loggedInUser;
    }
}