<?php

namespace App\Http\Controllers;

use App\Http\Requests\PurchaseOrderRequest;
use App\Models\Product;
use App\Models\PurchaseOrder;
use App\Models\Supplier;
use App\Services\CodeGeneratorService;
use App\Services\InventoryService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PurchaseOrderController extends Controller
{
    public function __construct(
        private readonly CodeGeneratorService $codes,
        private readonly InventoryService $inventory,
    ) {}

    public function index(Request $request)
    {
        $orders = PurchaseOrder::with(['supplier', 'user'])
            ->withCount('items')
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->input('status')))
            ->when($request->filled('supplier'), fn ($q) => $q->where('supplier_id', $request->input('supplier')))
            ->when($request->filled('search'), function ($q) use ($request) {
                $term = $request->input('search');
                $q->where(function ($query) use ($term) {
                    $query->where('code', 'like', "%{$term}%")
                        ->orWhereHas('supplier', fn ($supplier) => $supplier->where('business_name', 'like', "%{$term}%"));
                });
            })
            ->latest()
            ->paginate(12)
            ->withQueryString();

        $suppliers = Supplier::orderBy('business_name')->get();

        return view('purchases.index', compact('orders', 'suppliers'));
    }

    public function create()
    {
        $suppliers = Supplier::where('active', true)->orderBy('business_name')->get();
        $products = Product::with('supplier')->active()->orderBy('name')->get();

        return view('purchases.create', compact('suppliers', 'products'));
    }

    public function store(PurchaseOrderRequest $request)
    {
        $data = $request->validated();

        $products = Product::whereIn('id', collect($data['items'])->pluck('product_id'))->get()->keyBy('id');
        $total = 0.0;
        $lines = [];

        foreach ($data['items'] as $item) {
            $product = $products->get($item['product_id']);

            if (! $product) {
                continue;
            }

            $subtotal = round((float) $item['unit_cost'] * (int) $item['quantity'], 2);
            $total += $subtotal;

            $lines[] = [
                'product_id' => $product->id,
                'product_name' => $product->name,
                'quantity' => (int) $item['quantity'],
                'unit_cost' => (float) $item['unit_cost'],
                'subtotal' => $subtotal,
            ];
        }

        $order = DB::transaction(function () use ($data, $lines, $total) {
            $order = PurchaseOrder::create([
                'code' => $this->codes->next('OC', PurchaseOrder::class),
                'supplier_id' => $data['supplier_id'],
                'user_id' => auth()->id(),
                'status' => 'pendiente',
                'ordered_at' => now()->toDateString(),
                'expected_at' => $data['expected_at'],
                'total' => round($total, 2),
                'notes' => $data['notes'] ?? null,
            ]);

            foreach ($lines as $line) {
                $order->items()->create($line);
            }

            return $order;
        });

        return redirect()->route('purchases.show', $order)
            ->with('success', 'Orden de compra '.$order->code.' registrada.');
    }

    public function show(PurchaseOrder $purchaseOrder)
    {
        $purchaseOrder->load(['supplier', 'user', 'items.product']);

        return view('purchases.show', compact('purchaseOrder'));
    }

    public function receive(Request $request, PurchaseOrder $purchaseOrder)
    {
        if ($purchaseOrder->status === 'recibida') {
            return back()->with('error', 'Esta orden de compra ya fue recibida.');
        }

        $data = $request->validate([
            'received_at' => ['nullable', 'date'],
            'notes' => ['nullable', 'string', 'max:500'],
        ]);

        $this->inventory->receivePurchase($purchaseOrder, $request->user(), $data);

        return redirect()->route('purchases.show', $purchaseOrder)
            ->with('success', 'Recepción verificada. El stock fue actualizado y la orden quedó recibida.');
    }
}
