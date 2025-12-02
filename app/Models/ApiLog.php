<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ApiLog extends Model
{
    protected $fillable = [
        'platform',
        'endpoint',
        'request',
        'response',
        'status_code',
    ];

    protected $casts = [
        'request' => 'array',
        'response' => 'array',
        'status_code' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
}