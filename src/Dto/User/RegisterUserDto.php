<?php

namespace App\Dto\User;

use App\Dto\Interfaces\UserRequestInterface;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

#[UniqueEntity(
    fields: ['email'],
    entityClass: User::class,
    message: 'Cet email est déjà utilisé'
)]
class RegisterUserDto implements UserRequestInterface
{
    public function __construct(

        #[Assert\NotBlank(message: 'Email est requis')]
        #[Assert\Length(
            min: 3,
            max: 180,
            minMessage: 'Email doit contenir au moins {{ limit }} caractères',
            maxMessage: 'Email ne doit pas dépasser {{ limit }} caractères'
        )]
        private readonly ?string $email = null,
        #[Assert\Length(
            max: 255,
            maxMessage: 'Prénom ne doit pas dépasser {{ limit }} caractères'
        )]
        private readonly ?string $firstName = null,
        #[Assert\Length(
            max: 255,
            maxMessage: 'Nom ne doit pas dépasser {{ limit }} caractères'
        )]
        private readonly ?string $lastName = null,
        
        #[Assert\NotBlank(message: 'Mot de passe est requis')]
        #[Assert\Length(
            min: 6,
            max: 4096,
            minMessage: 'Mot de passe doit contenir au moins {{ limit }} caractères',
            maxMessage: 'Mot de passe ne doit pas dépasser {{ limit }} caractères'
        )]

        #[Assert\Regex(
            pattern: '/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{6,}$/',
            message: 'Le mot de passe doit contenir au moins une lettre majuscule, une lettre minuscule, un chiffre et un caractère spécial'
        )]
        private readonly ?string $plainPassword = null,
        #[Assert\NotBlank(message: 'Confirmation de mot de passe est requis')]
        #[Assert\EqualTo(
            propertyPath: 'plainPassword',
            message: 'La confirmation de mot de passe doit être identique au mot de passe'
        )]
        private readonly ?string $confirmPassword = null,
    ) {
    }




        /**
         * get the value of email
         */
        public function getEmail(): ?string
        {

                return $this->email;
        }


        /**
         * get the value of firstName
         */
        public function getFirstName(): ?string
        {

                return $this->firstName;
        }


        /**
         * get the value of lastName
         */
        public function getLastName(): ?string  
        {

                return $this->lastName;
        }


        /**
         * get the value of password
         */
        public function getPlainPassword(): ?string
        {

                return $this->plainPassword;
        }


        /**
         * get the value of confirmPassword
         */
        public function getConfirmPassword(): ?string
        {

                return $this->confirmPassword;
        }
}