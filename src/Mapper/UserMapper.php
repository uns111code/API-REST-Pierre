<?php

namespace App\Mapper;

use App\Dto\Interfaces\UserRequestInterface;
use App\Entity\User;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserMapper
{
    public function __construct(
        private readonly UserPasswordHasherInterface $passwordHasher,
    ) {
    }

    public function map(UserRequestInterface $dto, ?User $user = null): User
    {
        $user ??= new User;

        if (null !== $dto->getEmail()) {
            $user->setEmail(
                $dto->getEmail()
            );
        }

        if (null !== $dto->getFirstName()) {
            $user->setFirstName(
                $dto->getFirstName()
            );
        }

        if (null !== $dto->getLastName()) {
            $user->setLastName(
                $dto->getLastName()
            );
        }

        if (null !== $dto->getPlainPassword()) {
            $user->setPassword(
                $this->passwordHasher->hashPassword(
                    $user,
                    $dto->getPlainPassword()
                )
            );
        }

        return $user;
    }
}