<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'status',
        'last_login_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'last_login_at' => 'datetime',
    ];

    /** Shape the React pages expect (id like USR-001, lastLogin text). */
    public function toApi(): array
    {
        return [
            'id' => 'USR-' . str_pad((string) $this->id, 3, '0', STR_PAD_LEFT),
            'dbId' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'role' => $this->role,
            'status' => $this->status,
            'lastLogin' => $this->last_login_at
                ? $this->last_login_at->format('M j, Y - h:i A')
                : 'Never',
        ];
    }
}
