<?php

namespace App\Dto;

use App\Entity\User;
use App\Entity\ClassesRegistration;

class RegistrationDto
{
    public ?User $user = null;

    /** @var ClassesRegistration[] */
    public array $classesRegistrations = [];
}
