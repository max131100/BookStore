<?php

namespace ArgumentResolver;

use App\ArgumentResolver\RequestBodyArgumentResolver;
use App\Attribute\RequestBody;
use App\Exception\RequestBodyConvertException;
use App\Exception\ValidationException;
use App\Tests\AbstractTestCase;
use Composer\Semver\Constraint\Constraint;
use Exception;
use PHPUnit\Framework\TestCase;
use stdClass;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class RequestBodyArgumentResolverTest extends AbstractTestCase
{
    private SerializerInterface $serializer;

    private ValidatorInterface $validator;

    protected function setUp(): void
    {
        parent::setUp();

        $this->serializer = $this->createMock(SerializerInterface::class);
        $this->validator = $this->createMock(ValidatorInterface::class);
    }

    public function testResolverSupportsArgumentsOfRequestBodyType()
    {
        $meta = new ArgumentMetadata('some', '', false, false, null,
            attributes: [new RequestBody()]);

        $this->assertNotEmpty($this->createResolver()->resolve(new Request(), $meta));
    }

    public function testResolverNotSupportsArgumentsOfRequestBodyType()
    {
        $meta = new ArgumentMetadata('some', '', false, false, null);

        $this->assertEmpty($this->createResolver()->resolve(new Request(), $meta));
    }

    public function testResolverThrowsConvertExceptionWhenDeserializationFails(): void
    {
        $this->expectException(RequestBodyConvertException::class);

        $request = new Request(content: 'testing content');
        $meta = new ArgumentMetadata('some', stdClass::class, false, false, null,
            attributes: [new RequestBody()]);

        $this->serializer->expects($this->once())
            ->method('deserialize')
            ->with($request->getContent(), $meta->getType(), JsonEncoder::FORMAT)
            ->willThrowException(new Exception());

        $this->createResolver()->resolve($request, $meta);
    }

    public function testResolverThrowsValidationExceptionWhenValidationFails(): void
    {
        $this->expectException(ValidationException::class);

        $body = ['test' => true];
        $encodedBody = json_encode($body);

        $request = new Request(content: $encodedBody);
        $meta = new ArgumentMetadata('some', stdClass::class, false, false, null,
            attributes: [new RequestBody()]);

        $this->serializer->expects($this->once())
            ->method('deserialize')
            ->with($request->getContent(), $meta->getType(), JsonEncoder::FORMAT)
            ->willReturn($body);

        $this->validator->expects($this->once())
            ->method('validate')
            ->willReturn(new ConstraintViolationList([
                new ConstraintViolation('error', null, [], null, 'some', null)
            ]));

        $this->createResolver()->resolve($request, $meta);
    }

    public function testResolve(): void
    {
        $body = ['test' => true];
        $encodedBody = json_encode($body);

        $request = new Request(content: $encodedBody);
        $meta = new ArgumentMetadata('some', stdClass::class, false, false, null,
            attributes: [new RequestBody()]);

        $this->serializer->expects($this->once())
            ->method('deserialize')
            ->with($request->getContent(), $meta->getType(), JsonEncoder::FORMAT)
            ->willReturn($body);

        $this->validator->expects($this->once())
            ->method('validate')
            ->willReturn(new ConstraintViolationList([]));

        $actual = $this->createResolver()->resolve($request, $meta)[0];

        $this->assertEquals($body, $actual);
    }

    private function createResolver(): RequestBodyArgumentResolver
    {
        return new RequestBodyArgumentResolver($this->serializer, $this->validator);
    }
}
