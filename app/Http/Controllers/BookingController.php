<?php
namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Aparment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BookingController extends Controller
{

    public function store(Request $request)
    {
        $request->validate([
            'aparment_id' => 'required|exists:aparments,id',
            'start_date' => 'required|date|after_or_equal:today',
            'end_date' => 'required|date|after:start_date',
        ]);

        $apartmentId = $request->aparment_id;
        $start = $request->start_date;
        $end = $request->end_date;

        $isConflict = Booking::where('aparment_id', $apartmentId)
            ->where('status', 'approved')
            ->where(function ($query) use ($start, $end) {
                $query->whereBetween('start_date', [$start, $end])
                    ->orWhereBetween('end_date', [$start, $end])
                    ->orWhere(function ($q) use ($start, $end) {
                        $q->where('start_date', '<=', $start)
                            ->where('end_date', '>=', $end);
                    });
            })->exists();

        if ($isConflict) {
            return response()->json([
                'success' => false,
                'message' => 'عذراً، الشقة محجوزة بالفعل في هذه الفترة.'
            ], 422);
        }

        $booking = Booking::create([
            'user_id' => Auth::id(),
            'aparment_id' => $apartmentId,
            'start_date' => $start,
            'end_date' => $end,
            'status' => 'pending'
        ]);

        return response()->json([
            'success' => true,
            'message' => 'تم إرسال طلب الحجز، بانتظار موافقة صاحب الشقة.',
            'data' => $booking
        ], 201);
    }

    public function approveBooking($id)
    {
        $booking = Booking::findOrFail($id);
        $apartment = Aparment::findOrFail($booking->aparment_id);

        if (Auth::id() !== $apartment->user_id) {
            return response()->json(['message' => 'غير مصرح لك'], 403);
        }

        $booking->update(['status' => 'approved']);

        return response()->json(['message' => 'تم قبول الحجز بنجاح.']);
    }
    public function cancelBooking($id)
    {
        $booking = Booking::findOrFail($id);


        if (Auth::id() !== $booking->user_id) {
            return response()->json(['message' => 'غير مصرح لك بإلغاء هذا الحجز'], 403);
        }

        $booking->update(['status' => 'canceled']);

        return response()->json([
            'success' => true,
            'message' => 'تم إلغاء الحجز بنجاح'
        ]);
    }

    public function updateBooking(Request $request, $id)
    {
        $request->validate([
            'start_date' => 'required|date|after_or_equal:today',
            'end_date' => 'required|date|after:start_date',
        ]);

        $booking = Booking::findOrFail($id);

        if (Auth::id() !== $booking->user_id) {
            return response()->json(['message' => 'غير مصرح لك بتعديل هذا الحجز'], 403);
        }

        $overlap = Booking::where('aparment_id', $booking->aparment_id) // تصحيح الاسم هنا
            ->where('id', '!=', $id)
            ->where('status', 'approved')
            ->where(function ($query) use ($request) {
                $query->whereBetween('start_date', [$request->start_date, $request->end_date])
                    ->orWhereBetween('end_date', [$request->start_date, $request->end_date])
                    ->orWhere(function ($q) use ($request) {
                        $q->where('start_date', '<=', $request->start_date)
                            ->where('end_date', '>=', $request->end_date);
                    });
            })
            ->exists();

        if ($overlap) {
            return response()->json(['message' => 'عذراً، الشقة محجوزة في هذه التواريخ.'], 422);
        }

        $booking->update([
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'status' => 'pending'
        ]);

        return response()->json([
            'success' => true,
            'message' => 'تم تحديث بيانات الحجز، وهي بانتظار موافقة صاحب الشقة مجدداً'
        ]);
    }
    public function myBookings()
    {
        $bookings = Booking::where('user_id', Auth::id())
            ->with('aparment')
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'message' => 'تم جلب تاريخ الحجوزات بنجاح',
            'data' => $bookings
        ], 200);
    }
}
