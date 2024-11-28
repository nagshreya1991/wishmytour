<?php

namespace Modules\Admin\app\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Modules\Admin\app\Models\Configs;

class configController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        $configs = Configs::all();
        return view('backend.config.index', compact('configs'));
       
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        return view('backend.config.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        Configs::create($request->all());

        return redirect()->route('admin.config.index')
            ->with('success', 'config created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show($id): View
    {
        $config = Configs::findOrFail($id);
        return view('backend.config.show', compact('config'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id): View
    {
        $config = Configs::findOrFail($id);
        return view('backend.config.edit', compact('config'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id): RedirectResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $config = Configs::findOrFail($id);
        $config->update($request->all());

        return redirect()->route('admin.config.index')
            ->with('success', 'config updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id): RedirectResponse
    {
        $config = Configs::findOrFail($id);
       
        $config->delete();

        return redirect()->route('admin.config.index')
            ->with('success', 'config deleted successfully.');
    }
}
