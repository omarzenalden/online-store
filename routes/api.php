<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BrandController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\OfferController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProductPhotoController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\WishlistController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

    Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
        return $request->user();
    });

    Route::controller(AuthController::class)->group(function (){
        Route::post('client_register' , 'register_as_client')->name('user.client_register');
        Route::post('seller_register' , 'register_as_seller')->name('user.seller_register');
        Route::post('login' , 'login')->name('user.login');
        Route::group(['middleware' => ['auth:sanctum']] , function (){
            Route::get('logout' , 'logout')->name('user.logout');
        });
        Route::get('forget_password' , 'forget_password')->name('user.forget_password');
        Route::get('check_code' , 'check_code')->name('user.check_code');
        Route::get('reset_password' , 'reset_password')->name('user.reset_password');

        Route::get('auth/google' , 'redirect_to_google');
        Route::get('auth/google/callback' , 'google_handle_call_back');

    Route::group(['middleware' => ['auth:sanctum']] , function (){
        Route::prefix('profile')->group(function (){
        Route::get('/all' , 'show_all_profiles');
        Route::get('/show/{id}' , 'show_user_profile');
        Route::post('/update' , 'update_profile');
        Route::post('password/update' , 'update_password');
        Route::delete('/delete' , 'delete_my_profile');
        Route::delete('/delete/{id}' , 'delete_profile');
          });
        });
    });

    Route::post('/email/verification-notification' , function (Request $request){
            $request->user()->sendEmailVerificationNotification();
        return response()->json(['message' => 'new verification link sent!']);
    })->middleware(['auth:sanctum' , 'throttle:6,1'])->name('verification.send');

    Route::controller(CartController::class)
        ->middleware('auth:sanctum')->group(function (){
        Route::get('/cart', 'show_cart');
        Route::delete('/cart/delete/{product_id}', 'delete_from_cart');
        Route::post('/add_to_cart/{product_id}', 'add_to_cart');
    });


    Route::controller(OrderController::class)
        ->middleware('auth:sanctum')->group(function(){
            Route::post('/save_address','save_address');
            Route::post('/make_primary/{address_id}','make_primary');
            Route::get('/show_addresses','show_addresses');
            Route::post('/edit_address/{address_id}','edit_address');
            Route::delete('/delete_address/{address_id}','delete_address');
            Route::get('/delivery/{cart_id}' , 'deliver_to_my_address');
        });

    Route::controller(WishlistController::class)
        ->middleware('auth:sanctum')->group(function(){
           Route::post('/add_product_to_wishlist/{product_id}' , 'add_product_to_wishlist');
           Route::post('/add_offer_to_wishlist/{offer_id}' , 'add_offer_to_wishlist');
           Route::get('/show_wishlist' , 'show_wishlist');
           Route::delete('/remove_from_wishlist/{id}' , 'remove_from_wishlist');
        });
    //crud products
    Route::controller(ProductController::class)->group(function (){
        Route::get('/AllProduct' , 'GetAllProducts');
        Route::post('/CreateProduct' , 'CreateProduct');
        Route::get('/ShowOneProduct/{ProductId}' , 'GetOneProduct');
        Route::post('/UpdateProduct/{ProductId}' , 'UpdateProduct');
        Route::delete('/DestroyProduct/{Productid}' , 'DestroyProduct');
        Route::post('/Search/Product' , 'SearchProduct');
        Route::get('/GetPopular/{ProductId}' , 'GetPapular');
        Route::get('/GetProductsByCategoryAndBrand/{CategoryId}/{BrandId?}' , 'GetProductsByCategoryAndBrand');
      });
    Route::controller(CategoryController::class)->group(function (){
        Route::get('/AllCategory' , 'GetAllCategories')->name('get_all_categories');
        Route::post('/CreateCategory' , 'CreateCategory')->name('create_category');
        Route::get('/ShowCategory/{CategoryId}' , 'GetOneCategory')->name('show_one_category');
        Route::post('/UpdateCategory/{CategoryId}' , 'UpdateCategory')->name('update_category');
        Route::delete('/DestroyCategory/{CategoryId}' , 'DestroyCategory')->name('delete_category');
        Route::post('/Search/Category' , 'search')->name('SearchCategory');
      });
    Route::controller(BrandController::class)->group(function (){
        Route::get('/AllBrand' , 'GetAllBrand')->name('get_all_brands');
        Route::post('/CreateBrand' , 'CreateBrand')->name('create_brand');
        Route::get('/ShowBrand/{BrandId}' , 'GetOneBrand')->name('show_one_brand');
        Route::post('/UpdateBrand/{BrandIdd}' , 'UpdateBrand')->name('update_brand');
        Route::delete('/DestroyBrand/{BrandId}' , 'DestroyBrand')->name('delete_brand');
        Route::post('/SearchBrand' , 'SearchForBrand')->name('search_brand');
        Route::get('Brands/{categoryId}', 'GetBrandByCategory');
      });

    Route::controller(TransactionController::class)
        ->middleware('auth:sanctum')->group(function(){
            Route::get('/my_wallet' , 'show_my_wallet');
            Route::get('/user_wallet/{user_id}' , 'show_user_wallet');
        });
Route::post('Create/Products/{ProductId}/Photos', [ProductPhotoController::class, 'CreatePhotoesProduct']);
Route::delete('Delete/Product-Photos/{ProductId}', [ProductPhotoController::class, 'DeletePhotoesProduct']);
Route::get('GetOne/ProductPhotos/{ProductId}', [ProductPhotoController::class, 'ShowOnePhotoesProduct']);
Route::get('Products/{ProductId}/GetPhotosByProduct', [ProductPhotoController::class, 'GetPhotosByProduct']);


Route::prefix('offers')->controller(OfferController::class)->group(function () {
    Route::get('/GetAllOffer', 'ShowAllOffers');
    Route::post('/CreateOffer', 'CreateOffer');
    Route::get('/ShowOneOffer/{OfferId}','ShowOneOffer');
    Route::post('/Update/{OfferId}', 'UpdateOffer');
    Route::delete('/Delete/{OfferId}', 'DestroyOffer');
    Route::post('/SearchOffer' , 'SearchOffer');
    Route::post('/{OfferId}/AddProducts', 'AddProductsForOffer');

});
    Route::post('/reviews/create', [ReviewController::class, 'CreateReview']);
    Route::post('/reviews/{ReviewId}', [ReviewController::class, 'UpdateReview']);
    Route::delete('/reviews/delete/{ReviewId}', [ReviewController::class, 'DestroyReview']);
    Route::get('/reviews', [ReviewController::class, 'GetAllReviews']);
    Route::get('/reviews/show/{ReviewId}', [ReviewController::class, 'GetOneReview']);
    Route::get('/GetReviews/ByProduct/{productId}', [ReviewController::class, 'GetByProductId']);
    Route::get('/users/{UserId}/reviews', [ReviewController::class, 'GetByUserId']);
    Route::get('/products/{productId}/comments', [ReviewController::class, 'SortCommentsByTime']);
    Route::get('/products/{productId}/CommentsByMostLikes', [ReviewController::class, 'GetCommentsByMostLikes']);
Route::get('/products/{productId}/CommentsByLessLikes', [ReviewController::class, 'GetCommentsByLessLikes']);
Route::get('/reviews/addlikeOrDisLikeTo/{reviewId}/byuser/{userId}',[ReviewController::class, 'AddDislikeOrDislike']);
Route::get('/reviews/deleteLikeOrDislike/{reviewId}/byuser/{userId}',[ReviewController::class, 'DeleteLikeOrDislike']);

