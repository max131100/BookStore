<?php

namespace App\Tests\Controller;

use App\Entity\Book;
use App\Entity\BookCategory;
use App\Entity\BookFormat;
use App\Entity\BookToBookFormat;
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

    public function testBookById(): void
    {
        $bookId = $this->createBook();

        $this->client->request('GET', 'api/v1/book/' . $bookId);

        $responseContent = $this->client->getResponse()->getContent();

        $this->assertResponseIsSuccessful();
        $this->assertJsonDocumentMatchesSchema($responseContent, [
            'type' => 'object',
            'required' => ['id', 'title', 'slug', 'image', 'authors', 'meap', 'publicationDate', 'rating', 'reviews',
                'categories', 'formats'],
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
                'rating' => ['type' => 'number'],
                'reviews' => ['type' => 'integer'],
                'categories' => [
                    'type' => 'array',
                    'items' => [
                        'type' => 'object',
                        'required' => ['id', 'title', 'slug'],
                        'properties' => [
                            'id' => ['type' => 'integer'],
                            'title' => ['type' => 'string'],
                            'slug' => ['type' => 'string']
                        ]
                    ]
                ],
                'formats' => [
                    'type' => 'array',
                    'items' => [
                        'type' => 'object',
                        'required' => ['id', 'title', 'description', 'comment', 'price', 'discountPercent'],
                        'properties' => [
                            'id' => ['type' => 'integer'],
                            'title' => ['type' => 'string'],
                            'description' => ['type' => 'string'],
                            'comment' => ['type' => 'string'],
                            'price' => ['type' => 'number'],
                            'discountPercent' => ['type' => 'integer']
                        ]
                    ]
                ]
            ],
        ]);
    }

    private function createCategory(): int
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
        ->setCategories(new ArrayCollection([$bookCategory]))
        ->setIsbn('123321')
        ->setDescription('Test description'));

        $this->em->flush();

        return $bookCategory->getId();
    }

    private function createBook(): int
    {
        $bookCategory = (new BookCategory())->setTitle('Devices')->setSlug('devices');
        $this->em->persist($bookCategory);

        $format = (new BookFormat())->setTitle('format')->setDescription('description-format')
            ->setComment(null);

        $this->em->persist($format);

        $book = (new Book())
            ->setTitle('Test book')
            ->setSlug('test-book')
            ->setImage('http://test-book.png')
            ->setPublicationDate(new DateTime())
            ->setAuthors(['author'])
            ->setMeap(false)
            ->setCategories(new ArrayCollection([$bookCategory]))
            ->setIsbn('123321')
            ->setDescription('Test description');

        $this->em->persist($book);

        $join = (new BookToBookFormat())->setPrice(123.55)
            ->setFormat($format)
            ->setBook($book)
            ->setDiscountPercent(5);

        $this->em->persist($join);
        $this->em->flush();

        return $book->getId();
    }
}
