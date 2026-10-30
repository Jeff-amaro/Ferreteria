<?php

namespace App\Http\Controllers;

use App\Http\Requests\QuotationRequest;
use App\Models\Category;
use App\Models\Client;
use App\Models\Product;
use App\Models\Quotation;
use App\Services\CodeGeneratorService;
use App\Services\PricingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class PosController extends Controller
{
    public function __construct(
        private readonly PricingService $pricing,
        private readonly CodeGeneratorService $codes,
    ) {}

    public function index(Request $request)
    {
        $categories = Category::where('active', true)
            ->whereHas('products', fn ($q) => $q->where('active', true))
            ->orderBy('name')
            ->get();

        $products = Product::with(['category', 'brand'])
            ->active()
            ->orderBy('name')
            ->get();

        $clients = Client::active()->orderBy('name')->get();

        $selectedClientId = $request->integer('client_id') ?: null;

        return view('pos.index', compact('categories', 'products', 'clients', 'selectedClientId'));
    }

    public function store(QuotationRequest $request)
    {
        $data = $request->validated();

        $client = Client::findOrFail($data['client_id']);

        $products = Product::active()
            ->whereIn('id', collect($data['items'])->pluck('product_id'))
            ->get()
            ->keyBy('id');

        $lines = [];

        foreach ($data['items'] as $item) {
            $product = $products->get($item['product_id']);

            if (! $product) {
                throw ValidationException::withMessages([
                    'items' => 'Uno de los productos seleccionados ya no está disponible.',
                ]);
            }

            if ($product->stock < $item['quantity']) {
                throw ValidationException::withMessages([
                    'items' => "Stock insuficiente para {$product->name}. Disponible: {$product->stock}.",
                ]);
            }

            $lines[] = ['product' => $product, 'quantity' => (int) $item['quantity']];
        }

        $totals = $this->pricing->calculate(
            $lines,
            (float) ($data['discount'] ?? 0),
            (float) ($data['shipping'] ?? 0),
            $client->is_wholesale,
        );

        $quotation = DB::transaction(function () use ($data, $client, $totals) {
            $quotation = Quotation::create([
                'code' => $this->codes->next('COT', Quotation::class),
                'client_id' => $client->id,
                'user_id' => auth()->id(),
                'subtotal' => $totals['subtotal'],
                'discount' => $totals['discount'],
                'shipping' => $totals['shipping'],
                'igv' => $totals['igv'],
                'total' => $totals['total'],
                'status' => 'emitida',
                'valid_until' => $data['valid_until'] ?? now()->addDays(15)->toDateString(),
                'notes' => $data['notes'] ?? null,
            ]);

            foreach ($totals['items'] as $item) {
                $quotation->items()->create($item);
            }

            return $quotation;
        });

        return redirect()
            ->route('quotations.show', $quotation)
            ->with('success', 'Venta registrada. La cotización '.$quotation->code.' está lista para convertirse en orden.');
    }

    public function stock(Product $product)
    {
        return response()->json([
            'id' => $product->id,
            'name' => $product->name,
            'sku' => $product->sku,
            'price' => (float) $product->price,
            'stock' => $product->stock,
            'status' => $product->stock_status,
            'updated_at' => now()->toIso8601String(),
        ]);
    }
}
