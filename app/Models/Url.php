<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Url extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'original_url',
        'clicks',
        'last_clicked_at',
        'expires_at',
        'source',
        'user_id',
    ];

    protected $dates = [
        'last_clicked_at',
        'expires_at',
    ];

    protected $hidden = [
        'user_id',
        'source',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function scopeByCode($query, string $code)
    {
        return $query->where('code', $code);
    }
}
