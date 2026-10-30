<?php

namespace App\Http\Controllers;

use App\Services\StockService;
use Barryvdh\DomPDF\Facade\Pdf;

class ReplenishmentController extends Controller
{
    public function __construct(private readonly StockService $stock) {}

    public function index()
    {
        $groups = $this->stock->replenishmentGroups();
        $totalUnits = $groups->flatMap(fn ($group) => $group['products'])->sum('suggested_quantity');

        return view('replenishment.index', compact('groups', 'totalUnits'));
    }

    public function pdf()
    {
        $groups = $this->stock->replenishmentGroups();
        $totalUnits = $groups->flatMap(fn ($group) => $group['products'])->sum('suggested_quantity');

        $pdf = Pdf::loadView('replenishment.pdf', compact('groups', 'totalUnits'))->setPaper('a4');

        return $pdf->download('lista-reposicion.pdf');
    }
}
