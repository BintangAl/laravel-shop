<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AuthRedirectController extends Controller
{
    public function redirectAuth()
    {
        if (request('action') == 'cart' || request('action') == 'buyNow') {
            return [
                'action' => request('action'),
                'product_id' => request('product_id'),
                'quantity' => request('quantity'),
                'size' => request('size') != 'undefined' ? request('size') : '',
                'color' => request('color') != 'undefined' ? request('color') : '',
            ];
        }
    }
}
