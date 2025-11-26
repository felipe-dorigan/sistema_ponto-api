<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ApiLog extends Model
{
    protected $table = 'logs.api_logs';

    protected $fillable = [
        'level',
        'url',
        'method',
        'ip',
        'input',
        'exception',
        'message',
        'trace',
    ];

    protected $casts = [
        'input' => 'array',
    ];

    public $timestamps = true;
}
