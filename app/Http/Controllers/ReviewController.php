<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Review;
use App\Models\Booking;
use Illuminate\Support\Facades\Auth;

class ReviewController extends Controller
{
    // في ملف ReviewController.php
    public function store(Request $request)
    {
        
        $request->validate([
            'apartment_id' => 'required|exists:aparments,id',
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string'
        ]);

        $userId = Auth::id();
        $apartmentId = $request->apartment_id;

        
        $hasBooked = Booking::where('user_id', $userId)
            ->where('aparment_id', $apartmentId) 
            ->where('status', 'approved')
            ->exists();

        if (!$hasBooked) {
            return response()->json(['message' => 'عذراً، لا يمكنك تقييم شقة لم تقم بحجزها مسبقاً.'], 403);
        }

        
        $alreadyReviewed = Review::where('user_id', $userId)
            ->where('aparment_id', $apartmentId)
            ->exists();

        if ($alreadyReviewed) {
            return response()->json(['message' => 'لقد قمت بتقييم هذه الشقة مسبقاً.'], 400);
        }
        $review = Review::create([
            'user_id' => $userId,
            'aparment_id' => $apartmentId, 
            'rating' => $request->rating,
            'comment' => $request->comment,
        ]);

        return response()->json(['message' => 'تم إضافة التقييم بنجاح', 'data' => $review], 201);
    }
}
