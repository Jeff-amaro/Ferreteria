<?php

namespace App\Http\Controllers;

use App\Http\Requests\WholesaleEvaluationRequest;
use App\Models\WholesaleRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class WholesaleController extends Controller
{
    public function index(Request $request)
    {
        $requests = WholesaleRequest::with(['client', 'quotation', 'requester', 'evaluator'])
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->input('status')))
            ->latest()
            ->paginate(12)
            ->withQueryString();

        $pendingCount = WholesaleRequest::where('status', 'pendiente')->count();

        return view('wholesale.index', compact('requests', 'pendingCount'));
    }

    public function evaluate(WholesaleEvaluationRequest $request, WholesaleRequest $wholesaleRequest)
    {
        if ($wholesaleRequest->status !== 'pendiente') {
            return back()->with('error', 'Esta solicitud ya fue evaluada.');
        }

        $data = $request->validated();

        DB::transaction(function () use ($wholesaleRequest, $data, $request) {
            $wholesaleRequest->update([
                'status' => $data['status'],
                'approved_discount' => $data['status'] === 'aprobada' ? $data['approved_discount'] : null,
                'notes' => $data['notes'] ?? $wholesaleRequest->notes,
                'evaluated_by' => $request->user()->id,
                'evaluated_at' => now(),
            ]);

            if ($data['status'] === 'aprobada' && $wholesaleRequest->quotation) {
                $quotation = $wholesaleRequest->quotation;
                $extra = round((float) $quotation->subtotal * ((float) $data['approved_discount'] / 100), 2);
                $discount = min((float) $quotation->subtotal, (float) $quotation->discount + $extra);
                $taxable = round(max(0, (float) $quotation->subtotal - $discount), 2);
                $igv = round($taxable * 0.18, 2);
                $total = round($taxable + $igv + (float) $quotation->shipping, 2);

                $quotation->update([
                    'discount' => $discount,
                    'igv' => $igv,
                    'total' => $total,
                ]);
            }
        });

        return back()->with('success', 'Solicitud mayorista evaluada correctamente.');
    }
}
