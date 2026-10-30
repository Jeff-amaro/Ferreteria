<?php

namespace App\Http\Controllers;

use App\Http\Requests\SupplierRequest;
use App\Models\Supplier;
use Illuminate\Http\Request;

class SupplierController extends Controller
{
    public function index(Request $request)
    {
        $suppliers = Supplier::withCount(['products', 'purchaseOrders'])
            ->when($request->filled('search'), function ($q) use ($request) {
                $term = $request->input('search');
                $q->where('business_name', 'like', "%{$term}%")
                    ->orWhere('ruc', 'like', "%{$term}%")
                    ->orWhere('contact_name', 'like', "%{$term}%");
            })
            ->when($request->filled('status'), fn ($q) => $q->where('active', $request->input('status') === 'active'))
            ->orderBy('business_name')
            ->paginate(12)
            ->withQueryString();

        return view('suppliers.index', compact('suppliers'));
    }

    public function create()
    {
        return view('suppliers.create');
    }

    public function store(SupplierRequest $request)
    {
        $data = $request->validated();
        $data['active'] = $request->boolean('active', true);

        Supplier::create($data);

        return redirect()->route('suppliers.index')->with('success', 'Proveedor registrado correctamente.');
    }

    public function show(Supplier $supplier)
    {
        $supplier->load(['products' => fn ($q) => $q->latest()->take(12), 'purchaseOrders' => fn ($q) => $q->latest()->take(10)]);

        return view('suppliers.show', compact('supplier'));
    }

    public function edit(Supplier $supplier)
    {
        return view('suppliers.edit', compact('supplier'));
    }

    public function update(SupplierRequest $request, Supplier $supplier)
    {
        $data = $request->validated();
        $data['active'] = $request->boolean('active');

        $supplier->update($data);

        return redirect()->route('suppliers.index')->with('success', 'Proveedor actualizado correctamente.');
    }

    public function destroy(Supplier $supplier)
    {
        $supplier->update(['active' => false]);
        $supplier->delete();

        return redirect()->route('suppliers.index')->with('success', 'Proveedor dado de baja. Su historial se conserva.');
    }
}
