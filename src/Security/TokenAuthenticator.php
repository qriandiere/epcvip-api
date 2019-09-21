<?php

namespace App\Security;

use App\Entity\Token;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Guard\AbstractGuardAuthenticator;

/**
 * Class TokenAuthenticator
 * @package App\Security
 */
class TokenAuthenticator extends AbstractGuardAuthenticator
{
    /** @var EntityManagerInterface $em */
    private $em;

    /**
     * TokenAuthenticator constructor.
     * @param EntityManagerInterface $em
     */
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * @param Request $request
     * @return bool
     */
    public function supports(Request $request)
    {
        return $request->headers->has('X-AUTH-TOKEN');
    }

    /**
     * @param Request $request
     * @return array|mixed
     */
    public function getCredentials(Request $request)
    {
        return [
            'token' => $request->headers->get('X-AUTH-TOKEN'),
        ];
    }

    /**
     * @param mixed $credentials
     * @param UserProviderInterface $userProvider
     * @return \App\Entity\User|UserInterface|null
     */
    public function getUser($credentials, UserProviderInterface $userProvider)
    {
        $value = $credentials['token'];

        if (null === $value) {
            return null;
        }
        $token = $this->em->getRepository(Token::class)->findOneBy(['value' => $value]);
        // if a User object, checkCredentials() is called
        return $token === null ? null : $token->getUser();
    }

    /**
     * @param mixed $credentials
     * @param UserInterface $user
     * @return bool
     */
    public function checkCredentials($credentials, UserInterface $user)
    {
        // no credential check is needed in this case
        return true;
    }

    /**
     * @param Request $request
     * @param AuthenticationException $exception
     * @return JsonResponse|Response|null
     */
    public function onAuthenticationFailure(Request $request, AuthenticationException $exception)
    {
        $data = [
            'message' => strtr($exception->getMessageKey(), $exception->getMessageData())
        ];

        return new JsonResponse($data, Response::HTTP_FORBIDDEN);
    }

    /**
     * @param Request $request
     * @param TokenInterface $token
     * @param string $providerKey
     * @return Response|null
     */
    public function onAuthenticationSuccess(Request $request, TokenInterface $token, $providerKey)
    {
        // let the process continue
        return null;
    }

    /**
     * @param Request $request
     * @param AuthenticationException|null $authException
     * @return JsonResponse|Response
     */
    public function start(Request $request, AuthenticationException $authException = null)
    {
        $data = [
            'message' => 'Authentication Required'
        ];

        return new JsonResponse($data, Response::HTTP_UNAUTHORIZED);
    }

    /**
     * @return bool
     */
    public function supportsRememberMe()
    {
        return false;
    }
}