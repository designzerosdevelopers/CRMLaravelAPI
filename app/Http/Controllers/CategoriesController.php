<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Categories;

class CategoriesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {

        $cats = Categories::all();

        return view('pages.controlpanel.categories.index', ['cats' => $cats]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('pages.controlpanel.categories.create');
    }
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'cat_name' => ['required', 'max:255'],

        ]);

        Categories::create([
            'cat_name' => $request->cat_name,
        ]);

        return redirect()->route('categories.index')->with('success', 'Category created successfully.');;
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $cat = Categories::where('id', $id)->first();

        return view('pages.controlpanel.categories.edit', ['cat' => $cat]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'cat_name' => ['required', 'max:255'],

        ]);

        Categories::find($id)->update([
            'cat_name' => $request->cat_name,
        ]);

        return redirect()->route('categories.index')->with('success', 'Category updated successfully.');;
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        Categories::find($id)->delete();

        return redirect()->route('categories.index')->with('success', 'Category deleted successfully.');;
    }
}
