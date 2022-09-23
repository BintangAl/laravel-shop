<?php

namespace App\Http\Controllers\API;

use App\Events\NotificationCreated;
use Carbon\Carbon;
use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Customer;
use App\Models\Image;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\ProductColor;
use App\Models\ProductSize;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Support\Facades\File;

use function PHPUnit\Framework\returnSelf;

class ApiController extends Controller
{
    public function Notification($action, $data)
    {
        $notifications = curl_init();

        curl_setopt_array($notifications, array(
            CURLOPT_URL => env('DAHSBOARD_URL') . '/api/notification',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => array(
                'key' => config('services.dashboard.key'),
                'action' => $action,
                'id' => isset($data['id']) ? $data['id'] : '',
                'message' => isset($data['message']) ? $data['message'] : '',
                'user_id' => isset($data['user_id']) ? $data['user_id'] : '',
                'invoice' => isset($data['invoice']) ? $data['invoice'] : '',
                'product' => isset($data['product']) ? $data['product'] : '',
                'image' => isset($data['image']) ? $data['image'] : '',
                'from' => isset($data['from']) ? $data['from'] : '',
                'to' => isset($data['to']) ? $data['to'] : '',
                'status' => isset($data['status']) ? $data['status'] : ''
            ),
            CURLOPT_HTTPHEADER => array(
                'accept: application/json'
            ),
        ));

        $response = curl_exec($notifications);

        $error = curl_error($notifications);

        curl_close($notifications);

        return $response ?: $error;
    }

    public function NotificationNextId()
    {
        $notifications = curl_init();

        curl_setopt_array($notifications, array(
            CURLOPT_URL => env('DAHSBOARD_URL') . '/api/notification',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => array(
                'key' => config('services.dashboard.key'),
                'action' => 'next_id',
            ),
            CURLOPT_HTTPHEADER => array(
                'accept: application/json'
            ),
        ));

        $response = curl_exec($notifications);

        $error = curl_error($notifications);

        curl_close($notifications);

        return $response ?: $error;
    }

    public function SelectAddress($key, $id)
    {
        if ($key == config('services.app_api.key')) {
            $address = Customer::find($id);

            return response([
                'name' => $address->nama_penerima,
                'phone' => $address->no_tlp,
                'address' => $address->alamat,
                'province_id' => explode('#', $address->provinsi)[1],
                'province' => explode('#', $address->provinsi)[0],
                'city_id' => explode('#', $address->kota)[1],
                'city' => explode('#', $address->kota)[0],
                'kodepos' => $address->kodepos
            ], 200);
        } else {
            return response(['Api key not found.'], 400);
        }
    }

    public function DeliveryOption($key, $destination)
    {
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
            CURLOPT_POSTFIELDS => 'origin=178&destination=' . $destination . '&weight=1&courier=jne',
            CURLOPT_HTTPHEADER => array(
                'key: 5db1d89e70c6f05ca4a6c0f178c81f2c',
                'Content-Type: application/x-www-form-urlencoded'
            ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);

        if ($key == config('services.app_api.key')) {
            return response()->json(json_decode($response)->rajaongkir->results[0]);
        } else {
            return response(['Api key not found.'], 400);
        }
    }

    public function Statistic(Request $request)
    {
        if ($request->key == config('services.app_api.key')) {
            $request->validate(['filter' => 'required']);
            if ($request->filter == 'monthly') {
                $dataSuccess = Transaction::whereYear('created_at', Carbon::now()->year)->whereMonth('created_at', Carbon::now()->month)->where('status', 'Selesai');

                $orders = Transaction::whereYear('created_at', Carbon::now()->year)->whereMonth('created_at', Carbon::now()->month);

                $monthly = [];
                for ($i = 1; $i <= 31; $i++) {
                    $monthly['dat' . $i] = Transaction::whereYear('created_at', Carbon::now()->year)->whereMonth('created_at', Carbon::now()->month)->whereDate('created_at', date('Y-m') . '-' . $i)->where('status', 'Selesai')->sum('amount');
                }

                return response()->json([
                    'status' => 200,
                    'message' => 'Success Get Data',
                    'data' => [
                        'sales' => $dataSuccess->sum('amount'),
                        'orders' => (isset($request->status) ? $orders->where('status', $request->status)->get() : $orders->get()),
                        'statistic' => $monthly
                    ]
                ]);
            } elseif ($request->filter == 'yearly') {
                $dataSuccess = Transaction::whereYear('created_at', Carbon::now()->year)->where('status', 'Selesai');

                $orders = Transaction::whereYear('created_at', Carbon::now()->year);

                $yearly = [];
                for ($i = 1; $i <= 12; $i++) {
                    $yearly['mon' . $i] = Transaction::whereYear('created_at', Carbon::now()->year)->whereMonth('created_at', $i)->where('status', 'Selesai')->sum('amount');
                }

                return response()->json([
                    'status' => 200,
                    'message' => 'Success Get Data',
                    'data' => [
                        'sales' => $dataSuccess->sum('amount'),
                        'orders' => (isset($request->status) ? $orders->where('status', $request->status)->get() : $orders->get()),
                        'statistic' => $yearly
                    ]
                ]);
            } else {
                return response()->json([
                    'status' => 400,
                    'message' => 'filter only monthly & yearly',
                    'data' => []
                ], 400);
            }
        } else {
            return response()->json([
                'status' => 400,
                'message' => 'key not found.',
                'data' => []
            ], 400);
        }
    }

