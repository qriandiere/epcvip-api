<?php

namespace App\EventListener;

use App\Doctrine\EnumStatusDefaultType;
use App\Entity\Notification;
use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Doctrine\ORM\Event\OnFlushEventArgs;
use Doctrine\ORM\Event\PreFlushEventArgs;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Exception\ApiException;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Workflow\Registry;

/**
 * Class EntityListener
 * @package App\Listener
 */
class EntityListener
{
    /** @var string */
    const WORKFLOW_TRANSITION_DELETED = 'delete';
    /* @var ContainerInterface $container */
    private $container;
    /** @var Registry $registry */
    private $registry;
    /** @var UserRepository $userRepository */
    private $userRepository;

    /**
     * EntityListener constructor.
     * @param ContainerInterface $container
     * @param Registry $registry
     * @param UserRepository $userRepository
     */
    public function __construct(ContainerInterface $container, Registry $registry, UserRepository $userRepository)
    {
        $this->container = $container;
        $this->registry = $registry;
        $this->userRepository = $userRepository;
    }


    /**
     * @param $object
     * @return User
     * @throws \Exception
     */
    private function getUser($object)
    {
        if (
            $this->container->get('security.token_storage')->getToken() instanceof TokenInterface
            and
            $this->container->get('security.token_storage')->getToken()->getUser() instanceof User
        ) {
            return $this->container->get('security.token_storage')->getToken()->getUser();
        } else if ($object instanceof User) {
            return $object;
        }
        return null;
    }

    /**
     * @param PreFlushEventArgs $event
     * @throws \Doctrine\ORM\ORMException
     */
    public function preFlush(PreFlushEventArgs $event)
    {
        $em = $event->getEntityManager();
        $now = new \DateTime();
        foreach ($event->getEntityManager()->getUnitOfWork()->getScheduledEntityInsertions() as $object) {
            $author = $this->getUser($object);
            if ($author !== null)
                $object
                    ->setAuthor($author);
            $object
                ->setCreatedAt($now);
            if($object->getStatus() === null)
                $object
                    ->setStatus(EnumStatusDefaultType::STATUS_NEW);
            $em->merge($object);
            $em->persist($object);
        }
        foreach ($event->getEntityManager()->getUnitOfWork()->getScheduledEntityUpdates() as $object) {
            $object
                ->setUpdatedAt($now);
            $em->merge($object);
            $em->persist($object);
        }
        // Soft delete : we don't want to delete record, so we will just mark them as deleted
        // so they will be filtered out of all our queries by our doctrine filter
        foreach ($em->getUnitOfWork()->getScheduledEntityDeletions() as $object) {
            $workflow = $this->registry->get($object);
            if (!$workflow->can($object, self::WORKFLOW_TRANSITION_DELETED))
                throw new ApiException(
                    JsonResponse::HTTP_FORBIDDEN,
                    'Transition forbidden'
                );
            $workflow->apply($object, self::WORKFLOW_TRANSITION_DELETED);
            $object
                ->setDeletedAt(new \DateTime());
            $em->merge($object);
            $em->persist($object);
        }
    }
}