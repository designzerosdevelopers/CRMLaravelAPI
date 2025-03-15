<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Categories;
use Symfony\Component\HttpFoundation\Response;

class CategoriesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $cats = Categories::all();

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'data'    => $cats,
            ], Response::HTTP_OK);
        }
        return view('pages.controlpanel.categories.index', compact('cats'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Send a POST request with "cat_name" to create a new category.'
            ], Response::HTTP_OK);
        }
        return view('categories.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'cat_name' => ['required', 'max:255'],
        ]);

        $category = Categories::create($validated);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Category created successfully.',
                'data'    => $category
            ], Response::HTTP_CREATED);
        }
        return redirect()->route('categories.index')
            ->with('success', 'Category created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, string $id)
    {
        $category = Categories::find($id);

        if (!$category) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Category not found'
                ], Response::HTTP_NOT_FOUND);
            }
            return redirect()->route('categories.index')->withErrors('Category not found');
        }

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'data'    => $category
            ], Response::HTTP_OK);
        }
        return view('pages.controlpanel.categories.show', compact('category'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Request $request, string $id)
    {
        $cat = Categories::find($id);

        if (!$cat) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Category not found'
                ], Response::HTTP_NOT_FOUND);
            }
            return redirect()->route('categories.index')->withErrors('Category not found');
        }

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'data'    => $cat
            ], Response::HTTP_OK);
        }
        return view('pages.controlpanel.categories.edit', compact('cat'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $validated = $request->validate([
            'cat_name' => ['required', 'max:255'],
        ]);

        $category = Categories::find($id);

        if (!$category) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Category not found'
                ], Response::HTTP_NOT_FOUND);
            }
            return redirect()->route('categories.index')->withErrors('Category not found');
        }

        $category->update($validated);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Category updated successfully.',
                'data'    => $category
            ], Response::HTTP_OK);
        }
        return redirect()->route('categories.index')
            ->with('success', 'Category updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, string $id)
    {
        $category = Categories::find($id);

        if (!$category) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Category not found'
                ], Response::HTTP_NOT_FOUND);
            }
            return redirect()->route('categories.index')->withErrors('Category not found');
        }

        $category->delete();

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Category deleted successfully.'
            ], Response::HTTP_OK);
        }
        return redirect()->route('categories.index')
            ->with('success', 'Category deleted successfully.');
    }
}
