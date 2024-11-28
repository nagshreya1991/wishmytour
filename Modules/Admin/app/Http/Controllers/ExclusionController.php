<?php

namespace Modules\Admin\app\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Modules\Package\app\Models\Exclusion;

class ExclusionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        $exclusions = Exclusion::all();
        return view('backend.exclusion.index', compact('exclusions'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        return view('backend.exclusion.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        Exclusion::create($request->all());

        return redirect()->route('admin.exclusions.index')
            ->with('success', 'Exclusion created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show($id): View
    {
        $exclusion = Exclusion::findOrFail($id);
        return view('backend.exclusion.show', compact('exclusion'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id): View
    {
        $exclusion = Exclusion::findOrFail($id);
        return view('backend.exclusion.edit', compact('exclusion'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id): RedirectResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $exclusion = Exclusion::findOrFail($id);
        $exclusion->update($request->all());

        return redirect()->route('admin.exclusions.index')
            ->with('success', 'Exclusion updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id): RedirectResponse
    {
        $exclusion = Exclusion::findOrFail($id);
        $exclusion->delete();

        return redirect()->route('admin.exclusions.index')
            ->with('success', 'Exclusion deleted successfully.');
    }
}
