<?php

namespace App\Tests;

use App\Entity\User;
use App\Repository\TokenRepository;
use App\Service\Token;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * Class AuthenticationTest
 * @package App\Tests
 */
class AuthenticationTest extends TestCase
{
    /**
     * @throws \Exception
     */
    public function setUp()
    {
        parent::setUp();
    }

    /**
     *
     */
    public function login()
    {
        $client = static::createClient();
        $client->request(
            'GET',
            '/authentication/login'
        );
        $this->assertEquals(JsonResponse::HTTP_BAD_REQUEST, $client->getResponse()->getStatusCode());
        /*
         * username = user_ROLE_USER_1
         * password = user_1_password
         * base64 encode (username:password) => dXNlcl9ST0xFX1VTRVJfMTp1c2VyXzFfcGFzc3dvcmQ=
         */
        $client->request(
            'GET',
            '/authentication/login',
            [],
            [],
            [
                'Authorization: Basic dXNlcl9ST0xFX1VTRVJfMTp1c2VyXzFfcGFzc3dvcmQ='
            ]
        );
        $this->assertEquals(JsonResponse::HTTP_OK, $client->getResponse()->getStatusCode());
        $user = $this->em->getRepository(User::class)->find(1);
        $token = $this->em->getRepository(TokenRepository::class)->findOneBy([
            'user' => $user, 'type' => Token::AUTHENTICATION
        ]);
        $this->assertEquals($token->getValue(), $client->getResponse()->getContent());
    }
}