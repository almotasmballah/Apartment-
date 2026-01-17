<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Booking extends Model
{
    use HasFactory;

    
    protected $fillable = [
        'user_id',
        'aparment_id',
        'start_date',
        'end_date',
        'status'
    ];

    
    public function aparment()
    {
        return $this->belongsTo(Aparment::class, 'aparment_id');
    }

    
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}