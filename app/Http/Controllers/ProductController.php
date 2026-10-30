<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProductRequest;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\SalesOrderItem;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::with(['category', 'brand', 'supplier']);

        if ($request->boolean('trashed')) {
            $query->onlyTrashed();
        }

        $query->search($request->input('search'))
            ->when($request->filled('category'), fn ($q) => $q->where('category_id', $request->integer('category')))
            ->when($request->filled('brand'), fn ($q) => $q->where('brand_id', $request->integer('brand')))
            ->when($request->filled('stock'), function ($q) use ($request) {
                if ($request->input('stock') === 'low') {
                    $q->lowStock();
                } elseif ($request->input('stock') === 'out') {
                    $q->where('stock', '<=', 0);
                }
            })
            ->when($request->filled('status'), fn ($q) => $q->where('active', $request->input('status') === 'active'));

        $products = $query->latest()->paginate(12)->withQueryString();

        return view('products.index', [
            'products' => $products,
            'categories' => Category::orderBy('name')->get(),
            'brands' => Brand::orderBy('name')->get(),
        ]);
    }

    public function create()
    {
        return view('products.create', $this->formData());
    }

    public function store(ProductRequest $request)
    {
        $data = $request->validated();
        $data['active'] = $request->boolean('active');
        $data['image_path'] = $this->storeImage($request);

        unset($data['image']);

        Product::create($data);

        return redirect()->route('products.index')->with('success', 'Producto registrado correctamente.');
    }

    public function show(Product $product)
    {
        $product->load(['category', 'brand', 'supplier']);

        $movements = SalesOrderItem::with('salesOrder')
            ->where('product_id', $product->id)
            ->latest()
            ->take(8)
            ->get();

        return view('products.show', compact('product', 'movements'));
    }

    public function edit(Product $product)
    {
        return view('products.edit', array_merge($this->formData(), ['product' => $product]));
    }

    public function update(ProductRequest $request, Product $product)
    {
        $data = $request->validated();
        $data['active'] = $request->boolean('active');

        if ($request->hasFile('image')) {
            if ($product->image_path) {
                Storage::disk('public')->delete($product->image_path);
            }
            $data['image_path'] = $request->file('image')->store('products', 'public');
        }

        unset($data['image']);

        $product->update($data);

        return redirect()->route('products.index')->with('success', 'Producto actualizado correctamente.');
    }

    public function destroy(Product $product)
    {
        $product->update(['active' => false]);
        $product->delete();

        return redirect()->route('products.index')->with('success', 'Producto dado de baja. Su historial se conserva.');
    }

    public function restore(int $product)
    {
        $product = Product::withTrashed()->findOrFail($product);
        $product->restore();
        $product->update(['active' => true]);

        return redirect()->route('products.index')->with('success', 'Producto restaurado correctamente.');
    }

    private function formData(): array
    {
        return [
            'categories' => Category::orderBy('name')->get(),
            'brands' => Brand::orderBy('name')->get(),
            'suppliers' => Supplier::orderBy('business_name')->get(),
        ];
    }

    private function storeImage(ProductRequest $request): ?string
    {
        return $request->hasFile('image')
            ? $request->file('image')->store('products', 'public')
            : null;
    }
}