    public function Users(Request $request)
    {
        if ($request->key == config('services.app_api.key')) {
            if (isset($request->search)) {
                return response()->json([
                    'status' => 200,
                    'message' => 'Success Get Data.',
                    'data' => User::where('name', 'Like', '%' . $request->search . '%')->paginate(10)
                ]);
            } else {
                return response()->json([
                    'status' => 200,
                    'message' => 'Success Get Data.',
                    'data' => User::paginate(10)
                ]);
            }
        } else {
            return response()->json([
                'status' => 400,
                'message' => 'key not found.',
                'data' => []
            ], 400);
        }
    }

    public function Products(Request $request)
    {
        if ($request->key == config('services.app_api.key')) {
            if ($request->action == 'list') {
                if (isset($request->search)) {
                    return response()->json([
                        'status' => 200,
                        'message' => 'Success Get Data',
                        'data' => Product::with(['image', 'category', 'size', 'color'])
                            ->where('product_name', 'Like', '%' . $request->search . '%')
                            ->orWhere('sku', 'Like', '%' . $request->search . '%')
                            ->latest()->paginate(),
                    ], 200);
                } elseif (isset($request->id)) {
                    return response()->json([
                        'status' => 200,
                        'message' => 'Success Get Data',
                        'data' => Product::with(['image', 'category', 'size', 'color'])->where('id', $request->id)->get(),
                    ], 200);
                } else {
                    return response()->json([
                        'status' => 200,
                        'message' => 'Success Get Data',
                        'data' => Product::with(['image', 'category', 'size', 'color'])->paginate(10)
                    ], 200);
                }
            } elseif ($request->action == 'create') {
                //dd($request->all());
                $validate = $request->validate([
                    'sku' => 'required',
                    'product_name' => 'required',
                    'product_price' => 'required',
                    'product_detail' => 'required',
                    'product_stok' => 'required',
                    'category_id' => 'required'
                ]);

                Product::create($validate);

                if (isset($request->size_custom)) {
                    foreach (json_decode($request->size_custom) as $size) {
                        ProductSize::create([
                            'product_id' => Product::latest()->first()->id,
                            'size' => explode('#', $size)[0],
                            'price' => explode('#', $size)[1],
                        ]);
                    }
                }

                if (isset($request->size)) {
                    foreach (json_decode($request->size) as $size) {
                        ProductSize::create([
                            'product_id' => Product::latest()->first()->id,
                            'size' => $size,
                            'price' => $request->product_price,
                        ]);
                    }
                }
                return response()->json([
                    'status' => 200,
                    'message' => 'Product Created.',
                    'data' => $validate
                ], 200);
            } elseif ($request->action == 'update') {
                //dd($request->all());
                $validate = $request->validate([
                    'id' => 'required',
                    'product_name' => 'required',
                    'product_price' => 'required',
                    'product_detail' => 'required',
                    'product_size' => 'max:255',
                    'product_stok' => 'required',
                    'category_id' => 'required'
                ]);

                Product::where('id', $request->id)->update($validate);
                return response()->json([
                    'status' => 200,
                    'message' => 'Product Updated.',
                    'data' => $validate
                ], 200);
            } elseif ($request->action == 'add-size') {
                if ($request->data) {
                    if ($request->product_id) {
                        ProductSize::where('product_id', $request->product_id)->delete();
                        if ($request->data) {
                            foreach (json_decode($request->data) as $size) {
                                ProductSize::create([
                                    'product_id' => $size->product_id,
                                    'size' => $size->size,
                                    'price' => $size->price
                                ]);
                            }
                        }

                        return response()->json([
                            'status' => 200,
                            'message' => 'Product Size Added.',
                            'data' => $request->data ?: json_decode($request->data)
                        ], 200);
                    } else {
                        return response()->json([
                            'status' => 400,
                            'message' => 'Product Id not found.',
                            'data' => ''
                        ], 400);
                    }
                } else {
                    return response()->json([
                        'status' => 400,
                        'message' => 'Data array not found.',
                        'data' => ''
                    ], 400);
                }
            } elseif ($request->action == 'delete') {
                if (isset($request->id)) {
                    $product = Product::where('id', $request->id);
                    $image = Image::where('product_id', $request->id);
                    $product_size = ProductSize::where('product_id', $request->id);
                    $product_color = ProductColor::where('product_id', $request->id);
                    if (count($product->get())) {
                        $product->delete();
                        $product_size->delete();

                        $pathProductImage = [];
                        $pathProductColor = [];
                        foreach ($image->get('image') as $img) {
                            $pathProductImage[] = public_path($img->image);
                        }
                        foreach ($product_color->get('image') as $imgColor) {
                            $pathProductColor[] = public_path($imgColor->image);
                        }
                        File::delete($pathProductImage);
                        File::delete($pathProductColor);
                        $image->delete();
                        $product_color->delete();

                        return response()->json([
                            'status' => 200,
                            'message' => 'Product Deleted.',
                            'data' => []
                        ], 200);
                    } else {
                        return response()->json([
                            'status' => 400,
                            'message' => 'Product not found.',
                            'data' => []
                        ], 400);
                    }
                } else {
                    return response()->json([
                        'status' => 400,
                        'message' => 'ID not found.',
                        'data' => []
                    ], 400);
                }
            } else {
                return response()->json([
                    'status' => 400,
                    'message' => 'action not found.',
                    'data' => []
                ], 400);
            }
        } else {
            return response()->json([
                'status' => 400,
                'message' => 'key not found.',
                'data' => []
            ], 400);
        }
    }

