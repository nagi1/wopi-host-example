<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Sushi\Sushi;

class User extends Authenticatable
{
    use Sushi;

    protected $guarded = [];

    protected $schema = [
        'id' => 'integer',
        'name' => 'string',
        'email' => 'string',
        'info' => 'text',
        'password' => 'string',
        'created_at' => 'timestamp',
        'updated_at' => 'timestamp',
    ];

    protected $rows = [
        [
            'id' => 1,
            'name' => 'New York',
            'email' => 'admin@gmail.com',
            'password' => 'password',
            'info' => '',
        ],

    ];
}
