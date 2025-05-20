<?php

namespace App\Dto\Interfaces;

interface UserRequestInterface
{
    public function getEmail(): ?string;

    public function getFirstName(): ?string;

    public function getLastName(): ?string;

    public function getPlainPassword(): ?string;

    // public function getConfirmPassword(): ?string; n'est pas nécessaire de le mettre ici car il n'est pas utilisé dans l'interface
}