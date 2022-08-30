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
        
        $est = 24 * explode('-', $transaction->delivery_estimation)[1];
        $estimation = date_create($transaction->created_at);
        date_add($estimation, date_interval_create_from_date_string($est.' hours'));

        if ($transaction->user_id == auth()->user()->id) {
            return view('transaction.transaction')
                ->with([
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
        date_add($estimation, date_interval_create_from_date_string($est.' hours'));

        if ($transaction->user_id == auth()->user()->id) {
            if ($transaction->status == 'Dikirim') {
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
        
                return view('transaction.delivery')
                ->with([
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
    
}
