<?php

namespace App\Tests\Service;

use App\Entity\User;
use App\Exception\UserAlreadyExistsException;
use App\Model\SignUpRequest;
use App\Repository\UserRepository;
use App\Service\SignUpService;
use App\Tests\AbstractTestCase;
use Doctrine\ORM\EntityManagerInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Security\Http\Authentication\AuthenticationSuccessHandler;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasher;

class SignUpServiceTest extends AbstractTestCase
{
    private UserRepository $userRepository;

    private EntityManagerInterface $em;

    private UserPasswordHasher $hasher;

    private AuthenticationSuccessHandler $successHandler;

    public function setUp(): void
    {
        parent::setUp();

        $this->userRepository = $this->createMock(UserRepository::class);
        $this->em = $this->createMock(EntityManagerInterface::class);
        $this->hasher = $this->createMock(UserPasswordHasher::class);
        $this->successHandler = $this->createMock(AuthenticationSuccessHandler::class);
    }

    private function createService(): SignUpService
    {
        return new SignUpService($this->hasher, $this->userRepository, $this->em, $this->successHandler);
    }

    public function testSignUpThrowsExceptionWhenUserAlreadyExists(): void
    {
        $this->expectException(UserAlreadyExistsException::class);

        $this->userRepository->expects($this->once())
            ->method('existsByEmail')
            ->with('test@test.com')
            ->willReturn(true);

        $this->createService()->signUp((new SignUpRequest())->setEmail('test@test.com'));
    }

    public function testSignUp(): void
    {
        $response = new Response();

        $this->userRepository->expects($this->once())
            ->method('existsByEmail')
            ->with('test@test.com')
            ->willReturn(false);

        $user = (new User())
            ->setRoles(['ROLE_USER'])
            ->setEmail('test@test.com')
            ->setLastName('test')
            ->setFirstName('test');

        $userWithHashedPassword = clone $user;
        $userWithHashedPassword->setPassword('hashed_password');

        $this->hasher->expects($this->once())
            ->method('hashPassword')
            ->with($user, '11111111')
            ->willReturn('hashed_password');

        $this->em->expects($this->once())
            ->method('persist')
            ->with($userWithHashedPassword);

        $this->em->expects($this->once())
            ->method('flush');

        $this->successHandler->expects($this->once())
            ->method('handleAuthenticationSuccess')
            ->with($userWithHashedPassword)
            ->willReturn($response);

        $signUpRequest = (new SignUpRequest())
            ->setFirstName('test')
            ->setLastName('test')
            ->setEmail('test@test.com')
            ->setPassword('11111111');

        $this->assertEquals($response, $this->createService()->signUp($signUpRequest));
    }
}
