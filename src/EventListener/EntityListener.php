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
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

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
    /** @var UserRepository $userRepository */
    private $userRepository;

    /**
     * EntityListener constructor.
     * @param ContainerInterface $container
     * @param UserRepository $userRepository
     */
    public function __construct(ContainerInterface $container, UserRepository $userRepository)
    {
        $this->container = $container;
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
        } else if ($object instanceof Notification) {
            return null;
        }
        // For fixtures
        return $this->userRepository->find(1);
    }

    /**
     * @param PreFlushEventArgs $event
     * @throws \Doctrine\ORM\ORMException
     */
    public function preFlush(PreFlushEventArgs $event)
    {
        $em = $event->getEntityManager();
        $registry = $this->container->get('workflow.registry');
        // Soft delete : we don't want to delete record, so we will just mark them as deleted
        // so they will be filtered out of all our queries by our doctrine filter
        foreach ($em->getUnitOfWork()->getScheduledEntityDeletions() as $object) {
            $workflow = $registry->get($object);
            //@todo api exception
            if (!$workflow->can($object, self::WORKFLOW_TRANSITION_DELETED))
                throw new HttpException(
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

    /**
     * @param OnFlushEventArgs $event
     * @throws \Doctrine\ORM\ORMException
     */
    public function onFlush(OnFlushEventArgs $event)
    {
        $now = new \DateTime();
        $em = $event->getEntityManager();
        foreach ($event->getEntityManager()->getUnitOfWork()->getScheduledEntityInsertions() as $object) {
            $author = $this->getUser($object);
            if ($author !== null)
                $object
                    ->setAuthor($author);
            $object
                ->setCreatedAt($now)
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
    }
}