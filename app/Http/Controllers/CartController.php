<?php

namespace App\Http\Controllers;

use App\Http\Requests\AddToCartRequest;
use App\Http\Responses\Response;
use App\Services\CartService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public $cartService;

    public function __construct(CartService $cartService)
    {
        $this->cartService = $cartService;
    }

    public function show_cart():jsonResponse
    {
        $data = [];
        try {
            $data = $this->cartService->show_cart();
            return Response::Success($data['cart'],$data['message']);
        }catch(Exception $e){
            $message = $e->getMessage();
            return Response::Error($data,$message);
        }
    }
    public function add_to_cart(AddToCartRequest $request,$product_id):jsonResponse
    {
        $data = [];
        try {
            $data = $this->cartService->add_to_cart($request->validated(),$product_id);
            return Response::Success($data['product'],$data['message']);
        }catch(Exception $e){
            $message = $e->getMessage();
            return Response::Error($data,$message);
        }
    }
    public function delete_from_cart($product_id):jsonResponse
    {
        $data = [];
        try {
            $data = $this->cartService->delete_from_cart($product_id);
            return Response::Success($data['cart'],$data['message']);
        }catch(Exception $e){
            $message = $e->getMessage();
            return Response::Error($data,$message);
        }
    }

    ////
    // add update quantity to cart

}
