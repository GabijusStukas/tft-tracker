<?php

namespace App\Http\Controllers;

use App\Models\Food;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class FoodController extends Controller
{
    /**
     * @param Request $request
     * @return Response
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'calories' => 'nullable|numeric|min:0',
            'protein' => 'nullable|numeric|min:0',
            'carbs' => 'nullable|numeric|min:0',
            'fat' => 'nullable|numeric|min:0',
            'image' => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('food-images', 'public');
            $validated['image'] = $path;
        }

        $food = Food::query()->create($validated);

        return Inertia::render('NewMeal', [
            'success' => 'Meal created successfully!',
            'food' => $food,
        ]);
    }

    /**
     * @param Food $food
     * @return BinaryFileResponse
     */
    public function show(Food $food)
    {
        if (!$food->image || !Storage::disk('public')->exists($food->image)) {
            abort(404);
        }

        return response()->file(Storage::disk('public')->path($food->image));
    }

}
