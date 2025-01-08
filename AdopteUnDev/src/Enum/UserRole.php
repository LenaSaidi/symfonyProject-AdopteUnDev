<?php

namespace App\Enum;

enum UserRole: string
{
    case DEVELOPER = 'ROLE_DEV';
    case COMPANY = 'ROLE_COMPANY';
}
