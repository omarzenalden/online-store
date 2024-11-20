<?php

namespace App\Services;

use App\Models\Cart;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;

class CartService
{

    public function show_cart()
    {
        //show the cart and cart_items tables
        $cart = Cart::with(['products' => function ($query) {
            $query->select('products.*', 'cart_items.*'); // Select additional pivot data (e.g., quantity)
        }])
            ->where('user_id', Auth::id())
            ->first();

        $message = 'getting cart successfully';
        return [
            'cart' => $cart,
            'message' => $message,
        ];
    }

    public function add_to_cart($request, $product_id)
    {
        $cart = Cart::with(['products' => function ($query) {
            $query->select('cart_items.*');
        }])
            ->where('user_id', Auth::id())
            ->first();

        $product = Product::find($product_id);

        if (!$cart || !$product) {
            return [
                'cart' => '',
                'message' => 'Cart or product not found',
            ];
        }

        if ($product->quantity < $request['quantity']) {
            return [
                'cart' => '',
                'message' => 'Not enough stock available',
            ];
        }
            $product_in_cart = $cart->products()
                ->where('cart_items.product_id' , $product_id)
                ->first();

            if (!is_null($product_in_cart)){
                $product_in_cart->pivot->quantity +=  $request['quantity'];
                $product_in_cart->pivot->price += ($product->price * $request['quantity']);
                $cart->products()->updateExistingPivot($product_id,[
                    'quantity' => $product_in_cart->pivot->quantity,
                    'price' => $product_in_cart->pivot->price,
                ]);
                $cart->total_price += $product->price * $request['quantity'];
                $cart->save();
                return [
                    'product' => $product_in_cart,
                    'message' => 'Product added to cart',
                ];
            }else {
                // Attach the product to the cart with the specified quantity and price
                $cart->products()->attach($product_id, [
                    'quantity' => $request['quantity'],
                    'price' => $product->price * $request['quantity'],
                ]);
                $product->quantity -= $request['quantity'];
                $product->save();
                $cart->total_price = $cart->products()->sum('cart_items.price');
                Cart::query()
                    ->where('user_id', Auth::id())
                    ->update([
                        'total_price' => $cart->total_price,
                    ]);
                return [
                    'product' => $product,
                    'message' => 'Product added to cart',
                ];
            }
    }

    public function delete_from_cart($product_id)
    {
        $cart = Cart::with(['products' => function($query){
            $query->select('cart_items.*');
        }])
        ->where('user_id' , Auth::id())
        ->first();

        $product_in_cart = $cart->products()
            ->where('cart_items.product_id' , $product_id)
            ->first();
        if (!$product_in_cart){
            return [
                'cart' => '',
                'message' => 'product not found'
            ];
        }else{
            $product_price = $product_in_cart->pivot->price;
            $cart->total_price -= $product_price;
            $cart->save();
                $cart->products()->detach($product_id);
            return [
                'cart'  => $cart,
                'message' => 'product delete from the cart successfully',
            ];
        }
    }



}
