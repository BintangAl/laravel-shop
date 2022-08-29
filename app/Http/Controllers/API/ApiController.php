<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Address;
use Illuminate\Http\Request;

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
            return response(['Api key not found.'], 404);
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
            return response(['Api key not found.'], 404);
        }
    }
}
