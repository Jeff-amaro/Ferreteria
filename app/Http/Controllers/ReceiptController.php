<?php

namespace App\Http\Controllers;

use App\Models\Receipt;
use Barryvdh\DomPDF\Facade\Pdf;

class ReceiptController extends Controller
{
    public function show(Receipt $receipt)
    {
        $receipt->load(['salesOrder.client', 'salesOrder.items', 'payment.user']);

        return view('receipts.show', compact('receipt'));
    }

    public function pdf(Receipt $receipt)
    {
        $receipt->load(['salesOrder.client', 'salesOrder.items', 'payment.user']);

        $pdf = Pdf::loadView('receipts.pdf', compact('receipt'))->setPaper('a4');

        return $pdf->download('comprobante-'.$receipt->code.'.pdf');
    }
}
