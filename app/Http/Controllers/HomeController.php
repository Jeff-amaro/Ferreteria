<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;

class HomeController extends Controller
{
    public function __invoke()
    {
        $categories = Category::where('active', true)
            ->withCount(['products' => fn ($q) => $q->where('active', true)])
            ->orderBy('name')
            ->get();

        $featured = Product::with(['category', 'brand'])
            ->active()
            ->where('stock', '>', 0)
            ->latest()
            ->take(8)
            ->get();

        return view('home', compact('categories', 'featured'));
    }
}
