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
        private readonly PlayingService $playingService
    ) {

    }

    #[Route(
        "/api/save-playing-time",
        methods: ['POST']
    )]
    #[Tag('Playing')]
    public function savePlayingTime(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (!$data || !$data['purchasedGameId'] || !$data['time']){
            return $this->json(
                [
                    'message' => 'Missing data'
                ],
                Response::HTTP_BAD_REQUEST
            );
        }

        try {
            $this->playingService->savePlayingTime($data['purchasedGameId'], $data['time']);
        } catch (\Exception $e) {
            return $this->json(
                [
                    'message' => $e->getMessage()
                ],
                $e->getCode()
            );
        }

        return $this->json(
            [
                'message' => 'Successfully updated'
            ],
            Response::HTTP_OK
        );
    }
}
