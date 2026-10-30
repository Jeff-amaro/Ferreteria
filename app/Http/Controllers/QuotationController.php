<?php

namespace App\Http\Controllers;

use App\Models\Quotation;
use App\Models\WholesaleRequest;
use App\Services\SalesService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class QuotationController extends Controller
{
    public function __construct(private readonly SalesService $sales) {}

    public function index(Request $request)
    {
        $quotations = Quotation::with(['client', 'user'])
            ->when($request->filled('search'), function ($q) use ($request) {
                $term = $request->input('search');
                $q->where(function ($query) use ($term) {
                    $query->where('code', 'like', "%{$term}%")
                        ->orWhereHas('client', fn ($client) => $client->where('name', 'like', "%{$term}%")
                            ->orWhere('document_number', 'like', "%{$term}%"));
                });
            })
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->input('status')))
            ->latest()
            ->paginate(12)
            ->withQueryString();

        return view('quotations.index', compact('quotations'));
    }

    public function show(Quotation $quotation)
    {
        $quotation->load([
            'client',
            'user',
            'items.product',
            'salesOrder',
            'wholesaleRequests.requester',
            'wholesaleRequests.evaluator',
        ]);

        return view('quotations.show', compact('quotation'));
    }

    public function emit(Quotation $quotation)
    {
        if ($quotation->status !== 'borrador') {
            return back()->with('error', 'Solo las cotizaciones en borrador pueden emitirse.');
        }

        $quotation->update([
            'status' => 'emitida',
            'valid_until' => $quotation->valid_until ?? now()->addDays(15)->toDateString(),
        ]);

        return back()->with('success', 'Cotización emitida correctamente.');
    }

    public function status(Request $request, Quotation $quotation)
    {
        $data = $request->validate([
            'status' => ['required', Rule::in(['aceptada', 'rechazada'])],
        ]);

        if (! in_array($quotation->status, ['emitida', 'borrador'], true)) {
            return back()->with('error', 'La cotización ya no puede cambiar de estado.');
        }

        $quotation->update(['status' => $data['status']]);

        return back()->with('success', 'Estado de la cotización actualizado.');
    }

    public function convert(Quotation $quotation)
    {
        if ($quotation->is_converted) {
            return back()->with('error', 'Esta cotización ya fue convertida en orden de venta.');
        }

        $order = $this->sales->createFromQuotation($quotation, request()->user());

        return redirect()
            ->route('orders.show', $order)
            ->with('success', 'Orden de venta '.$order->code.' generada. El stock fue reservado.');
    }

    public function wholesale(Request $request, Quotation $quotation)
    {
        $data = $request->validate([
            'requested_discount' => ['required', 'numeric', 'min:0', 'max:30'],
            'notes' => ['nullable', 'string', 'max:300'],
        ]);

        if ($quotation->wholesaleRequests()->where('status', 'pendiente')->exists()) {
            return back()->with('error', 'Ya existe una solicitud de evaluación pendiente para esta cotización.');
        }

        WholesaleRequest::create([
            'client_id' => $quotation->client_id,
            'quotation_id' => $quotation->id,
            'requested_by' => $request->user()->id,
            'status' => 'pendiente',
            'requested_discount' => $data['requested_discount'],
            'notes' => $data['notes'] ?? null,
        ]);

        return back()->with('success', 'Solicitud enviada al administrador para su evaluación.');
    }

    public function pdf(Quotation $quotation)
    {
        $quotation->load(['client', 'user', 'items.product']);

        $pdf = Pdf::loadView('quotations.pdf', compact('quotation'))
            ->setPaper('a4');

        return $pdf->download('cotizacion-'.$quotation->code.'.pdf');
    }
}
