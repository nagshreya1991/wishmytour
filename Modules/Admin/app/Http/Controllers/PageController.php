<?php

namespace Modules\Admin\app\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Modules\Admin\app\Models\Pages;

class PageController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        $pages = Pages::all();
        return view('backend.page.index', compact('pages'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        return view('backend.page.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'title' => 'required',
            'content' => 'required|string',  
        ]);

        Pages::create($request->all());

        return redirect()->route('admin.pages.index')
            ->with('success', 'Page created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show($id): View
    {
        $page = Pages::findOrFail($id);
        return view('backend.page.show', compact('page'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id): View
    {
        $page = Pages::findOrFail($id);
        return view('backend.page.edit', compact('page'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id): RedirectResponse
    {
        $request->validate([
            'title' => 'required',  
            'content' => 'required|string', 
        ]);

        $page = Pages::findOrFail($id);
        $page->update($request->all());

        return redirect()->route('admin.pages.index')
            ->with('success', 'Page updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id): RedirectResponse
    {
        $page = Pages::findOrFail($id);
       
        $page->delete();

        return redirect()->route('admin.pages.index')
            ->with('success', 'Page deleted successfully.');
    }
}
