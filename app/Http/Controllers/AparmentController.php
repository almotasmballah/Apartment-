<?php

namespace App\Http\Controllers;

use App\Models\Aparment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AparmentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
   public function index()
{
    $user = Auth::user();

    if ($user->role === 'landlord') {
        $Aparments = $user->Aparment;
    }
    else {
        $Aparments = Aparment::all();
    }

    return response()->json([
        'success' => true,
        'message' => 'تم جلب الشقق بنجاح',
        'data' => $Aparments
    ], 200);
}
   public function filterByCity(Request $request) {
    $city = $request->query('city');
    $apartments = Aparment::where('city', 'like', "%$city%")->get(); 
    return response()->json($apartments);
}
    public function filterByPrice(Request $request) {
        $min = $request->query('min_price');
        $max = $request->query('max_price');
        $apartments = Aparment::whereBetween('price', [$min, $max])->get();
        return response()->json($apartments);
    }

    public function filterByFeatures(Request $request) {
        $search = $request->query('search');
        $apartments = Aparment::where('location', 'like', "%$search%")
                               ->orWhere('features', 'like', "%$search%")
                               ->get();
        return response()->json($apartments);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
public function store(Request $request)
{
    $validatedData = $request->validate([
        'city'     => 'required|string|max:255',
        'price'    => 'required|numeric',
        'location' => 'required|string',
        'features' => 'required|string',
        'image'    => 'nullable|string',
    ]);

    $validatedData['user_id'] = auth()->id();

    $product = Aparment::create($validatedData);

    return response()->json([
        'success' => true,
        'message' => 'تم إضافة الشقة بنجاح',
        'data'    => $product
    ], 201);
}

    /**
     * Display the specified resource.
     */
    public function show(Aparment $aparment)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Aparment $aparment)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $Aparment = Aparment::find($id);

        if (!$Aparment) {
            return response()->json([
                'success' => false,
                'message' => 'الشقة غير موجودة'
            ], 404);
        }

        if ((int)$Aparment->user_id !== (int)Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => 'غير مسموح لك بتعديل هذه الشقة'
            ], 403);
        }

        $validatedData = $request->validate([
            'city' => 'sometimes|required|string',
            'price' => 'sometimes|required|numeric',
            'location' => 'sometimes|required|string',
            'features' => 'sometimes|required|string',
            'image' => 'sometimes|nullable|string',
        ]);

        $Aparment->update($validatedData);

        return response()->json([
            'success' => true,
            'message' => 'تم تحديث بيانات الشقة بنجاح',
            'data' => $Aparment
        ], 200);
    }
    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $user_id = Auth::user()->id;

        $Aparment = Aparment::findOrFail($id);

        if ($Aparment->user_id != $user_id) {
            return response()->json(['message' => 'Unauthorized to delete this Aparment'], 403);
        }

        $Aparment->delete();

        return response()->json([
            'success' => true,
            'message' => 'تم حذف المنتج بنجاح'
        ], 200);
    }

}
