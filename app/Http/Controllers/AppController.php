<?php

namespace App\Http\Controllers;

use App\Events\NotificationCreated;
use App\Http\Controllers\API\ApiController;
use Carbon\Carbon;
use App\Http\Controllers\Tripay\TripayController;
use App\Models\Cart;
use App\Models\Category;
use App\Models\Customer;
use App\Models\Image;
use App\Models\Product;
use App\Models\ProductColor;
use App\Models\ProductSize;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class AppController extends Controller
{
    public function app()
    {
        $api = new ApiController();
        if (Auth::user()) {
            $notif = json_decode($api->Notification('list', [
                'user_id' => auth()->user()->id,
                'to' => 'munnshop'
            ]));
        }

        return view('app')
            ->with([
                'title' => 'MunnShop',
                'notif' => isset($notif) ? $notif->data : null,
                'carousel' => Image::where('category', 'carousel')->latest()->limit(3)->get(),
                'category' => Category::all(),
                'recomendation' => Product::with('image')->get(),
                'cart' => (Auth::check()) ? Cart::where('user_id', auth()->user()->id)->get() : []
            ]);
    }

    public function notification(Request $request)
    {
        $api = new ApiController();
        $notif = $api->Notification('is_true', ['id' => $request->id]);

        return response()->json($notif);
    }

    public function search(Request $request)
    {
        $output = '';
        if (isset($request->search)) {
            $product = Product::where('product_name', 'Like', '%' . $request->search . '%')->get();

            foreach ($product as $key => $value) {
                $link = "'" . route('product', [$value->id, strtolower(str_replace([' ', '/'], '_', $value->product_name))]) . "'";
                $output .= '
                <div class="col-lg-2 col-md-3 col-sm-4 col-6 mb-4">
                    <div class="product position-relative" onclick="window.location=' . $link . '">
                        <div class="box-product bg-white shadow-sm h-225 position-relative pointer">
                            <div class="product-image bg-white h-150 w-100 bg-image" style="background-image: url(' . url($value->image[0]->image) . ')"></div>
                            <div class="fs-xsmall p-2 sans">' . substr($value->product_name, 0, 27) . (strlen($value->product_name) > 27 ? '...' : '') . '</div>
                            <div class="product-price position-absolute bottom-0 start-0 ps-2 text-main mb-1">Rp ' . number_format($value->product_price) . '</div>
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
        $api = new ApiController();
        if (Auth::user()) {
            $notif = json_decode($api->Notification('list', [
                'user_id' => auth()->user()->id,
                'to' => 'munnshop'
            ]));
        }

        return view('category')
            ->with([
                'title' => 'category',
                'notif' => isset($notif) ? $notif->data : null,
                'category' => Category::where('id', $id)->first()->category,
                'product' => Category::where('id', $id)->first()->product,
                'cart' => (Auth::check()) ? Cart::where('user_id', auth()->user()->id)->get() : []
            ]);
    }

    public function product($id, $product)
    {
        $api = new ApiController();
        if (Auth::user()) {
            $notif = json_decode($api->Notification('list', [
                'user_id' => auth()->user()->id,
                'to' => 'munnshop'
            ]));
        }

        $product = Product::where('id', $id)->first();

        if ($product) {
            return view('product')
                ->with([
                    'title' => 'product',
                    'notif' => isset($notif) ? $notif->data : null,
                    'product' => $product,
                    'image' => array_merge(json_decode($product->image), json_decode($product->color)),
                    'cart' => (Auth::check()) ? Cart::where('user_id', auth()->user()->id)->get() : [],
                    'detail' => explode("\n", $product->product_detail),
                    'recomendation' => Category::with('product')->find($product->category_id)->product
                ]);
        } else {
            abort(404);
        }
    }

    public function cart()
    {
        $api = new ApiController();
        if (Auth::user()) {
            $notif = json_decode($api->Notification('list', [
                'user_id' => auth()->user()->id,
                'to' => 'munnshop'
            ]));
        }

        return view('cart')
            ->with([
                'title' => 'cart',
                'notif' => isset($notif) ? $notif->data : null,
                'cart' => json_decode(Cart::with('productDetail')->where('user_id', auth()->user()->id)->latest('updated_at')->get())
            ]);
    }

    public function addCart(Request $request)
    {
        $user_id = auth()->user()->id;
        $product = Product::where('id', $request->product_id)->first();
        $size = ProductSize::find($request->size_id);
        $color = ProductColor::find($request->color_id);
        $cart = Cart::where([['user_id', '=', $user_id], ['product_id', '=', $product->id], ['product_size', '=', ($size ? $size->size : null)], ['product_color', '=', ($color ? $color->color : null)]])->get();

        if (count($cart)) {
            $cart[0]->quantity = $cart[0]->quantity + $request->quantity;
            $cart[0]->save();

            if ($request->type == 'buyNow') {
                Session::flash('buyNow', $product->product_price * $cart[0]->quantity);
            }

            return response('updated');
        } else {
            $price = $product->product_price;
            if ($size && $color) {
                $price = max($size->price, $color->price);
            } else if ($size) {
                $price = $size->price;
            } elseif ($color) {
                $price = $color->price;
            }

            Cart::create([
                'user_id' => $user_id,
                'product_id' => $product->id,
                'product_size' => ($size ? $size->size : null),
                'product_color' => ($color ? $color->color : null),
                'price' => $price,
                'quantity' => $request->quantity,
            ]);

            if ($request->type == 'buyNow') {
                Session::flash('buyNow', $product->product_price * $request->quantity);
            }

            return response('added');
        }
    }

    public function updateQuantity(Request $request)
    {
        $user_id = auth()->user()->id;
        $product = Cart::with('productDetail')->where([['user_id', '=', $user_id], ['id', '=', $request->id]])->get();

        if (count($product)) {
            if ($request->action == 'min') {
                if ($request->quantity != 0) {
                    $product[0]->quantity = $product[0]->quantity - 1;
                    $product[0]->save();
                }
            } elseif ($request->action == 'add') {
                $product[0]->quantity = $product[0]->quantity + 1;
                $product[0]->save();
            }
        }

        return response($product[0]);
    }

    public function deleteCart(Request $request)
    {
        Cart::where([['user_id', '=', auth()->user()->id], ['id', '=', $request->id]])->delete();
        return response('deleted');
    }

    public function checkout(Request $request)
    {
        if (!$request->cart) {
            abort(404);
        }

        $carts = [];
        foreach ($request->cart as $cart_id) {
            $carts[] = json_decode(Cart::with('productDetail')->find($cart_id));
        }

        $subtotal = 0;
        foreach ($carts as $total) {
            $subtotal += ($total->price * $total->quantity);
        }

        $address = Customer::where([['user_id', '=', auth()->user()->id], ['status', '=', 'true']])->first();
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

        $api = new ApiController();
        if (Auth::user()) {
            $notif = json_decode($api->Notification('list', [
                'user_id' => auth()->user()->id,
                'to' => 'munnshop'
            ]));
        }

        return view('checkout')
            ->with([
                'title' => 'checkout',
                'cart' => Cart::where('user_id', auth()->user()->id)->get(),
                'notif' => isset($notif) ? $notif->data : null,
                'cart_id' => $request->cart,
                'products' => $carts,
                'subtotal' => $subtotal,
                'address' => Customer::where('user_id', auth()->user()->id)->get(),
                'delivery' => $delivery,
                'channel' => $channel
            ]);
    }
}
