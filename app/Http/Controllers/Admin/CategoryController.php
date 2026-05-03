<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PetCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Breed;

class CategoryController extends Controller
{
    public function index()
{
    $categories = PetCategory::withCount('pets', 'breeds')
        ->latest()
        ->paginate(15);

    $breeds = Breed::with('petCategory')
        ->withCount('pets')
        ->latest()
        ->paginate(20);

    return view('admin.categories.index', compact('categories', 'breeds'));
}

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'      => 'required|string|max:100|unique:pet_categories,name',
            'icon'      => 'nullable|string|max:50',
            'is_active' => 'boolean',
        ]);

        DB::transaction(function () use ($data) {
            PetCategory::create([
                'name'      => $data['name'],
                'icon'      => $data['icon'] ?? 'bi-tag',
                'is_active' => $data['is_active'] ?? true,
            ]);
        });

        return back()->with('success', 'Category created successfully.');
    }

    public function update(Request $request, PetCategory $category)
    {
        $data = $request->validate([
            'name'      => "required|string|max:100|unique:pet_categories,name,{$category->id}",
            'icon'      => 'nullable|string|max:50',
            'is_active' => 'boolean',
        ]);

        DB::transaction(function () use ($category, $data) {
            $category->update($data);
        });

        return back()->with('success', 'Category updated.');
    }

    public function destroy(PetCategory $category)
    {
        if ($category->pets()->exists()) {
            return back()->with('error', 'Cannot delete category with existing pets.');
        }

        DB::transaction(function () use ($category) {
            $category->breeds()->delete();
            $category->delete();
        });

        return back()->with('success', 'Category deleted.');
    }
}