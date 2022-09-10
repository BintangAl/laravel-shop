<?php

namespace App\Http\Controllers\Tripay;

use App\Events\NotificationCreated;
use App\Http\Controllers\API\ApiController;
use App\Http\Controllers\Controller;
use App\Models\Notification;
use App\Models\Product;
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
            $product = Product::with('image')->find($invoice->product_id);

            if (!$invoice) {
                return 'No invoice found for this unique ref: ' . $uniqueRef;
            }

            $invoice->update(['status' => (($status == 'UNPAID') ? 'Belum Bayar' : (($status == 'PAID') ? 'Dikemas' : 'Gagal'))]);

            $notifAdmin = [
                'message' => 'MunnShop Payment ' . ($status == 'PAID' ? 'Success!' : 'Failed!'),
                'user_id' => $invoice->user_id,
                'invoice' => $invoice->invoice,
                'product' => $product->product_name,
                'image' => url($product->image[0]->image),
                'from' => 'munnshop',
                'to' => 'admin'
            ];

            $api = new ApiController();
            $api->Notification('create', $notifAdmin);
            NotificationCreated::dispatch(array_merge($notifAdmin, ['id' => $api->NotificationNextId()]));

            $notifStore = [
                'message' => 'Pembayaran ' . ($status == 'PAID' ? 'Berhasil!' : 'Gagal!'),
                'user_id' => $invoice->user_id,
                'invoice' => $invoice->invoice,
                'product' => $product->product_name,
                'image' => url($product->image[0]->image),
                'from' => 'munnshop',
                'to' => 'munnshop'
            ];

            $apiNotif = new ApiController();
            $apiNotif->Notification('create', $notifStore);
            NotificationCreated::dispatch(array_merge($notifStore, ['id' => $api->NotificationNextId()]));

            return response()->json(['success' => true]);
        }
    }
}
