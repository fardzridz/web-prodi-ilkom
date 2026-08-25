<?php

namespace App\Models;

use Database\Factories\ContactFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
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
class Contact extends Model
{
    /** @use HasFactory<ContactFactory> */
    use HasFactory;
}
