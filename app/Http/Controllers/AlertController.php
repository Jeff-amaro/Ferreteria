<?php

namespace App\Http\Controllers;

use App\Models\Alert;
use Illuminate\Http\Request;

class AlertController extends Controller
{
    public function index(Request $request)
    {
        $alerts = Alert::with('alertable')
            ->when($request->filled('type'), fn ($q) => $q->where('type', $request->input('type')))
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->input('status')))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        $pendingCount = Alert::pending()->count();

        return view('alerts.index', compact('alerts', 'pendingCount'));
    }

    public function read(Alert $alert)
    {
        $alert->update(['status' => 'leida']);

        return back()->with('success', 'Alerta marcada como leída.');
    }

    public function readAll()
    {
        $updated = Alert::pending()->update(['status' => 'leida']);

        return back()->with('success', $updated.' alertas marcadas como leídas.');
    }
}
