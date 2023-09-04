<?php

namespace App\Tests\Controller;

use App\Entity\Book;
use App\Entity\Review;
use App\Tests\AbstractControllerTest;
use App\Tests\MockUtils;
use DateTime;
use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;

class ReviewControllerTest extends AbstractControllerTest
{

    public function testReviews(): void
    {
        $user = MockUtils::createUser();
        $this->em->persist($user);

        $book = MockUtils::createBook()->setUser($user);
        $this->em->persist($book);
        $this->em->persist(MockUtils::createReview($book));

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
}
