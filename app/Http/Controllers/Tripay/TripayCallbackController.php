<?php

namespace App\Http\Controllers\Tripay;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use Illuminate\Http\Request;

class TripayCallbackController extends Controller
{
    // Isi dengan private key anda
    protected $privateKey = 'uPggL-Dd8ZG-CdpA9-Wc2xf-bAV2b';

    public function handle(Request $request)
    {
        $callbackSignature = $request->server('HTTP_X_CALLBACK_SIGNATURE');
        $json = $request->getContent();
        $signature = hash_hmac('sha256', $json, $this->privateKey);

        if ($signature !== (string) $callbackSignature) {
            return 'Invalid signature';
        }

        if ('payment_status' !== (string) $request->server('HTTP_X_CALLBACK_EVENT')) {
            return 'Invalid callback event, no action was taken';
        }

        $data = json_decode($json);
        $uniqueRef = $data->reference;
        $status = strtoupper((string) $data->status);

        /*
            |--------------------------------------------------------------------------
            | Proses callback untuk closed payment
            |--------------------------------------------------------------------------
            */
        if (1 === (int) $data->is_closed_payment) {
            $invoice = Transaction::where('reference', $uniqueRef)->first();

            if (!$invoice) {
                return 'No invoice found for this unique ref: ' . $uniqueRef;
            }

            $invoice->update(['status' => (($status == 'UNPAID') ? 'Belum Bayar' : (($status == 'PAID') ? 'Dikemas' : 'Gagal'))]);
            return response()->json(['success' => true]);
        }
    }
}
