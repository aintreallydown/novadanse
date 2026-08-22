<?php

namespace App\Dto;

use App\Entity\User;
use App\Entity\DossierAdhesion;

class AdhesionDto
{
    public ?User $user = null;
    public ?DossierAdhesion $dossierAdhesion = null;
}
