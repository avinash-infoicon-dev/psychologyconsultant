<?php

namespace App\Http\Controllers\API\Payment;

use App\Http\Controllers\Controller;
use App\Http\Resources\InvoiceResource;
use App\Models\Invoice;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class InvoiceController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $invoices = $request->user()->invoices()->latest()->paginate(20);

        return response()->json([
            'data' => InvoiceResource::collection($invoices),
            'meta' => [
                'current_page' => $invoices->currentPage(),
                'last_page' => $invoices->lastPage(),
                'total' => $invoices->total(),
            ],
        ]);
    }

    public function show(Request $request, Invoice $invoice): JsonResponse
    {
        abort_unless($invoice->user_id === $request->user()->id, 403);

        return response()->json(['data' => new InvoiceResource($invoice)]);
    }

    public function download(Request $request, Invoice $invoice): StreamedResponse
    {
        abort_unless($invoice->user_id === $request->user()->id, 403);
        abort_unless($invoice->pdf_path !== null, 404, 'Invoice PDF has not been generated yet.');

        $disk = Storage::disk(config('payment.invoice.storage_disk', 'private'));

        return $disk->download($invoice->pdf_path, "{$invoice->invoice_number}.pdf");
    }
}
