<?php

namespace App\Security\Voter;

use App\Entity\Product;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Class ProductVoter
 * @package App\Security\Voter
 */
class ProductVoter extends Voter
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
    const SUBMIT = 'submit';
    /** @var string */
    const REVIEWING = 'reviewing';
    /** @var string */
    const APPROVAL = 'approval';
    /** @var string */
    const DISAPPROVAL = 'disapproval';
    /** @var string */
    const DEACTIVATION = 'deactivation';
    /** @var string */
    const ACTIVATION = 'activation';
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
                self::ACTIVATION,
                self::DEACTIVATION,
                self::DISAPPROVAL,
                self::APPROVAL,
                self::REVIEWING,
            ]) and $subject instanceof Product;
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
            case self::ACTIVATION:
                return $this->canActivate($subject, $user);
                break;
            case self::DEACTIVATION:
                return $this->canDeactivate($subject, $user);
                break;
            case self::DISAPPROVAL:
                return $this->canDisapprove($subject, $user);
                break;
            case self::APPROVAL:
                return $this->canApprove($subject, $user);
                break;
            case self::REVIEWING:
                return $this->canReview($subject, $user);
                break;
        }

        return false;
    }

    /**
     * @param Product $product
     * @param UserInterface $user
     * @return bool
     */
    private function canActivate(Product $product, UserInterface $user)
    {
        //Allow a reviewer
        return $this->security->isGranted('ROLE_REVIEWER');
    }

    /**
     * @param Product $product
     * @param UserInterface $user
     * @return bool
     */
    private function canDeactivate(Product $product, UserInterface $user)
    {
        //Allow a reviewer
        return $this->security->isGranted('ROLE_REVIEWER');
    }

    /**
     * @param Product $product
     * @param UserInterface $user
     * @return bool
     */
    private function canDisapprove(Product $product, UserInterface $user)
    {
        //Allow a reviewer
        return $this->security->isGranted('ROLE_REVIEWER');
    }

    /**
     * @param Product $product
     * @param UserInterface $user
     * @return bool
     */
    private function canApprove(Product $product, UserInterface $user)
    {
        //Allow a reviewer
        return $this->security->isGranted('ROLE_REVIEWER');
    }

    /**
     * @param Product $product
     * @param UserInterface $user
     * @return bool
     */
    private function canReview(Product $product, UserInterface $user)
    {
        //Allow a reviewer
        return $this->security->isGranted('ROLE_REVIEWER');
    }

    /**
     * @param Product $product
     * @param UserInterface $user
     * @return bool
     */
    private function canDelete(Product $product, UserInterface $user)
    {
        //Allow an admin or the author
        return $this->security->isGranted('ROLE_ADMIN') or $user === $product->getAuthor();
    }

    /**
     * @param Product $product
     * @param UserInterface $user
     * @return bool
     */
    private function canEdit(Product $product, UserInterface $user)
    {
        //logic goes here
        return true;
    }

    /**
     * @param Product $product
     * @param UserInterface $user
     * @return bool
     */
    private function canCreate(Product $product, UserInterface $user)
    {
        //logic goes here
        return true;
    }

    /**
     * @param Product $product
     * @param UserInterface $user
     * @return bool
     */
    private function canView(Product $product, UserInterface $user)
    {
        //login goes here
        return true;
    }
}