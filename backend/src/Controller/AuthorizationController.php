<?php

namespace App\Controller;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

class AuthorizationController extends AbstractController
{
    #[Route("/login", name: 'app_login', methods: ['POST'])]
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
    #[Route("/logout", name: 'app_logout', methods: ['POST'])]
    public function logout(): void
    {
        throw new \Exception('Wrong endpoint');
    }
}