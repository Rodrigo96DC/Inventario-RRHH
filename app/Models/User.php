<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Permission\Traits\HasRoles;
use App\Traits\TieneTrazabilidad;

class User extends Authenticatable
{
    use HasRoles, Notifiable, SoftDeletes, TieneTrazabilidad;

    protected $fillable = [
        'name', 'email', 'password',
        'avatar', 'estado', 'tema', 'color',
        'created_by', 'updated_by', 'deleted_by',
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password'          => 'hashed',
        ];
    }

    public function creadoPor()   { return $this->belongsTo(User::class, 'created_by'); }
    public function editadoPor()  { return $this->belongsTo(User::class, 'updated_by'); }
    public function eliminadoPor(){ return $this->belongsTo(User::class, 'deleted_by'); }
}