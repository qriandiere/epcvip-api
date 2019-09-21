<?php

namespace App\EventSubscriber;

use App\Entity\Log;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Container\ContainerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;

/**
 * Class LoggerSubscriber
 * @package App\EventSubscriber
 */
class LoggerSubscriber implements EventSubscriberInterface
{
    /** @var EntityManagerInterface $em */
    private $em;
    /** @var ContainerInterface $container */
    private $container;

    /**
     * LoggerSubscriber constructor.
     * @param EntityManagerInterface $em
     * @param ContainerInterface $container
     */
    public function __construct(
        EntityManagerInterface $em,
        ContainerInterface $container
    )
    {
        $this->em = $em;
        $this->container = $container;
    }

    /**
     * @param RequestEvent $event
     */
    public function onKernelRequest(RequestEvent $event)
    {
        $request = $event->getRequest();
        $result = json_encode([
            'requestUri' => $request->getRequestUri(),
            'user' => $request->getUser(),
            'content' => $request->getContent(),
        ]);
        $log = (new Log())
            ->setRequest($result);
        $logger = $this->container->get('monolog.logger.request');
        $logger->notice($result);
        $event->getRequest()->getSession()->set('log', $log);
    }

    /**
     * @param ResponseEvent $event
     */
    public function onKernelResponse(ResponseEvent $event)
    {
        $log = $event->getRequest()->getSession()->get('log');
        if ($log === null) return;
        $response = $event->getResponse();
        $result = json_encode([
            'statusCode' => $response->getStatusCode(),
            'content' => $response->getContent(),
        ]);
        $log
            ->setResponse($result);
        $this->em->persist($log);
        $this->em->flush();
        $logger = $this->container->get('monolog.logger.response');
        $logger->notice($result);
    }

    /**
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::REQUEST => 'onKernelRequest',
            KernelEvents::RESPONSE => 'onKernelResponse',
        ];
    }
}