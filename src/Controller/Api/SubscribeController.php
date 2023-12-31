<?php

namespace App\Controller\Api;

use App\Attribute\RequestBody;
use App\Model\ErrorResponse;
use App\Model\SubscriberRequest;
use App\Service\SubscriberService;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Annotations as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SubscribeController extends AbstractController
{
    public function __construct(private SubscriberService $service)
    {
    }

    /**
     * @OA\Response(
     *     response=200,
     *     description="Subscribes email to news mailing list",
     * )
     * @OA\Response(
     *     response=400,
     *     description="Validation failed",
     *     @Model(type=ErrorResponse::class)
     * )
     * @OA\Response(
     *     response=409,
     *     description="Email already exists",
     *     @Model(type=ErrorResponse::class)
     * )
     * @OA\RequestBody(@Model(type=SubscriberRequest::class))
     */
    #[Route(path: 'api/v1/subscribe', methods: ['POST'])]
    public function subscribe(#[RequestBody] SubscriberRequest $subscriberRequest): Response
    {
        $this->service->subscribe($subscriberRequest);
        return $this->json(null);
    }
}
