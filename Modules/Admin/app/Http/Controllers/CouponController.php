<?php

namespace Modules\Admin\app\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Modules\Admin\app\Models\Coupons;
use Illuminate\Support\Facades\Log;
use Modules\User\app\Models\User;
use Modules\Booking\app\Models\Booking;

class CouponController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        $coupons = Coupons::all();
        return view('backend.coupon.index', compact('coupons'));
       
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $customers = User::where('user_type', 2)->with('customerDetail')->get();
        return view('backend.coupon.create', compact('customers'));
    }
    
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'code' => 'required|string|max:255',
            'user_ids' => 'nullable|string', // Ensure user_ids field validation
        ]);
    
        $couponData = $request->except(['user_ids']);
        $coupon = Coupons::create($couponData);
        
        // Save selected customer IDs as a comma-separated list
        $coupon->user_id = $request->input('user_ids');
        $coupon->save();
    
        return redirect()->route('admin.coupons.index')
            ->with('success', 'Coupon created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show($id): View
    {
        $coupon = Coupons::findOrFail($id);
        $customers = User::where('user_type', 2)->with('customerDetail')->get();
       // $totalUsed = Booking::where('coupon_id', $id)->count();
        $totalUsed = Booking::whereRaw('FIND_IN_SET(?, coupon_id)', [$id])->count();
        return view('backend.coupon.show', compact('coupon', 'totalUsed', 'customers'));
        
        
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $coupon = Coupons::findOrFail($id);
        // Count the number of times the coupon has been used
      
    
        // Get eligible customers
        $customers = Customer::all();
    
        return view('backend.coupons.edit', compact('coupon', 'customers'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $coupon = Coupons::findOrFail($id);
        
        // Validate the request data
        $request->validate([
            'customers' => 'array',
            'customers.*' => 'exists:users,id'
        ]);
        
        // Get the selected customer IDs
        $customerIds = $request->input('customers', []);
        
        // Convert the customer IDs to a comma-separated string
        $coupon->user_id = implode(',', $customerIds);
        
        // Save the coupon
        $coupon->save();
        
        return redirect()->route('admin.coupons.index')->with('success', 'Coupon updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id): RedirectResponse
    {
        $coupon = Coupons::findOrFail($id);
       
        $coupon->delete();

        return redirect()->route('admin.coupons.index')
            ->with('success', 'Coupon deleted successfully.');
    }
    public function toggleStatus(Request $request)
    {
        //Log::info('Toggle Status Request:', $request->all());

        $coupon = Coupons::find($request->id);
        if ($coupon) {
            $coupon->status = $request->status;
            $coupon->save();
            return response()->json(['message' => 'Coupon status updated successfully.']);
        }
        return response()->json(['message' => 'Coupon not found.'], 404);
    }
    public function toggleShowStatus(Request $request)
   {
    $request->validate([
        'id' => 'required|integer|exists:coupons,id',
        'show_status' => 'required|boolean',
    ]);

    $coupon = Coupons::findOrFail($request->id);
    $coupon->show_status = $request->show_status;
    $coupon->save();

    return response()->json(['message' => 'Show status updated successfully.']);
   }
   public function couponBookings($id)
   {
       $coupon = Coupons::findOrFail($id);
       $bookings = Booking::whereRaw('FIND_IN_SET(?, coupon_id)', [$id])
           ->leftJoin('booking_customer_details', 'bookings.id', '=', 'booking_customer_details.booking_id')
           ->select(
               'bookings.booking_number',
               'bookings.created_at',
               'booking_customer_details.name',
               'booking_customer_details.email',
               'booking_customer_details.phone_number'
           )
           ->get();
   
       return view('backend.coupon.bookings', compact('coupon', 'bookings'));
   }
}