    public function ProductColor(Request $request)
    {
        if ($request->key == config('services.app_api.key')) {
            $image = $request->File('image');

            if ($image) {
                $new_name = rand() . '.' . $image->getClientOriginalExtension();
                $image->move(public_path('/asset/img/product'), $new_name);
            }

            if ($request->action == 'create') {
                ProductColor::create([
                    'product_id' => ($request->id ?: Product::latest()->first()->id),
                    'color' => $request->color,
                    'price' => $request->price,
                    'image' => ($image ? '/asset/img/product/' . $new_name : null),
                ]);

                $message = 'Variant Color Created.';
            } elseif ($request->action == 'update') {
                if ($request->update == 'color') {
                    ProductColor::find($request->id)->update(['color' => $request->color]);
                } elseif ($request->update == 'price') {
                    ProductColor::find($request->id)->update(['price' => $request->price]);
                } elseif ($request->update == 'image') {
                    $color = ProductColor::find($request->id);
                    if (isset($color->image)) {
                        unlink(public_path($color->image));
                    }
                    $color->update(['image' => '/asset/img/product/' . $new_name]);
                }

                $message = 'Variant Color Updated.';
            } elseif ($request->action == 'delete') {
                $color = ProductColor::find($request->id);
                if (isset($color->image)) {
                    unlink(public_path($color->image));
                }
                $color->delete();
                $message = 'Variant Color Deleted';
            }

            return response()->json([
                'status' => 200,
                'message' => $message ?: '',
                'data' => request()->all()
            ], 200);
        } else {
            return response()->json([
                'status' => 400,
                'message' => 'key not found.',
                'data' => []
            ], 400);
        }
    }

