<?php

namespace App\Controller;

use App\Service\PlayingService;
use App\Service\TokenService;
use OpenApi\Attributes\Tag;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;

#[AsController]
class PlayingController extends AbstractController
{
    public function __construct(
        private readonly PlayingService $playingService,
        private TokenService $tokenService
    ) {

    }

    #[Route(
        "/api/save-playing-time",
        methods: ['POST']
    )]
    #[Tag('Playing')]
    public function SavePlayingTime(Request $request)
    {
        $data = json_decode($request->getContent(), true);

        if (!$data || !$data['gameId'] || !$data['time']){
            return new JsonResponse(
                [
                    'message' => 'Missing data'
                ],
                Response::HTTP_BAD_REQUEST
            );
        }

        $token = $request->headers->get('Authorization');
        $decodedToken = $this->tokenService->decodeLongToken($token);

        try {
            $this->playingService->SavePlayingTime($data['gameId'], $decodedToken->login, $data['time']);
        } catch (\Exception $e) {
            return new JsonResponse(
                [
                    'message' => $e->getMessage()
                ],
                $e->getCode()
            );
        }

        return new JsonResponse(
            [
                'message' => 'successfully updated'
            ],
            Response::HTTP_OK
        );
    }
}