<?php

namespace App\Http\Controllers;

use App\Events\NotificationCreated;
use App\Http\Controllers\API\ApiController;
use App\Http\Controllers\Tripay\TripayController;
use App\Models\Address;
use App\Models\Cart;
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

        $customer = Address::find(str_replace('address', '', request('address_id')));
        $product = Product::with('image')->find(request('product_id'));

        $delivery = explode('#', request('delivery'));

        $transaction = $tripay->RequestTransaction($customer, $product, $delivery);

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
                        'notif' => isset($notif) ? $notif->data : [],
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
        $transaction = Transaction::where('invoice', $invoice)->first();
        $payment = json_decode($tripay->getChannel($transaction->payment));

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
                    'notif' => isset($notif) ? $notif->data : [],
                    'cart' => Cart::where('user_id', auth()->user()->id)->get(),
                    'transaction' => $transaction,
                    'customer' => Address::find($transaction->customer_id),
                    'product' => Product::find($transaction->product_id),
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
                        'notif' => isset($notif) ? $notif->data : [],
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
        $transaction = Transaction::where('invoice', $invoice)->first();
        $product = Product::where('id', $transaction->product_id)->first();
        //dd($transaction->first());

        if ($transaction->user_id == auth()->user()->id) {
            if ($transaction->status == 'Dikirim') {
                $transaction->update(['status' => 'Selesai']);
                $product->update([
                    'product_stok' => $product->product_stok - $transaction->quantity,
                    'product_sold' => $product->product_sold + $transaction->quantity
                ]);

                $notif = [
                    'message' => 'MunnShop Order Accepted!',
                    'user_id' => $transaction->user_id,
                    'invoice' => $transaction->invoice,
                    'product' => $product->product_name,
                    'image' => url($product->image[0]->image),
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
