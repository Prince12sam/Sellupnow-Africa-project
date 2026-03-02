<?php

namespace App\Models\Backend;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class Admin extends Authenticatable
{
    use HasFactory, HasRoles, Notifiable;
    protected $fillable = [
        'name',
        'email',
        'image',
        'role',
        'password',
        'username',
        'email_verified',
        'about',
        'status'
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];
}
