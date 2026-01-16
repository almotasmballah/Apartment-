<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Aparment extends Model
{
    protected $fillable = [
        'city',
        // 'description',
        'price',
        'location',
        'features',
        'user_id',
        'image'
    ];

    protected $casts = [
        'price' => 'decimal:2',
        // 'quantity' => 'integer'
    ];
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function reviews()
    {
        return $this->hasMany(Review::class);
    }
}

