<?php

namespace App\Http\Controllers;

use App\Http\Requests\ClientRequest;
use App\Models\Client;
use Illuminate\Http\Request;

class ClientController extends Controller
{
    public function index(Request $request)
    {
        $clients = Client::withCount(['salesOrders', 'quotations'])
            ->search($request->input('search'))
            ->when($request->filled('type'), fn ($q) => $q->where('type', $request->input('type')))
            ->when($request->filled('status'), fn ($q) => $q->where('active', $request->input('status') === 'active'))
            ->latest()
            ->paginate(12)
            ->withQueryString();

        return view('clients.index', compact('clients'));
    }

    public function create()
    {
        return view('clients.create');
    }

    public function store(ClientRequest $request)
    {
        $data = $request->validated();
        $data['active'] = $request->boolean('active', true);

        Client::create($data);

        return redirect()->route('clients.index')->with('success', 'Cliente registrado correctamente.');
    }

    public function show(Client $client)
    {
        $client->loadCount(['salesOrders', 'quotations']);
        $client->load(['salesOrders' => fn ($q) => $q->latest()->take(10), 'quotations' => fn ($q) => $q->latest()->take(10)]);

        return view('clients.show', compact('client'));
    }

    public function edit(Client $client)
    {
        return view('clients.edit', compact('client'));
    }

    public function update(ClientRequest $request, Client $client)
    {
        $data = $request->validated();
        $data['active'] = $request->boolean('active');

        $client->update($data);

        return redirect()->route('clients.index')->with('success', 'Cliente actualizado correctamente.');
    }

    public function destroy(Client $client)
    {
        $client->update(['active' => false]);
        $client->delete();

        return redirect()->route('clients.index')->with('success', 'Cliente dado de baja. Su historial se conserva.');
    }
}
