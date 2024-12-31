<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'email',
        'city',
        'street',
        'mobile',
        'otp',
        'otp_verified',
        'payment_method',
        'password'

    ];
    
}
