<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Classification extends Model
{
    protected $fillable = [
        'user_id',
        'image',
        'category',
        'confidence',
        'recommendation'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}