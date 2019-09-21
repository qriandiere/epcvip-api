<?php

namespace App\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Response;
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
            KernelEvents::REQUEST => array('onKernelRequest', 9999),
            KernelEvents::RESPONSE => array('onKernelResponse', 9999),
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
        if ('OPTIONS' == $method) {
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
        $response->headers->set('Access-Control-Allow-Origin', getenv('APP_FRONTEND_URL'));
        #Here, we set the HTTP methods authorized
        $response->headers->set('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS');
        #Finally, we authorized theses HTTP header
        $response->headers->set('Access-Control-Allow-Headers',
            'Authorization, 
            Content-Type, 
            X-Auth-Token, 
        ');
    }
}