<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Breed;
use App\Models\PetCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BreedController extends Controller
{
    public function index()
    {
        $breeds     = Breed::with('petCategory')->withCount('pets')->latest()->paginate(20);
        $categories = PetCategory::where('is_active', true)->get();

        return view('admin.breeds.index', compact('breeds', 'categories'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'pet_category_id' => 'required|exists:pet_categories,id',
            'name'            => 'required|string|max:100',
            'is_active'       => 'boolean',
        ]);

        DB::transaction(fn() => Breed::create($data));

        return back()->with('success', 'Breed added.');
    }

    public function update(Request $request, Breed $breed)
    {
        $data = $request->validate([
            'pet_category_id' => 'required|exists:pet_categories,id',
            'name'            => 'required|string|max:100',
            'is_active'       => 'boolean',
        ]);

        DB::transaction(fn() => $breed->update($data));

        return back()->with('success', 'Breed updated.');
    }

    public function destroy(Breed $breed)
    {
        if ($breed->pets()->exists()) {
            return back()->with('error', 'Cannot delete breed with existing pets.');
        }
        DB::transaction(fn() => $breed->delete());

        return back()->with('success', 'Breed deleted.');
    }
}