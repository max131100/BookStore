<?php

namespace App\Tests\Controller;

use App\Entity\Book;
use App\Tests\AbstractControllerTest;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;

class RecommendationControllerTest extends AbstractControllerTest
{

    public function testRecommendationsByBookId()
    {
        $bookId = $this->createBook();

        $this->em->flush();

        $this->client->request('GET', 'api/v1/book/123/recommendations');

        $responseContent = $this->client->getResponse()->getContent();

        $this->assertResponseIsSuccessful();
        $this->assertJsonDocumentMatchesSchema($responseContent, [
            'type' => 'object',
            'required' => ['items'],
            'properties' => [
                'items' => [
                    'type' => 'array',
                    'items' => [
                        'type' => 'object',
                        'required' => ['id', 'title', 'slug',  'image', 'shortDescription'],
                        'properties' => [
                            'id' => ['type' => 'integer'],
                            'title' => ['type' => 'string'],
                            'slug' => ['type' => 'string'],
                            'image' => ['type' => 'string'],
                            'shortDescription' => ['type' => 'string']
                        ]
                    ]
                ]
            ]
        ]);
    }

    private function createBook(): int
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
        $this->em->flush();

        return $book->getId();
    }
}
