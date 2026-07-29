<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable([
    'history',
    'description',
    'vision',
    'mission',
    'goals',
    'accreditation',
    'advantages',
])]
class ProgramProfile extends Model {}
