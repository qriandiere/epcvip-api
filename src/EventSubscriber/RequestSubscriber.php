<?php

namespace App\EventSubscriber;

use App\Entity\Log;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Container\ContainerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ControllerEvent;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;

/**
 * Class CorsSubscriber
 * @package App\EventSubscriber
 */
class RequestSubscriber implements EventSubscriberInterface
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
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return array(
            KernelEvents::REQUEST => ['onKernelRequest', 9999],
            KernelEvents::RESPONSE => ['onKernelResponse', 9999],
            KernelEvents::EXCEPTION => ['onKernelException', 9999],
        );
    }

    /**
     * @param RequestEvent $event
     */
    public function onKernelRequest(RequestEvent $event)
    {
        if (!$event->isMasterRequest()) {
            return;
        }
        $request = $event->getRequest();
        $method = $request->getRealMethod();
        if ('OPTIONS' === $method) {
            $response = new Response();
            $event->setResponse($response);
        }
    }

    /**
     * @param ResponseEvent $event
     */
    public function onKernelResponse(ResponseEvent $event)
    {
        if (!$event->isMasterRequest()) {
            return;
        }
        $response = $event->getResponse();
        $response->headers->set('Access-Control-Allow-Origin', getenv('APP_FRONTEND_URL'));
        $response->headers->set('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS');
        $response->headers->set('Access-Control-Allow-Headers', 'Authorization, Content-Type, X-Auth-Token');
        $response->headers->set('Content-Type', 'application/json');
        /* Logging request and response */
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
     * @param ExceptionEvent $event
     */
    public function onKernelException(ExceptionEvent $event)
    {
        $exception = $event->getException();
        $code = $exception->getCode() === 0 ? 500 : $exception->getCode();
        $response = new JsonResponse([
            'message' => $exception->getMessage(),
            'code' => $code,
            'line' => $exception->getLine(),
            'trace' => $exception->getTrace()
        ], $code);
        $event->setResponse($response);
    }
}