<?php declare(strict_types=1);

namespace App\Controller;

use App\Contract\SubmittedSurveyDto;
use App\Service\SurveyService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

class SurveyController extends AbstractController
{
    #[Route(path: 'survey/{id}', methods: ['GET'])]
    public function getSurvey(
        int $id,
        SurveyService $service,
    ): JsonResponse {
        return $this->json($service->surveyToDto($id));
    }

    #[Route(path: '/survey', methods: ['POST'])]
    public function submitSurvey(
        Request $request,
        SerializerInterface $serializer,
        SurveyService $service,
    ): JsonResponse {
        $body = $request->getContent();
        /** @var SubmittedSurveyDto $dto */
        $dto = $serializer->deserialize($body, SubmittedSurveyDto::class, 'json');

        $service->saveSubmittedSurvey($dto);

        return $this->json(null);
    }
}
