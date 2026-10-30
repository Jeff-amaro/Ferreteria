<?php

namespace App\Http\Controllers;

use App\Http\Requests\ClientRequest;
use App\Models\Client;
use Illuminate\Http\Request;

class ClientLookupController extends Controller
{
    public function search(Request $request)
    {
        $term = trim((string) $request->input('q'));

        $clients = Client::query()
            ->when($term !== '', function ($query) use ($term) {
                $query->where(function ($q) use ($term) {
                    $q->where('document_number', 'like', "%{$term}%")
                        ->orWhere('name', 'like', "%{$term}%")
                        ->orWhere('email', 'like', "%{$term}%");
                });
            })
            ->orderBy('name')
            ->limit(10)
            ->get();

        return response()->json($clients->map(fn (Client $client) => [
            'id' => $client->id,
            'name' => $client->name,
            'document' => $client->document_label,
            'document_type' => $client->document_type,
            'document_number' => $client->document_number,
            'type' => $client->type,
            'type_label' => $client->type_label,
            'email' => $client->email,
            'phone' => $client->phone,
            'address' => $client->address,
        ]));
    }

    public function quickStore(ClientRequest $request)
    {
        $data = $request->validated();
        $data['active'] = true;

        $client = Client::create($data);

        return redirect()
            ->route('pos.index', ['client_id' => $client->id])
            ->with('success', 'Cliente '.$client->name.' registrado y seleccionado.');
    }
}