    public function Transactions(Request $request)
    {
        if ($request->key == config('services.app_api.key')) {
            if ($request->action == "list") {
                if (isset($request->invoice)) {
                    $data = Transaction::with(['user', 'customer', 'orders'])->where('invoice', 'Like', '%' . $request->invoice . '%')->first();

                    return response()->json([
                        'status' => 200,
                        'message' => 'Success Get Data',
                        'data' => $data,
                    ], 200);
                } else {
                    return response()->json([
                        'status' => 200,
                        'message' => 'Success Get Data',
                        'data' => Transaction::with(['user', 'orders'])->latest()->paginate(10)
                    ], 200);
                }
            } elseif ($request->action == 'update') {
                $validate = $request->validate([
                    'id' => 'required',
                    'no_resi' => 'required',
                    'status' => 'required'
                ]);

                $status = ['Belum Bayar', 'Dikemas', 'Dikirim', 'Selesai', 'Dibatalkan', 'Gagal'];

                if (in_array($request->status, $status)) {
                    $transaction = Transaction::where('id', $request->id)->first();
                    $product = Product::where('id', $transaction->product_id)->first();

                    $transaction->update($validate);

                    return response()->json([
                        'status' => 200,
                        'message' => 'Transactions Updated.',
                        'data' => $validate
                    ], 200);
                } else {
                    return response()->json([
                        'status' => 400,
                        'message' => 'Status wrong.',
                        'status choice' => $status
                    ], 400);
                }
            }
        } else {
            return response()->json([
                'status' => 400,
                'message' => 'key not found.',
                'data' => []
            ], 400);
        }
    }

    public function SetImage(Request $request)
    {
        if ($request->key == config('services.app_api.key')) {
            $request->validate(['action' => 'required']);

            if ($request->action != 'upload') {
                $request->validate(['id' => 'required']);
                $db_image = Image::find($request->id);
            }

            if ($request->action != 'delete') {
                $request->validate([
                    'image' => 'required|image',
                    'category' => 'required'
                ]);

                if ($request->category == 'product') {
                    $request->validate(['product_id' => 'required']);
                }

                $image = $request->File('image');
                $new_name = rand() . '.' . $image->getClientOriginalExtension();

                $data = [
                    'image' => $new_name,
                    'category' => $request->category,
                ];
            }

            if ($request->action == 'upload') {
                Image::create([
                    'image' => '/asset/img/' . $request->category . '/' . $new_name,
                    'category' => $request->category,
                    'product_id' => $request->product_id
                ]);
                $image->move(public_path('/asset/img/' . $request->category), $new_name);

                $message = 'Image Success Uploaded.';
            } elseif ($request->action == 'update') {
                if ($db_image) {
                    unlink(public_path($db_image->image));
                    $db_image->update([
                        'image' => '/asset/img/' . $request->category . '/' . $new_name,
                        'category' => $request->category,
                        'product_id' => $request->product_id

                    ]);
                    $image->move(public_path('/asset/img/' . $request->category), $new_name);

                    $message = 'Image Success Updated.';
                } else {
                    $message = 'ID Image not found.';
                    $data = [];
                }
            } elseif ($request->action == 'delete') {
                if ($db_image) {
                    unlink(public_path($db_image->image));
                    $db_image->delete();
                    $message = 'Image Success Deleted.';
                    $data = [];
                } else {
                    $message = 'ID Image not found.';
                    $data = [];
                }
            } else {
                $message = 'Action options [upload, update, delete]';
                $data = [];
            }

            return response()->json([
                'status' => 200,
                'message' => $message,
                'data' => $data
            ], 200);
        } else {
            return response()->json([
                'status' => 400,
                'message' => 'key not found.',
                'data' => []
            ], 400);
        }
    }

    public function Galery(Request $request)
    {
        if ($request->key == config('services.app_api.key')) {
            if (isset($request->category)) {
                $image = Image::where('category', $request->category)->latest();

                if (isset($request->limit)) {
                    $data = $image->limit($request->limit)->get(['id', 'image', 'category']);
                } else {
                    $data = $image->get(['id', 'image', 'category']);
                }
            } else {
                $data = Image::get(['id', 'image', 'category']);
            }

            return response()->json([
                'status' => 200,
                'message' => 'success',
                'data' => $data ?: []
            ], 200);
        } else {
            return response()->json([
                'status' => 400,
                'message' => 'key not found.',
                'data' => []
            ], 400);
        }
    }

    public function CategorieProduct(Request $request)
    {
        if ($request->key == config('services.app_api.key')) {
            if (isset($request->action)) {
            } else {
                return response()->json([
                    'status' => 200,
                    'message' => 'Success Get Data.',
                    'data' => Category::with('product')->get()
                ], 200);
            }
        } else {
            return response()->json([
                'status' => 400,
                'message' => 'key not found.',
                'data' => []
            ], 400);
        }
    }
}
