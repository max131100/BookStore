<?php

namespace App\Controller;

use App\Exception\BookCategoryNotFoundException;
use App\Service\BookService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Routing\Annotation\Route;
use App\Model\BookListResponse;
use OpenApi\Annotations as OA;
use Nelmio\ApiDocBundle\Annotation\Model;
use App\Model\ErrorResponse;
use App\Model\BookDetails;

class BookController extends AbstractController
{
    public function __construct(private BookService $service)
    {
    }

    /**
     * @OA\Response(
     *     response=200,
     *     description="Returns published books that belongs to chosen category",
     *     @Model(type=BookDetails::class)
     * )
     * @OA\Response(
     *     response=404,
     *     description="Book category not found",
     *     @Model(type=ErrorResponse::class)
     * )
     */
    #[Route(path: 'api/v1/category/{id}/books', methods: ['GET'])]
    public function booksByCategory(int $id): Response
    {
        return $this->json($this->service->getBooksByCategory($id));
    }

    /**
     * @OA\Response(
     *     response=200,
     *     description="Returns published book detail info",
     *     @Model(type=BookListResponse::class)
     * )
     * @OA\Response(
     *     response=404,
     *     description="Book not found",
     *     @Model(type=ErrorResponse::class)
     * )
     */
    #[Route(path: 'api/v1/book/{id}', methods: ['GET'])]
    public function booksById(int $id): Response
    {
        return $this->json($this->service->getBookById($id));
    }
}
