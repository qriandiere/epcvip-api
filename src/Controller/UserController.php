<?php

namespace App\Controller;

use App\Entity\User;
use App\Service\Serializer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/user", name="user_")
 */
class UserController extends AbstractController
{
    /**
     * @param User $user
     * @param Serializer $serializer
     * @return JsonResponse
     * @throws \Exception
     * @Route("/{id}", name="view")
     */
    public function view(
        User $user,
        Serializer $serializer
    )
    {
        $this->denyAccessUnlessGranted('view', $user);
        return new JsonResponse(
            $serializer->serialize($user, ['user']),
            JsonResponse::HTTP_OK
        )
    }
}