<?php

namespace Modules\Admin\app\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Modules\Package\app\Models\Inclusion;

class InclusionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        $inclusions = Inclusion::all();
        return view('backend.inclusion.index', compact('inclusions'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        return view('backend.inclusion.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        Inclusion::create($request->all());

        return redirect()->route('admin.inclusions.index')
            ->with('success', 'Inclusion created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show($id): View
    {
        $inclusion = Inclusion::findOrFail($id);
        return view('backend.inclusion.show', compact('inclusion'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id): View
    {
        $inclusion = Inclusion::findOrFail($id);
        return view('backend.inclusion.edit', compact('inclusion'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id): RedirectResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $inclusion = Inclusion::findOrFail($id);
        $inclusion->update($request->all());

        return redirect()->route('admin.inclusions.index')
            ->with('success', 'Inclusion updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id): RedirectResponse
    {
        $inclusion = Inclusion::findOrFail($id);
        $inclusion->delete();

        return redirect()->route('admin.inclusions.index')
            ->with('success', 'Inclusion deleted successfully.');
    }
}
