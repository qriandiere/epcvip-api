<?php

namespace App\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;

/**
 * Class CorsSubscriber
 * @package App\EventSubscriber
 */
class CorsSubscriber implements EventSubscriberInterface
{
    /**
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return array(
            KernelEvents::REQUEST => ['onKernelRequest', 9999],
            KernelEvents::RESPONSE => ['onKernelResponse', 9999],
            KernelEvents::EXCEPTION => ['onKernelException', 9999]
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
        #Only the right front end can access it
        //@todo make me work
//        $response->headers->set('Access-Control-Allow-Origin', getenv('APP_FRONTEND_URL'));
        $response->headers->set('Access-Control-Allow-Origin', '*');
        #Here, we set the HTTP methods authorized
        $response->headers->set('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS');
        #Finally, we authorized theses HTTP header
        $response->headers->set('Access-Control-Allow-Headers',
            'Authorization, Content-Type, X-Auth-Token');
        $response->headers->set('Content-Type', 'application/json');
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