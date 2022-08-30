<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Tripay\TripayController;
use App\Models\Address;
use App\Models\Cart;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

class UserController extends Controller
{
    public function profile()
    {
        return view('profile')
            ->with([
                'title' => 'profile',
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
        return view('profile')
            ->with([
                'title' => 'address',
                'cart' => Cart::where('user_id', auth()->user()->id)->get(),
                'address' => Address::where('user_id', auth()->user()->id)->latest()->get(),
                'province' => json_decode(file_get_contents('asset/json/province.json'))->rajaongkir->results,
                'city' => json_decode(file_get_contents('asset/json/city.json'))->rajaongkir->results
            ]);
    }

    public function addAddress(Request $request)
    {
        $address = Address::where('user_id', auth()->user()->id)->get();

        $request->validate(['*' => 'required']);
        Address::create([
            'user_id' => auth()->user()->id,
            'status' => (count($address)) ? 'false' : 'true',
            'nama_penerima' => $request->nama_penerima,
            'no_tlp' => $request->no_tlp,
            'alamat' => $request->alamat,
            'provinsi' => $request->provinsi,
            'kota' => $request->kota,
            'kodepos' => $request->kodepos
        ]);

        return back();
    }

    public function mainAddress(Request $request)
    {
        Address::where('user_id', auth()->user()->id)->update(['status' => 'false']);
        Address::where('id', $request->main_address)->update(['status' => 'true']);

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
        $transaction = Transaction::where('user_id', auth()->user()->id)->get();

        if ($status != 'all') {
            $purchase_status = (($status == 'unpaid') ? 'Belum Bayar' : (($status == 'packed') ? 'Dikemas' : (($status == 'sent') ? 'Dikirim' : (($status == 'done') ? 'Selesai' : (($status == 'cancel') ? 'Dibatalkan' : 'Gagal')))));
            $transaction = Transaction::where([['user_id', '=', auth()->user()->id], ['status', '=', $purchase_status]])->latest()->get();
        }

        return view('profile')
            ->with([
                'title' => 'purchase',
                'status' => $status,
                'transaction' => $transaction,
                'cart' => Cart::where('user_id', auth()->user()->id)->get()
            ]);
    }
}
