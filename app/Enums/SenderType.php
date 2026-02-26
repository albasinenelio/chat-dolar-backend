<?php

namespace App\Enums;

enum SenderType: string
{
    case Visitor = 'visitor';
    case Admin   = 'admin';
}