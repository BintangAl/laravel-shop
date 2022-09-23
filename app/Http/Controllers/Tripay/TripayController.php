<?php

namespace App\Http\Controllers\Tripay;

use App\Events\NotificationCreated;
use App\Http\Controllers\API\ApiController;
use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\Notification;
use App\Models\Transaction;
use App\Models\TransactionDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TripayController extends Controller
{
    public function getChannel($code)
    {
        $apiKey = config('services.tripay.api_key');

        $payload = ['code' => $code];

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_FRESH_CONNECT  => true,
            CURLOPT_URL            => config('services.tripay.endpoint') . 'merchant/payment-channel?' . http_build_query($payload),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HEADER         => false,
            CURLOPT_HTTPHEADER     => ['Authorization: Bearer ' . $apiKey],
            CURLOPT_FAILONERROR    => false,
            CURLOPT_IPRESOLVE      => CURL_IPRESOLVE_V4
        ));

        $response = curl_exec($curl);
        $error = curl_error($curl);

        curl_close($curl);

        return ($response) ?: $error;
    }

    public function RequestTransaction($customer, $cart, $product, $delivery)
    {
        $trasactionInfo = DB::select("SHOW TABLE STATUS LIKE 'transactions'");
        $trasactionNextID = $trasactionInfo[0]->Auto_increment;

        $apiKey       = config('services.tripay.api_key');
        $privateKey   = config('services.tripay.private_key');
        $merchantCode = config('services.tripay.merchant_id');
        $merchantRef  = "INV-" . time();
        $amount       = request('total');
        $delivery_service = [
            [
                'sku'         => 'JNE-' . $delivery[0],
                'name'        => $delivery[1],
                'price'       => request('total_ongkir'),
                'quantity'    => 1,
            ],
        ];

        $data = [
            'method'         => explode('#', request('payment'))[0],
            'merchant_ref'   => $merchantRef,
            'amount'         => $amount,
            'customer_name'  => $customer->nama_penerima,
            'customer_email' => auth()->user()->email,
            'customer_phone' => $customer->no_tlp,
            'order_items'    => array_merge($delivery_service, $product),
            // 'return_url'   => 'https://domainanda.com/redirect',
            'expired_time' => (time() + (24 * 60 * 60)), // 24 jam
            'signature'    => hash_hmac('sha256', $merchantCode . $merchantRef . $amount, $privateKey)
        ];

        $curl = curl_init();

        curl_setopt_array($curl, [
            CURLOPT_FRESH_CONNECT  => true,
            CURLOPT_URL            => config('services.tripay.endpoint') . 'transaction/create',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HEADER         => false,
            CURLOPT_HTTPHEADER     => ['Authorization: Bearer ' . $apiKey],
            CURLOPT_FAILONERROR    => false,
            CURLOPT_POST           => true,
            CURLOPT_POSTFIELDS     => http_build_query($data),
            CURLOPT_IPRESOLVE      => CURL_IPRESOLVE_V4
        ]);

        $response = curl_exec($curl);
        $error = curl_error($curl);

        curl_close($curl);

        if ($response) {
            if (json_decode($response)->success == true) {
                $response = json_decode(json: $response)->data;

                $status = $response->status;
                if ($status == "UNPAID") {
                    $status = "Belum Bayar";
                } elseif ($status == "PAID") {
                    $status = "Dikemas";
                } else {
                    $status = "Dibatalkan";
                }

                $trasaction = new Transaction();
                $trasaction->user_id = auth()->user()->id;
                $trasaction->customer_id = $customer->id;
                $trasaction->reference = $response->reference;
                $trasaction->invoice = $merchantRef;
                $trasaction->notes = request('notes');
                $trasaction->amount = $response->amount;
                $trasaction->payment = explode('#', request('payment'))[0];
                $trasaction->delivery_service = 'JNE ' . $delivery[0] . ' (' . $delivery[1] . ')';
                $trasaction->delivery_ongkir = request('total_ongkir');
                $trasaction->delivery_estimation = $delivery[3];
                $trasaction->status = $status;
                $trasaction->expire = date('Y-m-d H:i:s', $response->expired_time);
                $trasaction->save();

                foreach ($cart as $item) {
                    TransactionDetail::create([
                        'transaction_id' => $trasactionNextID,
                        'product_id' => $item->product_detail->id,
                        'product_size' => $item->product_size,
                        'product_color' => $item->product_color,
                        'price' => $item->price,
                        'quantity' => $item->quantity,
                        'total_price' =>  (int) $item->product_detail->product_price * $item->quantity,
                    ]);
                }

                $notif = [
                    'message' => 'MunnShop New Order!',
                    'user_id' => auth()->user()->id,
                    'invoice' => $merchantRef,
                    'product' => $cart[0]->product_detail->product_name,
                    'image' => url($cart[0]->product_detail->image[0]->image),
                    'from' => 'munnshop',
                    'to' => 'admin'
                ];

                $api = new ApiController();
                $api->Notification('create', $notif);
                NotificationCreated::dispatch(array_merge($notif, ['id' => $api->NotificationNextId()]));

                foreach ($cart as $item) {
                    Cart::find($item->id)->delete();
                }

                return $response;
            } else {
                return abort(429, message: "Oops! Something missing...");
            }
        } else {
            return abort(500);
        }
    }

    public function DetailTransaction($reference)
    {
        $apiKey = config('services.tripay.api_key');

        $payload = ['reference'    => $reference];

        $curl = curl_init();

        curl_setopt_array($curl, [
            CURLOPT_FRESH_CONNECT  => true,
            CURLOPT_URL            => config('services.tripay.endpoint') . 'transaction/detail?' . http_build_query($payload),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HEADER         => false,
            CURLOPT_HTTPHEADER     => ['Authorization: Bearer ' . $apiKey],
            CURLOPT_FAILONERROR    => false,
            CURLOPT_IPRESOLVE      => CURL_IPRESOLVE_V4
        ]);

        $response = curl_exec($curl);
        $error = curl_error($curl);

        curl_close($curl);

        return ($response) ?: $error;
    }
}
