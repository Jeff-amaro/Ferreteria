<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;

class CatalogController extends Controller
{
    public function index(Request $request)
    {
        $categories = Category::where('active', true)->orderBy('name')->get();

        $products = Product::with(['category', 'brand'])
            ->active()
            ->search($request->input('search'))
            ->when($request->filled('category'), fn ($q) => $q->where('category_id', $request->input('category')))
            ->when($request->input('stock') === 'available', fn ($q) => $q->where('stock', '>', 0))
            ->when($request->input('sort') === 'price_asc', fn ($q) => $q->orderBy('price'))
            ->when($request->input('sort') === 'price_desc', fn ($q) => $q->orderByDesc('price'))
            ->when($request->input('sort') === 'name', fn ($q) => $q->orderBy('name'))
            ->when(! in_array($request->input('sort'), ['price_asc', 'price_desc', 'name'], true), fn ($q) => $q->latest())
            ->paginate(12)
            ->withQueryString();

        return view('catalog.index', compact('products', 'categories'));
    }

    public function show(Product $product)
    {
        abort_unless($product->active, 404);

        $product->load(['category', 'brand', 'supplier']);

        $related = Product::with(['category', 'brand'])
            ->active()
            ->where('category_id', $product->category_id)
            ->whereKeyNot($product->id)
            ->take(4)
            ->get();

        return view('catalog.show', compact('product', 'related'));
    }
}
