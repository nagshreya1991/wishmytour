<?php

namespace Modules\Admin\app\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Modules\Admin\app\Models\Referral;

class ReferralController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        $referrals = Referral::all();
        return view('backend.referral.index', compact('referrals'));
       
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        return view('backend.referral.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'code' => 'required|string|max:255',
        ]);

        Referral::create($request->all());

        return redirect()->route('admin.referrals.index')
            ->with('success', 'Referral code created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show($id): View
    {
        $referral = Referral::findOrFail($id);
        return view('backend.referral.show', compact('referral'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id): View
    {
        $referral = Referral::findOrFail($id);
        return view('backend.referral.edit', compact('referral'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id): RedirectResponse
    {
        $request->validate([
            'code' => 'required|string|max:255',
        ]);

        $referral = Referral::findOrFail($id);
        $referral->update($request->all());

        return redirect()->route('admin.referrals.index')
            ->with('success', 'Referral updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id): RedirectResponse
    {
        $referral = Referral::findOrFail($id);
       
        $referral->delete();

        return redirect()->route('admin.referrals.index')
            ->with('success', 'Referral deleted successfully.');
    }
}
