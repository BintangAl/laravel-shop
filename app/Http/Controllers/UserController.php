<?php

namespace App\Http\Controllers;

use App\Http\Controllers\API\ApiController;
use App\Http\Controllers\Tripay\TripayController;
use App\Models\Cart;
use App\Models\Customer;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function profile()
    {
        $api = new ApiController();
        if (Auth::user()) {
            $notif = json_decode($api->Notification('list', [
                'user_id' => auth()->user()->id,
                'to' => 'munnshop'
            ]));
        }

        return view('profile')
            ->with([
                'title' => 'profile',
                'notif' => isset($notif) ? $notif->data : null,
                'cart' => Cart::where('user_id', auth()->user()->id)->get()
            ]);
    }

    public function profileUpdate(Request $request)
    {
        $user = User::find(auth()->user()->id);
        $user->name = $request->name;
        $user->save();

        return back();
    }

    public function address()
    {
        $api = new ApiController();
        if (Auth::user()) {
            $notif = json_decode($api->Notification('list', [
                'user_id' => auth()->user()->id,
                'to' => 'munnshop'
            ]));
        }

        return view('profile')
            ->with([
                'title' => 'address',
                'notif' => isset($notif) ? $notif->data : null,
                'cart' => Cart::where('user_id', auth()->user()->id)->get(),
                'address' => Customer::where('user_id', auth()->user()->id)->latest()->get(),
                'province' => json_decode(file_get_contents('asset/json/province.json'))->rajaongkir->results,
                'city' => json_decode(file_get_contents('asset/json/city.json'))->rajaongkir->results
            ]);
    }

    public function addAddress(Request $request)
    {
        $address = Customer::where('user_id', auth()->user()->id)->get();
        $request->validate(['*' => 'required']);

        Customer::create([
            'user_id' => auth()->user()->id,
            'status' => (count($address)) ? 'false' : 'true',
            'nama_penerima' => $request->nama_penerima,
            'no_tlp' => $request->no_tlp,
            'alamat' => $request->alamat,
            'provinsi' => $request->provinsi,
            'kota' => $request->kota,
            'kodepos' => $request->kodepos
        ]);

        if (isset($request->link)) {
            return redirect($request->link);
        } elseif (isset($request->cart)) {
            $link = '/checkout?';
            foreach (request('cart') as $key => $value) {
                $link .= 'cart%5B%5D=' . $value . '&';
            }
            return redirect(url($link));
        }

        return back();
    }

    public function mainAddress(Request $request)
    {
        Customer::where('user_id', auth()->user()->id)->update(['status' => 'false']);
        Customer::where('id', $request->main_address)->update(['status' => 'true']);

        return back();
    }

    public function changePassword(Request $request)
    {
        $request->validate([
            'old_password' => 'required',
            'password' => ['required', 'confirmed', Rules\Password::defaults()]
        ]);

        if (Hash::check($request->old_password, auth()->user()->password)) {
            $user = User::find(auth()->user()->id);
            $user->password = Hash::make($request->password);
            $user->save();

            return back()->with('successChangePassword', 'success');
        } else {
            return back()->with('checkFailed', 'The Old Password is not same');
        }
    }

    public function purchase($status)
    {
        $transaction = Transaction::with('orders')->where('user_id', auth()->user()->id)->latest()->get();

        if ($status != 'all') {
            $purchase_status = (($status == 'unpaid') ? 'Belum Bayar' : (($status == 'packed') ? 'Dikemas' : (($status == 'sent') ? 'Dikirim' : (($status == 'done') ? 'Selesai' : (($status == 'cancel') ? 'Dibatalkan' : 'Gagal')))));
            $transaction = Transaction::with('orders')->where([['user_id', '=', auth()->user()->id], ['status', '=', $purchase_status]])->latest()->get();
        }

        $api = new ApiController();
        if (Auth::user()) {
            $notif = json_decode($api->Notification('list', [
                'user_id' => auth()->user()->id,
                'to' => 'munnshop'
            ]));
        }

        // dd(json_decode($transaction)[0]->orders[0]->product_detail);

        return view('profile')
            ->with([
                'title' => 'purchase',
                'notif' => isset($notif) ? $notif->data : null,
                'status' => $status,
                'transaction' => json_decode($transaction),
                'cart' => Cart::where('user_id', auth()->user()->id)->get()
            ]);
    }
}
