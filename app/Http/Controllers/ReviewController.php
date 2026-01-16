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
        // 1. التحقق من البيانات (تأكد من اسم الجدول 'aparments' كما في قاعدة بياناتك)
        $request->validate([
            'apartment_id' => 'required|exists:aparments,id',
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string'
        ]);

        $userId = Auth::id();
        $apartmentId = $request->apartment_id;

        // 2. التحقق من الحجز (يجب استخدام aparment_id بدون t كما في قاعدة البيانات)
        $hasBooked = Booking::where('user_id', $userId)
            ->where('aparment_id', $apartmentId) // تعديل الاسم هنا
            ->where('status', 'approved')
            ->exists();

        if (!$hasBooked) {
            return response()->json(['message' => 'عذراً، لا يمكنك تقييم شقة لم تقم بحجزها مسبقاً.'], 403);
        }

        // 3. منع التكرار
        $alreadyReviewed = Review::where('user_id', $userId)
            ->where('aparment_id', $apartmentId) // تعديل الاسم هنا
            ->exists();

        if ($alreadyReviewed) {
            return response()->json(['message' => 'لقد قمت بتقييم هذه الشقة مسبقاً.'], 400);
        }

        // 4. حفظ التقييم (استخدام الأسماء المطابقة للـ Fillable وقاعدة البيانات)
        $review = Review::create([
            'user_id' => $userId,
            'aparment_id' => $apartmentId, // تعديل الاسم هنا
            'rating' => $request->rating,
            'comment' => $request->comment,
        ]);

        return response()->json(['message' => 'تم إضافة التقييم بنجاح', 'data' => $review], 201);
    }
}
