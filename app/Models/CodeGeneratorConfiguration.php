<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CodeGeneratorConfiguration extends Model
{
    use HasFactory;

    protected $fillable = [
        'max_length',
        'max_attempts',
        'max_retries',
    ];
}
