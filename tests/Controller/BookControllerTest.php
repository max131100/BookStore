<?php

namespace App\Tests\Controller;

use App\Entity\Book;
use App\Entity\BookCategory;
use App\Tests\AbstractControllerTest;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;

class BookControllerTest extends AbstractControllerTest
{
    public function testBooksByCategory()
    {
        $categoryId = $this->createCategory();
        $this->client->request('GET', 'api/v1/category/' . $categoryId . '/books');

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
                        'required' => ['id', 'title', 'slug', 'image', 'authors', 'meap', 'publicationDate'],
                        'properties' => [
                            'title' => ['type' => 'string'],
                            'slug' => ['type' => 'string'],
                            'id' => ['type' => 'integer'],
                            'publicationDate' => ['type' => 'integer'],
                            'image' => ['type' => 'string'],
                            'meap' => ['type' => 'boolean'],
                            'authors' => [
                                'type' => 'array',
                                'items' => ['type' => 'string'],
                            ],
                        ],
                    ],
                ],
            ],
            ]
        );
    }

    public function createCategory(): int
    {
        $bookCategory = (new BookCategory())->setTitle('Devices')->setSlug('devices');
        $this->em->persist($bookCategory);

        $this->em->persist((new Book())
        ->setTitle('Test book')
        ->setSlug('test-book')
        ->setImage('http://test-book.png')
        ->setPublicationDate(new DateTime())
        ->setAuthors(['author'])
        ->setMeap(false)
        ->setCategories(new ArrayCollection([$bookCategory])));

        $this->em->flush();

        return $bookCategory->getId();
    }
}
