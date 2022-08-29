<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Tripay\TripayController;
use App\Models\Address;
use App\Models\Cart;
use App\Models\Product;
use App\Models\Transaction;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    public function transaction()
    {
        $tripay = new TripayController();

        $customer = Address::find(str_replace('address', '', request('address_id')));
        $product = Product::find(request('product_id'));

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
                return view('transaction.payment')
                    ->with([
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

        if ($transaction->user_id == auth()->user()->id) {
            return view('transaction.transaction')
                ->with([
                    'cart' => Cart::where('user_id', auth()->user()->id)->get(),
                    'transaction' => $transaction,
                    'customer' => Address::find($transaction->customer_id),
                    'product' => Product::find($transaction->product_id),
                    'payment' => (isset($payment)) ? $payment->data[0] : []
                ]);
        } else {
            return abort(404);
        }
    }
}
