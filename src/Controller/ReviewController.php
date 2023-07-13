<?php

namespace App\Controller;

use App\Service\ReviewService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Routing\Annotation\Route;
use App\Model\BookListResponse;
use OpenApi\Annotations as OA;
use Nelmio\ApiDocBundle\Annotation\Model;
use App\Model\ErrorResponse;
use App\Model\ReviewPage;

class ReviewController extends AbstractController
{
    public function __construct(private ReviewService $service)
    {
    }

    /**
     * @OA\Parameter (name="page", in="query", description="Page number", @OA\Schema (type="integer"))
     * @OA\Response(
     *     response=200,
     *     description="Returns reviews page for selected book",
     *     @Model(type=ReviewPage::class)
     * )
     */
    #[Route(path: 'api/v1/book/{id}/reviews', methods: ['GET'])]
    public function reviews(int $id, Request $request): Response
    {
        return $this->json($this->service->getReviewPageByBookId(
            $id, $request->query->get('page', 1)));
    }
}
