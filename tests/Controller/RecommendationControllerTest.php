<?php

namespace App\Tests\Controller;

use App\Tests\AbstractControllerTest;
use App\Tests\MockUtils;

class RecommendationControllerTest extends AbstractControllerTest
{

    public function testRecommendationsByBookId()
    {
        $user = MockUtils::createUser();
        $this->em->persist($user);

        $book = MockUtils::createBook()->setUser($user);
        $this->em->persist($book);
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
}
