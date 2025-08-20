<?php

namespace App\Dto\Interfaces;

interface UserRequestInterface
{
    public function getUsername(): ?string;

    public function getFirstName(): ?string;

    public function getLastName(): ?string;

    public function getPlainPassword(): ?string;
}