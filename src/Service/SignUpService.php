<?php

namespace App\Service;

use App\Entity\User;
use App\Exception\UserAlreadyExistsException;
use App\Model\IdResponse;
use App\Model\SignUpRequest;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Security\Http\Authentication\AuthenticationSuccessHandler;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class SignUpService
{
        public function __construct(
            private UserPasswordHasherInterface $hasher,
            private UserRepository $userRepository,
            private EntityManagerInterface $em,
            private AuthenticationSuccessHandler $authenticationSuccessHandler
        )
        {
        }

        public function signUp(SignUpRequest $signUpRequest): Response
        {
            if ($this->userRepository->existsByEmail($signUpRequest->getEmail())) {
                throw new UserAlreadyExistsException();
            }

            $user = (new User())
                ->setRoles(['ROLE_USER'])
                ->setEmail($signUpRequest->getEmail())
                ->setFirstName($signUpRequest->getFirstName())
                ->setLastName($signUpRequest->getLastName());

            $user->setPassword($this->hasher->hashPassword($user, $signUpRequest->getPassword()));

            $this->em->persist($user);
            $this->em->flush();

            return $this->authenticationSuccessHandler->handleAuthenticationSuccess($user);
        }
}
