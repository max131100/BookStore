<?php

namespace Controller;

use App\Entity\BookCategory;
use App\Tests\AbstractControllerTest;

class BookCategoryControllerTest extends AbstractControllerTest
{
    public function testCategories(): void
    {
        $this->em->persist((new BookCategory())->setTitle('Devices')->setSlug('devices'));
        $this->em->flush();

        $this->client->request('GET', 'api/v1/book/categories');
        $responseContent = $this->client->getResponse()->getContent();
        echo ($responseContent);

        $this->assertResponseIsSuccessful();
        $this->assertJsonDocumentMatchesSchema($responseContent, [
            'type' => 'object',
            'required' => ['items'],
            'properties' => [
                'items' => [
                    'type' => 'array',
                    'items' => [
                        'type' => 'object',
                        'required' => ['id', 'title', 'slug'],
                        'properties' => [
                            'title' => ['type' => 'string'],
                            'slug' => ['type' => 'string'],
                            'id' => ['type' => 'integer'],
                        ],
                    ],
                ],
            ],
        ]);
    }
}
