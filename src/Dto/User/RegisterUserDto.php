<?php

namespace App\Dto\User;

use App\Dto\Interfaces\UserRequestInterface;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

#[UniqueEntity(
    fields: ['username'],
    entityClass: User::class,
    message: 'Ce nom d\'utilisateur est déjà utilisé'
)]
class RegisterUserDto implements UserRequestInterface
{
    public function __construct(
        #[Assert\NotBlank(message: 'Le nom d\'utilisateur est requis')]
        #[Assert\Length(
            min: 3,
            max: 180,
            minMessage: 'Le nom d\'utilisateur doit contenir au moins {{ limit }} caractères',
            maxMessage: 'Le nom d\'utilisateur ne peut pas dépasser {{ limit }} caractères'
        )]
        private readonly ?string $username = null,

        #[Assert\Length(
            max: 255,
            maxMessage: 'Le prénom ne peut pas dépasser {{ limit }} caractères'
        )]
        private readonly ?string $firstName = null,

        #[Assert\Length(
            max: 255,
            maxMessage: 'Le nom ne peut pas dépasser {{ limit }} caractères'
        )]
        private readonly ?string $lastName = null,

        #[Assert\NotBlank(message: 'Le mot de passe est requis')]
        #[Assert\Length(
            min: 6,
            max: 4096,
            minMessage: 'Le mot de passe doit contenir au moins {{ limit }} caractères',
            maxMessage: 'Le mot de passe ne peut pas dépasser {{ limit }} caractères'
        )]
        #[Assert\Regex(
            pattern: '/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{6,}$/',
            message: 'Le mot de passe doit contenir au moins une lettre majuscule, une lettre minuscule, un chiffre et un caractère spécial'
        )]
        private readonly ?string $plainPassword = null,

        #[Assert\NotBlank(message: 'La confirmation du mot de passe est requise')]
        #[Assert\EqualTo(
            propertyPath: 'plainPassword',
            message: 'La confirmation du mot de passe doit correspondre au mot de passe'
        )]
        private readonly ?string $confirmPassword = null,
    ) {
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function getPlainPassword(): ?string
    {
        return $this->plainPassword;
    }

    public function getConfirmPassword(): ?string
    {
        return $this->confirmPassword;
    }
}
