<?php

namespace App\Http\Controllers;

use App\Http\Requests\InventoryCountRequest;
use App\Models\InventoryCount;
use App\Models\Product;
use App\Services\InventoryService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class InventoryCountController extends Controller
{
    public function __construct(private readonly InventoryService $inventory) {}

    public function index(Request $request)
    {
        $counts = InventoryCount::with('user')
            ->withCount(['items', 'items as differences_count' => fn ($q) => $q->where('balanced', false)])
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->input('status')))
            ->latest()
            ->paginate(12)
            ->withQueryString();

        return view('inventory.index', compact('counts'));
    }

    public function create()
    {
        $products = Product::with(['category', 'brand'])
            ->active()
            ->orderBy('name')
            ->get();

        return view('inventory.create', compact('products'));
    }

    public function store(InventoryCountRequest $request)
    {
        $count = $this->inventory->completeCount($request->validated(), $request->user());

        return redirect()
            ->route('inventory.show', $count)
            ->with('success', 'Conteo físico '.$count->code.' registrado.'.($count->has_differences ? ' Se generó una auditoría por desbalance.' : ' Sin diferencias.'));
    }

    public function show(InventoryCount $inventoryCount)
    {
        $inventoryCount->load(['user', 'items.product', 'audits.requester', 'audits.resolver']);

        return view('inventory.show', compact('inventoryCount'));
    }

    public function pdf(InventoryCount $inventoryCount)
    {
        $inventoryCount->load(['user', 'items.product']);

        $pdf = Pdf::loadView('inventory.pdf', compact('inventoryCount'))->setPaper('a4');

        return $pdf->download('conteo-'.$inventoryCount->code.'.pdf');
    }
}
