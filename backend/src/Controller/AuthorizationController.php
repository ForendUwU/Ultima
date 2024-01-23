<?php

namespace App\Controller;

use App\Entity\User;
use OpenApi\Attributes\Tag;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

class AuthorizationController extends AbstractController
{
    #[Route(
        "/api/login",
        name: 'app_login',
        methods: ['POST']
    )]
    #[Tag('Authorization')]
    public function login(#[CurrentUser] ?User $user = null): JsonResponse
    {
        if (null === $user){
            return $this->json([
                'message' => 'missing credentials',
            ], 401);
        }
        return $this->json([
            $user->getId()
        ]);
    }

    /**
     * @throws \Exception
     */
    #[Route(
        "/api/logout",
        name: 'app_logout',
        methods: ['POST']
    )]
    #[Tag('Authorization')]
    public function logout(): void
    {
        throw new \Exception('Wrong endpoint');
    }
}