<?php

namespace App\Http\Controllers;

use App\Http\Requests\CampaignRequest;
use App\Models\CatalogCampaign;
use App\Models\Category;
use App\Models\Client;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class CampaignController extends Controller
{
    public function index()
    {
        $campaigns = CatalogCampaign::with(['user', 'category'])->latest()->paginate(10);
        $categories = Category::orderBy('name')->get();
        $recipients = Client::active()->whereNotNull('email')->where('email', '<>', '')->count();

        return view('campaigns.index', compact('campaigns', 'categories', 'recipients'));
    }

    public function store(CampaignRequest $request)
    {
        $data = $request->validated();

        $clients = Client::active()
            ->whereNotNull('email')
            ->where('email', '<>', '')
            ->get();

        foreach ($clients as $client) {
            try {
                Mail::raw($data['message'], function ($message) use ($client, $data) {
                    $message->to($client->email)->subject($data['subject']);
                });
            } catch (\Throwable $exception) {
                Log::warning('No se pudo enviar la campaña a '.$client->email.': '.$exception->getMessage());
            }
        }

        CatalogCampaign::create([
            'user_id' => $request->user()->id,
            'category_id' => $data['category_id'] ?? null,
            'subject' => $data['subject'],
            'message' => $data['message'],
            'recipients_count' => $clients->count(),
            'sent_at' => now(),
        ]);

        return redirect()->route('campaigns.index')
            ->with('success', 'Campaña enviada a '.$clients->count().' clientes con correo registrado.');
    }
}
