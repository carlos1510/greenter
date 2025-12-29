<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Testing\Fluent\Concerns\Has;

class Company extends Model
{
    use HasFactory;

    protected $fillable = [
        'razon_social',
        'ruc',
        'direccion',
        'logo_path',
        'sol_user',
        'sol_pass',
        'cert_path',
        'cliente_id',
        'cliente_secret',
        'production',
        'user_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
