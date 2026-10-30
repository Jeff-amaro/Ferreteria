<?php

namespace App\Http\Controllers;

use App\Http\Requests\AuditResolveRequest;
use App\Models\Audit;
use App\Services\InventoryService;
use Illuminate\Http\Request;

class AuditController extends Controller
{
    public function __construct(private readonly InventoryService $inventory) {}

    public function index(Request $request)
    {
        $audits = Audit::with(['inventoryCount', 'requester', 'resolver'])
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->input('status')))
            ->when($request->filled('search'), function ($q) use ($request) {
                $term = $request->input('search');
                $q->where(function ($query) use ($term) {
                    $query->where('code', 'like', "%{$term}%")
                        ->orWhere('reason', 'like', "%{$term}%");
                });
            })
            ->latest()
            ->paginate(12)
            ->withQueryString();

        $pendingCount = Audit::where('status', '!=', 'resuelta')->count();

        return view('audits.index', compact('audits', 'pendingCount'));
    }

    public function resolve(AuditResolveRequest $request, Audit $audit)
    {
        if ($audit->status === 'resuelta') {
            return back()->with('error', 'Esta auditoría ya fue resuelta.');
        }

        $this->inventory->resolveAudit($audit, $request->validated(), $request->user());

        return back()->with('success', $request->input('status') === 'resuelta'
            ? 'Auditoría resuelta y stock ajustado según el conteo físico.'
            : 'Auditoría marcada en proceso.');
    }
}
