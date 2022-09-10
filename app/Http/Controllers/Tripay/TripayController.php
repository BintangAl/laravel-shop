<?php

namespace App\Http\Controllers\Tripay;

use App\Events\NotificationCreated;
use App\Http\Controllers\API\ApiController;
use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\Notification;
use App\Models\Transaction;
use Illuminate\Http\Request;

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

    public function RequestTransaction($customer, $product, $delivery)
    {
        $apiKey       = config('services.tripay.api_key');
        $privateKey   = config('services.tripay.private_key');
        $merchantCode = config('services.tripay.merchant_id');
        $merchantRef  = "INV-" . time();
        $amount       = request('total');

        $data = [
            'method'         => explode('#', request('payment'))[0],
            'merchant_ref'   => $merchantRef,
            'amount'         => $amount,
            'customer_name'  => $customer->nama_penerima,
            'customer_email' => auth()->user()->email,
            'customer_phone' => $customer->no_tlp,
            'order_items'    => [
                [
                    'sku'         => $product->id,
                    'name'        => $product->product_name,
                    'price'       => $product->product_price,
                    'quantity'    => request('quantity'),
                    'product_url' => url('product/' . $product->id . '/' . strtolower(str_replace([' ', '/'], '_', $product->product_name))),
                    'image_url'   => $product->product_image,
                ],
                [
                    'sku'         => 'JNE-' . $delivery[0],
                    'name'        => $delivery[1],
                    'price'       => request('total_ongkir'),
                    'quantity'    => 1,
                ],
            ],
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
                $trasaction->product_id = $product->id;
                $trasaction->quantity = request('quantity');
                $trasaction->amount = $response->amount;
                $trasaction->payment = explode('#', request('payment'))[0];
                $trasaction->delivery_service = 'JNE ' . $delivery[0] . ' (' . $delivery[1] . ')';
                $trasaction->delivery_ongkir = request('total_ongkir');
                $trasaction->delivery_estimation = $delivery[3];
                $trasaction->status = $status;
                $trasaction->expire = date('Y-m-d H:i:s', $response->expired_time);
                $trasaction->save();

                $notif = [
                    'message' => 'MunnShop New Order!',
                    'user_id' => auth()->user()->id,
                    'invoice' => $merchantRef,
                    'product' => $product->product_name,
                    'image' => url($product->image[0]->image),
                    'from' => 'munnshop',
                    'to' => 'admin'
                ];

                $api = new ApiController();
                $api->Notification('create', $notif);
                NotificationCreated::dispatch(array_merge($notif, ['id' => $api->NotificationNextId()]));

                if (isset(request()->cart_id)) {
                    Cart::find(request('cart_id'))->delete();
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
