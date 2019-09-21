<?php

namespace App\Service;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Workflow\Registry;

/**
 * Class Workflow
 * @package App\Service
 */
class Workflow
{
    /** @var Security $security */
    private $security;
    /** @var Registry $registry */
    private $registry;
    /** @var EntityManagerInterface $em */
    private $em;

    /**
     * Workflow constructor.
     * @param Security $security
     * @param Registry $registry
     * @param EntityManagerInterface $em
     */
    public function __construct(Security $security, Registry $registry, EntityManagerInterface $em)
    {
        $this->security = $security;
        $this->registry = $registry;
        $this->em = $em;
    }

    /**
     * @param string $transition
     * @param $object
     * @return mixed
     */
    public function transition(
        string $transition, $object
    )
    {
        $workflow = $this->registry->get($object);
        if (!in_array($transition, $workflow->getDefinition()->getTransitions()))
            //@todo api exception
//            return new JsonResponse(null, 400);
            if (!$workflow->can($object, $transition))
                //@todo api exception
//            return new JsonResponse(null, 403);
                if (!$this->security->isGranted($transition, $object))
                    //@todo api exception

        $workflow->apply($transition, $object);
        $this->em->persist($object);
        $this->em->flush();
        return $object;
    }
}