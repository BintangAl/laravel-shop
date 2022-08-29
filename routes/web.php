<?php

use App\Http\Controllers\AppController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\Tripay\TripayCallbackController;
use App\Http\Controllers\UserController;
use App\Models\Cart;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// Route::get('/dashboard', function () {
//     return view('dashboard');
// })->middleware(['auth'])->name('dashboard');

require __DIR__ . '/auth.php';

Route::get('/', [AppController::class, 'app'])->name('app');
Route::get('/search', [AppController::class, 'search'])->name('search');

Route::get('/category/{id}/{category}', [AppController::class, 'category'])->name('category');
Route::get('/product/{id}/{product}', [AppController::class, 'product'])->name('product');

Route::get('/user/profile', [UserController::class, 'profile'])->name('profile')->middleware('auth');
Route::post('/user/profile-update', [UserController::class, 'profileUpdate'])->name('profile-update')->middleware('auth');

Route::get('/user/address', [UserController::class, 'address'])->name('address')->middleware('auth');
Route::post('/user/add-address', [UserController::class, 'addAddress'])->name('add-address')->middleware('auth');
Route::post('/user/main-address', [UserController::class, 'mainAddress'])->name('main-address')->middleware('auth');

Route::get('/user/password', function () {
    return view('profile')
        ->with([
            'title' => 'password',
            'cart' => Cart::where('user_id', auth()->user()->id)->get()
        ]);
})->name('password')->middleware('auth');
Route::post('/user/change-password', [UserController::class, 'changePassword'])->name('change-password')->middleware('auth');

Route::get('/user/purchase/{status}', [UserController::class, 'purchase'])->name('purchase')->middleware('auth');

Route::get('/cart', [AppController::class, 'cart'])->name('cart')->middleware('auth');
Route::post('/add-cart', [AppController::class, 'addCart'])->name('add-cart')->middleware('auth');
Route::post('/update-quantity', [AppController::class, 'updateQuantity'])->name('update-quantity')->middleware('auth');
Route::post('/delete-cart', [AppController::class, 'deleteCart'])->name('delete-cart')->middleware('auth');

Route::get('/checkout/{id}/{product}/{cart_id}', [AppController::class, 'checkout'])->name('checkout')->middleware('auth');
Route::post('/transaction', [TransactionController::class, 'transaction'])->name('transaction')->middleware('auth');

Route::get('/transaction/{invoice}', [TransactionController::class, 'detailTransaction'])->name('detail-transaction')->middleware('auth');
Route::get('/payment/{invoice}', [TransactionController::class, 'payment'])->name('payment')->middleware('auth');

Route::post('callback', [TripayCallbackController::class, 'handle']);
