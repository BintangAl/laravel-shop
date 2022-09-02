<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Address;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Transaction;
use App\Models\User;

use function PHPUnit\Framework\returnSelf;

class ApiController extends Controller
{
    public function SelectAddress($key, $id)
    {
        if ($key == config('services.app_api.key')) {
            $address = Address::find($id);

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

    public function Users(Request $request)
    {
        if ($request->key == config('services.app_api.key')) {
            return response()->json([
                'status' => 200,
                'message' => 'Success Get Data.',
                'data' => User::get(['name', 'email'])
            ]);
        }
    }

    public function Products(Request $request)
    {
        if ($request->key == config('services.app_api.key')) {
            if ($request->action == 'list') {
                return response()->json([
                    'status' => 200,
                    'message' => 'Success Get Data',
                    'data' => Product::all()
                ], 200);
            } elseif ($request->action == 'create') {
                //dd($request->all());
                $validate = $request->validate([
                    'product_name' => 'required',
                    'product_price' => 'required',
                    'product_detail' => 'required',
                    'product_image' => 'required',
                    'product_stok' => 'required',
                    'product_sold' => 'required',
                    'category_id' => 'required'
                ]);

                Product::create($validate);
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
                    'product_image' => 'required',
                    'product_stok' => 'required',
                    'product_sold' => 'required',
                    'category_id' => 'required'
                ]);

                Product::where('id, $id')->update($validate);
                return response()->json([
                    'status' => 200,
                    'message' => 'Product Updated.',
                    'data' => $validate
                ], 200);
            } elseif ($request->action == 'delete') {

                if (isset($request->id)) {
                    $product = Product::where('id', $request->id);

                    if (count($product->get())) {
                        $product->delete();
                        return response()->json([
                            'status' => 200,
                            'message' => 'Product Deleted.',
                            'data' => []
                        ], 200);
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

    public function Transactions(Request $request)
    {
        if ($request->key == config('services.app_api.key')) {
            if ($request->action == "list") {
                return response()->json([
                    'status' => 200,
                    'message' => 'Success Get Data',
                    'data' => Transaction::all()
                ], 200);
            } elseif ($request->action == 'update') {
                $validate = $request->validate([
                    'id' => 'required',
                    'no_resi' => 'required',
                    'status' => 'required'
                ]);

                $status = ['Belum Bayar', 'Dikemas', 'Dikirim', 'Selesai', 'Dibatalkan', 'Gagal'];

                if (in_array($request->status, $status)) {
                    Transaction::where('id', $request->id)->update($validate);

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
}
