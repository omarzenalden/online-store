<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BrandController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\WishlistController;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

//basic auth

Route::controller(AuthController::class)->group(function(){
    //for client:

    Route::post('/' , 'index')->name('index');
    Route::get('password/forget',  'showForgetPasswordForm')->name('password.forget');
    Route::post('password/email', 'forget_password')->name('password.email');
    Route::get('password/resendCode',  'resend_code')->name('password.resend');
    Route::get('password/code',  'showCheckCodeForm')->name('password.code');

    Route::post('password/check_code', 'check_code')->name('password.check_code');
    Route::get('password/reset', 'showResetPasswordForm')->name('password.reset');
    Route::post('password/update', 'reset_password')->name('password.update');

    //login by google
    Route::get('auth/google', 'redirect_to_google');
    Route::get('auth/google/callback', 'google_handle_call_back');

    Route::post('client_register', 'register_as_client')->name('user.client_register');
    Route::post('seller_register', 'register_as_seller')->name('user.seller_register');
    Route::post('login', 'login')->name('user.login');
    Route::group(['middleware' => ['auth:sanctum']], function () {
        Route::get('logout', 'logout')->name('user.logout');
    });

    Route::group(['middleware' => ['auth:sanctum']], function () {
        Route::prefix('profile')->group(function () {
            //for admin
            Route::get('/all', 'show_all_profiles');
            Route::delete('/delete/{id}', 'delete_profile');

            //for client
            Route::get('/show/{id}', 'show_user_profile');
            Route::post('/update', 'update_profile');
            Route::post('password/update', 'update_password');
            Route::delete('/delete', 'delete_my_profile');
        });
    });
});


    //email verification
    Route::get('/email/verify', function () {
        return view('verify-email');
    })->middleware('auth:sanctum')->name('verification.notice');

    Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
        $request->fulfill();
        return redirect('/');
    })->middleware(['auth:sanctum', 'signed'])->name('verification.verify');

    Route::post('/email/verification-notification', function (Request $request) {
        $request->user()->sendEmailVerificationNotification();
        return back()->with('message', 'Verification link sent!');
    })->middleware(['auth:sanctum', 'throttle:6,1'])->name('verification.send');


    Route::controller(CartController::class)
        ->middleware('auth:sanctum')->group(function () {
        Route::get('/cart', 'show_cart');
        Route::delete('/cart/delete/{product_id}', 'delete_from_cart');
        Route::post('/add_to_cart/{product_id}', 'add_to_cart');
    });


Route::controller(OrderController::class)
    ->middleware('auth:sanctum')->group(function () {
        Route::post('/save_address', 'save_address');
        Route::post('/make_primary/{address_id}', 'make_primary');
        Route::get('/show_addresses', 'show_addresses');
        Route::post('/edit_address/{address_id}', 'edit_address');
        Route::delete('/delete_address/{address_id}', 'delete_address');
        Route::get('/delivery/{cart_id}', 'deliver_to_my_address');
    });

Route::controller(WishlistController::class)
    ->middleware('auth:sanctum')->group(function () {
        Route::post('/add_product_to_wishlist/{product_id}', 'add_product_to_wishlist');
        Route::post('/add_offer_to_wishlist/{offer_id}', 'add_offer_to_wishlist');
        Route::get('/show_wishlist', 'show_wishlist');
        Route::delete('/remove_from_wishlist/{id}', 'remove_from_wishlist');
    });
//crud products
Route::controller(ProductController::class)->group(function () {
    //for admin
    Route::post('/create_product', 'create')->name('create_product');
    Route::post('/update_product/{id}', 'update')->name('update_product');
    Route::delete('/destroy_product/{id}', 'destroy')->name('delete_product');

    //for clients
    Route::get('/all_product', 'index')->name('get_all_products');
    Route::get('/show_product/{id}', 'show')->name('show_one_product');
    Route::post('/search/product', 'search')->name('search_product');
    Route::get('/getPopular/{id}', 'getPopular')->name('getPopular_product');
    Route::get('/getProductsByCategoryAndBrand/{categoryId}/{brandid?}', 'getProductsByCategoryAndBrand')->name('getByCategory');
});
Route::controller(CategoryController::class)->group(function () {
    //for admin
    Route::post('/create_category', 'create')->name('create_category');
    Route::post('/update_category/{id}', 'update')->name('update_category');
    Route::delete('/destroy_category/{id}', 'destroy')->name('delete_category');

    //for clients
    Route::get('/all_category', 'index')->name('get_all_categories');
    Route::get('/show_category/{id}', 'show')->name('show_one_category');
    Route::post('/search/category', 'search')->name('search_category');
});
Route::controller(BrandController::class)->group(function () {
    //for admin
    Route::post('/create_brand', 'create')->name('create_brand');
    Route::post('/update_brand/{id}', 'update')->name('update_brand');
    Route::delete('/destroy_brand/{id}', 'destroy')->name('delete_brand');

    //for clients
    Route::get('/all_brand', 'index')->name('get_all_brands');
    Route::get('/show_brand/{id}', 'show')->name('show_one_brand');
    Route::post('/search_brand', 'search')->name('search_brand');
    Route::get('brands/{category_id}', 'getByCategory');
});

Route::controller(TransactionController::class)
    ->middleware('auth:sanctum')->group(function () {
        //for admin
        Route::get('/user_wallet/{user_id}', 'show_user_wallet');

        //for clients
        Route::get('/my_wallet', 'show_my_wallet');
    });
