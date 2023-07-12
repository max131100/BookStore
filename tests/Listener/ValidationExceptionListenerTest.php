<?php

namespace Listener;

use App\Exception\ValidationException;
use App\Listener\ValidationExceptionListener;
use App\Model\ErrorResponse;
use App\Model\ErrorValidationDetails;
use App\Tests\AbstractTestCase;
use Doctrine\DBAL\Exception;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\ConstraintViolationList;

class ValidationExceptionListenerTest extends AbstractTestCase
{
    private SerializerInterface $serializer;

    protected function setUp(): void
    {
        parent::setUp();

        $this->serializer = $this->createMock(SerializerInterface::class);
    }

    public function testInvokeSkippedWhenNotValidationException(): void
    {
        $this->serializer->expects($this->never())
            ->method('serialize');

        $event = $this->createExceptionEvent(new Exception());

        (new ValidationExceptionListener($this->serializer))($event);
    }

    public function testInvoke(): void
    {
        $serialized = json_encode([
            'message' => 'Validation failed',
            'details' => [
                'violations' => [
                    ['field' => 'name', 'message' => 'error']
                ]
            ]
        ]);

        $event = $this->createExceptionEvent(new ValidationException(new ConstraintViolationList([
            new ConstraintViolation('error', null, [], null, 'name', null)
        ])));

        $this->serializer->expects($this->once())
            ->method('serialize')
            ->with($this->callback(function (ErrorResponse $response) {
                /** @var ErrorValidationDetails|object $details */
                $details = $response->getDetails();

                if (!$details instanceof ErrorValidationDetails) {
                    return false;
                }

                $violations = $details->getViolations();

                if (count($violations) !== 1 || $response->getMessage() !== 'Validation failed') {
                    return false;
                }

                return $violations[0]->getField() === 'name' && $violations[0]->getMessage() === 'error';
            }),
            JsonEncoder::FORMAT)
            ->willReturn($serialized);

        (new ValidationExceptionListener($this->serializer))($event);

        $this->assertResponse(Response::HTTP_BAD_REQUEST, $serialized, $event->getResponse());
    }
}
