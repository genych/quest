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
    #[Route(path: '/survey/{id}', methods: ['GET'])]
    public function getSurvey(
        int $id,
        SurveyService $service,
    ): JsonResponse {
        return $this->json($service->surveyToDto($id));
    }

//todo: docs
    #[Route(path: '/stats', methods: ['GET'])]
    public function getStats(
        SurveyService $service,
    ): JsonResponse {
        return $this->json($service->getAllStats());
    }

    /**
     * @example:
     *   POST http://localhost/survey
     *   Content-Type: application/json
     *
     *   {
     *     "surveyId": int,
     *     "answers": [{ "questionId": int, "value": int|string|null }, …]
     *   }
     *
     * @param Request $request
     * @param SerializerInterface $serializer
     * @param SurveyService $service
     * @return JsonResponse
     * @throws \Throwable
     */
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
