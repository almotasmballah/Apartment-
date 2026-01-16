<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Booking extends Model
{
    use HasFactory;

    // الحقول التي يسمح بتعبئتها (مهمة جداً لعملية الـ Store والـ Update)
    protected $fillable = [
        'user_id',
        'aparment_id',
        'start_date',
        'end_date',
        'status'
    ];

    // العلاقة مع الشقة (التي أضفتها أنت وهي صحيحة)
    public function aparment()
    {
        return $this->belongsTo(Aparment::class, 'aparment_id');
    }

    // إضافة العلاقة مع المستخدم (المستأجر)
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}