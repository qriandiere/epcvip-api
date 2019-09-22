<?php

namespace App\Controller;

use App\Exception\ApiException;
use App\Repository\TokenRepository;
use App\Repository\UserRepository;
use App\Service\Serializer;
use App\Service\Token;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * @Route("/authentication", name="authentication_")
 */
class AuthenticationController extends AbstractController
{
    /**
     * @param UserRepository $userRepository
     * @param UserPasswordEncoderInterface $userPasswordEncoder
     * @param Token $token
     * @param Request $request
     * @param TokenRepository $tokenRepository
     * @param Serializer $serializer
     * @return JsonResponse
     * @throws \Exception
     * @Route("/login", name="login", methods={"POST"})
     */
    public function login(
        UserRepository $userRepository,
        UserPasswordEncoderInterface $userPasswordEncoder,
        Token $token,
        Request $request,
        TokenRepository $tokenRepository,
        Serializer $serializer
    )
    {
        if ($request->getUser() === null or $request->getPassword() === null) {
            throw new ApiException(
                JsonResponse::HTTP_BAD_REQUEST,
                'Username or password missing'
            );
        }
        $user = $userRepository->findOneBy(['username' => $request->getUser()]);
        if ($user === null or !$userPasswordEncoder->isPasswordValid($user, $request->getPassword())){
            throw new ApiException(
                JsonResponse::HTTP_UNAUTHORIZED,
                'Username or password invalid'
            );
        }
        $authenticationToken = $tokenRepository->findOneBy(['author' => $user, 'type' => Token::AUTHENTICATION]);
        if ($authenticationToken === null) $authenticationToken = $token->new($user, Token::AUTHENTICATION);
        return new JsonResponse(
            [
                'token' => $authenticationToken->getValue(),
                'user' => $serializer->serialize($user, ['user'])
            ],
            JsonResponse::HTTP_OK
        );
    }
}