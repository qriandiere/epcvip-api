<?php

namespace App\Controller;

use App\Repository\UserRepository;
use App\Service\Token;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\HttpException;
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
     * @param Request $request
     * @return JsonResponse
     * @Route("/login", name="login", methods={"POST"})
     */
    public function login(
        UserRepository $userRepository,
        UserPasswordEncoder $userPasswordEncoder,
        Token $token,
        Request $request
    )
    {
        if ($request->getUser() === null or $request->getPassword() === null) {
            throw new HttpException(
                JsonResponse::HTTP_BAD_REQUEST,
                'Username or password missing'
            );
        }
        $user = $userRepository->findOneBy(['username' => $request->getUser()]);
        if ($user === null or !$userPasswordEncoder->isPasswordValid($user, $request->getPassword()))
            throw new HttpException(
                JsonResponse::HTTP_UNAUTHORIZED,
                'Username or password invalid'
            );
        $authenticationToken = $token->new($user, Token::AUTHENTICATION);
        return new JsonResponse(
            $authenticationToken->getValue(),
            JsonResponse::HTTP_OK
        );
    }
}