<?php

namespace App\EventSubscriber;

use App\Entity\Log;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ControllerEvent;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;

/**
 * Class LoggerSubscriber
 * @package App\EventSubscriber
 */
class LoggerSubscriber implements EventSubscriberInterface
{
    #@todo https://stackoverflow.com/questions/41183215/how-to-create-a-custom-monolog-file-only-for-users-login-in-symfony
    /** @var EntityManagerInterface $em */
    private $em;
    /** @var ContainerInterface $container */
    private $container;
    /** @var LoggerInterface $requestLogger */
    private $requestLogger;
    /** @var LoggerInterface $responseLogger */
    private $responseLogger;

    /**
     * LoggerSubscriber constructor.
     * @param EntityManagerInterface $em
     * @param ContainerInterface $container
     * @param LoggerInterface $requestLogger
     * @param LoggerInterface $responseLogger
     */
    public function __construct(
        EntityManagerInterface $em,
        ContainerInterface $container,
        LoggerInterface $requestLogger,
        LoggerInterface $responseLogger
    )
    {
        $this->em = $em;
        $this->container = $container;
        $this->requestLogger = $requestLogger;
        $this->responseLogger = $responseLogger;
    }

    /**
     * @param ControllerEvent $event
     */
    public function onKernelController(ControllerEvent $event)
    {
        $request = json_encode($event->getRequest(), true);
        $log = (new Log())
            ->setRequest($request);
        $this->requestLogger->info($request);
        $event->getRequest()->getSession()->set('log', $log);
    }

    /**
     * @param ResponseEvent $event
     */
    public function onKernelResponse(ResponseEvent $event)
    {
        $response = json_encode($event->getResponse());
        $log = $event->getRequest()->getSession()->get('log');
        $log
            ->setResponse($response);
        $this->em->persist($log);
        $this->em->flush();
        $this->responseLogger->info($response);
    }

    /**
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return [
//            KernelEvents::CONTROLLER => 'onKernelController',
//            KernelEvents::RESPONSE => 'onKernelResponse',
        ];
    }
}