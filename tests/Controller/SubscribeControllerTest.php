<?php

namespace Controller;


use App\Tests\AbstractControllerTest;
use Symfony\Component\HttpFoundation\Response;


class SubscribeControllerTest extends AbstractControllerTest
{

    public function testSubscribe(): void
    {
        $content = json_encode(['email' => 'test@test.com', 'agreed' => true]);
        $this->client->request('POST', 'api/v1/subscribe', content: $content);

        $this->assertResponseIsSuccessful();
    }

    public function testSubscriberNotAgreed(): void
    {
        $content = json_encode(['email' => 'test@test.com']);
        $this->client->request('POST', 'api/v1/subscribe', content: $content);

        $responseContent = $this->client->getResponse()->getContent();

        $this->assertResponseStatusCodeSame(Response::HTTP_BAD_REQUEST);
        $this->assertJsonDocumentMatches($responseContent, [
            '$.message' => 'Validation failed',
            '$.details.violations' => self::countOf(1),
            '$.details.violations[0].field' => 'agreed',
        ]);
    }
}
