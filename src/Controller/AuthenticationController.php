<?php

namespace App\Controller;

use App\Repository\UserRepository;
use App\Service\Token;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoder;

/**
 * @Route("/authentication", name="authentication_")
 */
class AuthenticationController extends AbstractController
{
    /**
     * @param UserRepository $userRepository
     * @param UserPasswordEncoder $userPasswordEncoder
     * @param Token $token
     * @return JsonResponse
     * @Route("/login", name="login", methods={"POST"})
     */
    public function login(
        UserRepository $userRepository,
        UserPasswordEncoder $userPasswordEncoder,
        Token $token
    )
    {
        if ($request->getUser() === null or $request->getPassword() === null) {
            return new JsonResponse(null, 400);
        }
        $user = $userRepository->findOneBy(['username' => $request->getUser()]);
        if ($user === null or !$userPasswordEncoder->isPasswordValid($user, $request->getPassword())) {
            return new JsonResponse(null, 401);
        }
        $authenticationToken = $token->new($user, Token::AUTHENTICATION);
        return new JsonResponse($authenticationToken->getValue(), 200);
    }
}