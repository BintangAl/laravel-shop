<?php

namespace App\Http\Controllers;

use App\Events\NotificationCreated;
use App\Http\Controllers\API\ApiController;
use App\Http\Controllers\Tripay\TripayController;
use App\Models\Cart;
use App\Models\Customer;
use App\Models\Notification;
use App\Models\Product;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TransactionController extends Controller
{
    public function transaction()
    {
        $tripay = new TripayController();

        $customer = Customer::find(str_replace('address', '', request('address_id')));
        $carts = [];
        foreach (request('cart') as $cart_id) {
            $carts[] = json_decode(Cart::with('productDetail')->find($cart_id));
        }

        $products = [];
        foreach ($carts as $cart) {
            $products[] = [
                'sku' => $cart->product_detail->sku,
                'name' => $cart->product_detail->product_name,
                'price' => $cart->price,
                'quantity' => $cart->quantity,
                'product_url' => url('product/' . $cart->product_detail->id . '/' . strtolower(str_replace([' ', '/'], '_', $cart->product_detail->product_name))),
                'image' => $cart->product_detail->image[0]->image,
            ];
        }

        $delivery = explode('#', request('delivery'));

        $transaction = $tripay->RequestTransaction($customer, $carts, $products, $delivery);

        return redirect(url('payment/' . $transaction->merchant_ref));
    }

    public function payment($invoice)
    {
        $tripay = new TripayController();
        $transaction = Transaction::where('invoice', $invoice)->first();
        $detail = json_decode($tripay->DetailTransaction($transaction->reference));
        $payment = json_decode($tripay->getChannel($transaction->payment));

        if ($transaction->user_id == auth()->user()->id) {
            if ($transaction->status == 'Belum Bayar') {
                $api = new ApiController();
                if (Auth::user()) {
                    $notif = json_decode($api->Notification('list', [
                        'user_id' => auth()->user()->id,
                        'to' => 'munnshop'
                    ]));
                }

                return view('transaction.payment')
                    ->with([
                        'title' => 'Transaction Payment',
                        'notif' => isset($notif) ? $notif->data : null,
                        'cart' => Cart::where('user_id', auth()->user()->id)->get(),
                        'data' => (isset($detail)) ? $detail->data : [],
                        'payment' => (isset($payment)) ? $payment->data[0] : []
                    ]);
            } else {
                return redirect(route('detail-transaction', [$transaction->invoice]));
            }
        } else {
            return abort(404);
        }
    }

    public function detailTransaction($invoice)
    {
        $tripay = new TripayController();
        $transaction = json_decode(Transaction::with('orders')->where('invoice', $invoice)->first());
        $payment = json_decode($tripay->getChannel($transaction->payment));
        $subtotal = 0;
        foreach ($transaction->orders as $total) {
            $subtotal += ($total->price * $total->quantity);
        }

        $est = 24 * explode('-', $transaction->delivery_estimation)[1];
        $estimation = date_create($transaction->created_at);
        date_add($estimation, date_interval_create_from_date_string($est . ' hours'));

        if ($transaction->user_id == auth()->user()->id) {
            $api = new ApiController();
            if (Auth::user()) {
                $notif = json_decode($api->Notification('list', [
                    'user_id' => auth()->user()->id,
                    'to' => 'munnshop'
                ]));
            }

            return view('transaction.transaction')
                ->with([
                    'title' => 'Transaction',
                    'notif' => isset($notif) ? $notif->data : null,
                    'cart' => Cart::where('user_id', auth()->user()->id)->get(),
                    'transaction' => $transaction,
                    'customer' => Customer::find($transaction->customer_id),
                    'products' => $transaction->orders,
                    'subtotal' => $subtotal,
                    'payment' => (isset($payment)) ? $payment->data[0] : [],
                    'estimation' => date_format($estimation, 'D, d M Y')
                ]);
        } else {
            return abort(404);
        }
    }

    public function delivery($invoice)
    {
        $tripay = new TripayController();
        $transaction = Transaction::where('invoice', $invoice)->first();

        $est = 24 * explode('-', $transaction->delivery_estimation)[1];
        $estimation = date_create($transaction->created_at);
        date_add($estimation, date_interval_create_from_date_string($est . ' hours'));

        if ($transaction->user_id == auth()->user()->id) {
            if ($transaction->status == 'Dikirim' || $transaction->status == 'Selesai') {
                $curl = curl_init();
                curl_setopt_array($curl, array(
                    CURLOPT_URL => 'https://api.binderbyte.com/v1/track?api_key=' . env("BINDER_BYTE_KEY") . '&courier=jnt&awb=' . $transaction->no_resi,
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_ENCODING => '',
                    CURLOPT_MAXREDIRS => 10,
                    CURLOPT_TIMEOUT => 0,
                    CURLOPT_FOLLOWLOCATION => true,
                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                    CURLOPT_CUSTOMREQUEST => 'GET',
                ));

                $response = curl_exec($curl);
                curl_close($curl);
                $response = json_decode($response);

                $api = new ApiController();
                if (Auth::user()) {
                    $notif = json_decode($api->Notification('list', [
                        'user_id' => auth()->user()->id,
                        'to' => 'munnshop'
                    ]));
                }

                return view('transaction.delivery')
                    ->with([
                        'title' => 'Transaction Delivery',
                        'notif' => isset($notif) ? $notif->data : null,
                        'cart' => Cart::where('user_id', auth()->user()->id)->get(),
                        'transaction' => $transaction,
                        'tracking' => ($response->status == 200) ? $response->data->history : [],
                        'estimation' => date_format($estimation, 'D, d M Y')
                    ]);
            } else {
                return redirect(route('detail-transaction', [$transaction->invoice]));
            }
        } else {
            return abort(404);
        }
    }

    public function cancel($invoice)
    {
        $transaction = Transaction::where('invoice', $invoice)->first();
        //dd($transaction->first());

        if ($transaction->user_id == auth()->user()->id) {
            if ($transaction->status == 'Belum Bayar') {
                $transaction->update(['status' => 'Dibatalkan']);
                return redirect(route('purchase', ['cancel']));
            } else {
                return redirect(route('detail-transaction', [$transaction->invoice]));
            }
        } else {
            return abort(404);
        }
    }

    public function done($invoice)
    {
        $transaction = Transaction::with('orders')->where('invoice', $invoice)->first();

        if ($transaction->user_id == auth()->user()->id) {
            if ($transaction->status == 'Dikirim') {
                $transaction->update(['status' => 'Selesai']);
                $transaction = json_decode(json_encode($transaction));
                foreach ($transaction->orders as $order) {
                    Product::where('id', $order->product_detail->id)->first()->update([
                        'product_stok' => $order->product_detail->product_stok - $order->quantity,
                        'product_sold' => $order->product_detail->product_sold + $order->quantity,
                    ]);
                }

                $notif = [
                    'message' => 'MunnShop Order Accepted!',
                    'user_id' => $transaction->user_id,
                    'invoice' => $transaction->invoice,
                    'product' => $transaction->orders[0]->product_detail->product_name,
                    'image' => url($transaction->orders[0]->product_detail->image[0]->image),
                    'from' => 'munnshop',
                    'to' => 'admin'
                ];

                $api = new ApiController();
                $api->Notification('create', $notif);
                NotificationCreated::dispatch(array_merge($notif, ['id' => $api->NotificationNextId()]));

                return redirect(route('purchase', ['done']));
            } else {
                return redirect(route('detail-transaction', [$transaction->invoice]));
            }
        } else {
            return abort(404);
        }
    }
}
