<?php

namespace App\Tests\Controller;

use App\Controller\ReviewController;
use App\Entity\Book;
use App\Entity\Review;
use App\Tests\AbstractControllerTest;
use DateTime;
use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use PHPUnit\Framework\TestCase;

class ReviewControllerTest extends AbstractControllerTest
{

    public function testReviews(): void
    {
        $book = $this->createBook();
        $this->createReview($book);

        $this->em->flush();

        $this->client->request('GET', 'api/v1/book/' . $book->getId() . '/reviews');

        $responseContent = $this->client->getResponse()->getContent();

        $this->assertResponseIsSuccessful();
        $this->assertJsonDocumentMatchesSchema($responseContent, [
            'type' => 'object',
            'required' => ['items', 'rating', 'page', 'pages', 'perPage', 'total'],
            'properties' => [
                'rating' => ['type' => 'number'],
                'page' => ['type' => 'integer'],
                'pages' => ['type' => 'integer'],
                'perPage' => ['type' => 'integer'],
                'total' => ['type' => 'integer'],
                'items' => [
                    'type' => 'array',
                    'items' => [
                        'type' => 'object',
                        'required' => ['id', 'content', 'author',  'rating', 'createdAt'],
                        'properties' => [
                            'id' => ['type' => 'integer'],
                            'content' => ['type' => 'string'],
                            'author' => ['type' => 'string'],
                            'rating' => ['type' => 'integer'],
                            'createdAt' => ['type' => 'integer']
                        ]
                    ]
                ]
            ]
        ]);
    }

    private function createBook(): Book
    {
        $book = (new Book())
            ->setTitle('Test book')
            ->setSlug('test-book')
            ->setImage('http://test-book.png')
            ->setPublicationDate(new DateTime())
            ->setAuthors(['Tester'])
            ->setMeap(false)
            ->setCategories(new ArrayCollection([]))
            ->setIsbn('123321')
            ->setDescription('Test description');

        $this->em->persist($book);

        return $book;
    }

    private function createReview(Book $book)
    {
        $this->em->persist((new Review())
        ->setAuthor('tester')
        ->setContent('Test content')
        ->setRating(5)
        ->setCreatedAt(new DateTimeImmutable())
        ->setBook($book));
    }
}
