<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;

class Admin extends Authenticatable
{
    protected $table = 'accounts';
    
    protected $fillable = [
        'email',
        'password',
    ];

    protected $hidden = [
        'password',
    ];
} 