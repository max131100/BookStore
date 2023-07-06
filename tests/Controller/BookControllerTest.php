<?php

namespace Controller;

use App\Controller\BookController;
use PHPUnit\Framework\TestCase;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class BookControllerTest extends WebTestCase
{

    public function testBooksByCategory()
    {
        $client = self::createClient();
        $client->request('GET', 'api/v1/category/7/books');
        $responseContent = $client->getResponse()->getContent();

        $this->assertResponseIsSuccessful();
        $this->assertJsonStringEqualsJsonFile(
            __DIR__ . '/responses/BookControllerTest_testCategories.json',
            $responseContent
        );
    }
}
