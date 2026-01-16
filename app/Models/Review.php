<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Review extends Model
{
    use HasFactory;

    // 1. تحديد الحقول التي يسمح بتعبئتها (Mass Assignment)
    // هذا ضروري لكي يعمل كود Review::create في الـ Controller
    protected $fillable = [
        'user_id', 
        'aparment_id', 
        'rating', 
        'comment'
    ];

    // 2. علاقة التقييم بالمستأجر (الذي قام بالتقييم)
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // 3. علاقة التقييم بالشقة (التي تم تقييمها)
    public function aparment()
    {
        return $this->belongsTo(Aparment::class);
    }
}