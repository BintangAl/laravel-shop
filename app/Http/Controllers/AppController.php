<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Tripay\TripayController;
use App\Models\Address;
use App\Models\Cart;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AppController extends Controller
{
    public function app()
    {
        return view('app')
            ->with([
                'category' => Category::all(),
                'recomendation' => Product::all(),
                'cart' => (Auth::check()) ? Cart::where('user_id', auth()->user()->id)->get() : []
            ]);
    }

    public function search(Request $request)
    {
        $output = '';
        if (isset($request->search)) {
            $product = Product::where('product_name', 'Like', '%' . $request->search . '%')->get();

            foreach ($product as $key => $value) {
                $link = "'" . route('product', [$value->id, strtolower(str_replace([' ', '/'], '_', $value->product_name))]) . "'";
                $name = strlen($value->product_name) > 30 ? substr($value->product_name, 0, 28) . "..." : $value->product_name;
                $output .= '
                <div class="col-lg-2 col-md-3 col-sm-4 col-6 mb-4">
                    <div class="product position-relative" onclick="window.location=' . $link . '">
                        <div class="box-product bg-white shadow-sm h-225 position-relative pointer">
                            <div class="product-image bg-primary h-150 w-100 bg-image" style="background-image: url(' . url($value->product_image) . ')"></div>
                            <div class="product-name fs-xsmall p-2 sans" id="product-name' . $value->id . '">' . $name . '</div>
                            <div class="product-price position-absolute bottom-0 start-0 ps-2 text-main mb-1">' . $value->product_price . '</div>
                        </div>
                        <div class="product-btn bg-main text-light fs-small text-center p-2 position-absolute w-100 pointer">BELI SEKARANG</div>
                    </div>
                </div>
                ';
            }
        }

        return response($output);
    }

    public function category($id, $category)
    {
        return view('category')
            ->with([
                'category' => Category::where('id', $id)->first()->category,
                'product' => Category::where('id', $id)->first()->product,
                'cart' => (Auth::check()) ? Cart::where('user_id', auth()->user()->id)->get() : []
            ]);
    }

    public function product($id, $product)
    {
        return view('product')
            ->with([
                'product' => Product::where('id', $id)->first(),
                'cart' => (Auth::check()) ? Cart::where('user_id', auth()->user()->id)->get() : []
            ]);
    }

    public function cart()
    {
        return view('cart')
            ->with([
                'cart' => Cart::where('user_id', auth()->user()->id)->get()
            ]);
    }

    public function addCart(Request $request)
    {
        $user_id = auth()->user()->id;
        $product = Product::where('id', $request->product_id)->first();
        $cart = Cart::where([['user_id', '=', $user_id], ['product_id', '=', $product->id]])->get();

        if (count($cart)) {
            $cart[0]->product_quantity = $cart[0]->product_quantity + $request->quantity;
            $cart[0]->save();
        } else {
            Cart::create([
                'user_id' => $user_id,
                'product_id' => $product->id,
                'product_name' => $product->product_name,
                'product_image' => $product->product_image,
                'product_price' => $product->product_price,
                'product_quantity' => $request->quantity,
            ]);
        }

        return response('added');
    }

    public function updateQuantity(Request $request)
    {
        $user_id = auth()->user()->id;
        $product = Cart::where([['user_id', '=', $user_id], ['product_id', '=', $request->product_id]])->get();

        if (count($product)) {
            if ($request->action == 'min') {
                if ($request->quantity != 0) {
                    $product[0]->product_quantity = $product[0]->product_quantity - 1;
                    $product[0]->save();
                }
            } elseif ($request->action == 'add') {
                $product[0]->product_quantity = $product[0]->product_quantity + 1;
                $product[0]->save();
            }
        }

        return response('updated');
    }

    public function deleteCart(Request $request)
    {
        Cart::where([['user_id', '=', auth()->user()->id], ['product_id', '=', $request->product_id]])->delete();
        return response('deleted');
    }

    public function checkout(Request $request, $id, $product, $cart_id)
    {
        $address = Address::where([['user_id', '=', auth()->user()->id], ['status', '=', 'true']])->first();
        $delivery = [];

        if ($address) {
            $curl = curl_init();

            curl_setopt_array($curl, array(
                CURLOPT_URL => 'https://api.rajaongkir.com/starter/cost',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POSTFIELDS => 'origin=178&destination=' . explode('#', $address->kota)[1] . '&weight=1&courier=jne',
                CURLOPT_HTTPHEADER => array(
                    'key: 5db1d89e70c6f05ca4a6c0f178c81f2c',
                    'Content-Type: application/x-www-form-urlencoded'
                ),
            ));

            $response = curl_exec($curl);
            curl_close($curl);

            if ($response) {
                if (json_decode($response)->rajaongkir->status->code == 200) {
                    $delivery = json_decode($response)->rajaongkir->results;
                }
            }
        }

        $tripay = new TripayController();
        $channel = $tripay->getChannel('');
        if (json_decode($channel)) {
            if (json_decode($channel)->success) {
                $channel = json_decode($channel)->data;
            } else {
                $channel = [];
            }
        } else {
            $channel = [];
        }

        return view('checkout')
            ->with([
                'cart_id' => $cart_id,
                'product' => Product::where('id', $id)->first(),
                'quantity' => $request->quantity,
                'cart' => Cart::where('user_id', auth()->user()->id)->get(),
                'address' => Address::where('user_id', auth()->user()->id)->get(),
                'delivery' => $delivery,
                'channel' => $channel
            ]);
    }
}
