<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable([
    'address',
    'email',
    'phone',
    'instagram',
    'youtube',
    'facebook',
    'map_embed',
])]
class Contact extends Model {}
