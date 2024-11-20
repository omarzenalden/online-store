<?php

namespace App\Http\Controllers;

use App\Http\Responses\Response;
use App\Models\Offer;
use App\Models\Product;
use App\Services\wishlistService;
use Exception;
use Illuminate\Http\Request;

class WishlistController extends Controller
{
    protected $wishlistService;

    public function __construct(wishlistService $wishlistService)
    {
        return $this->wishlistService = $wishlistService;
    }

    public function add_product_to_wishlist($product_id)
    {
        $data = [];
        try{
            $product = Product::query()->find($product_id);
            $data = $this->wishlistService->add_to_wishlist($product);
            return Response::Success($data['wishlistItem'],$data['message']);
        }catch(Exception $e){
            $message = $e->getMessage();
            return Response::Error($data,$message);
        }
    }
    public function add_offer_to_wishlist($offer_id)
    {
        $data = [];
        try{
            $offer = Offer::query()->find($offer_id);
            $data = $this->wishlistService->add_to_wishlist($offer);
            return Response::Success($data['wishlistItem'],$data['message']);
        }catch(Exception $e){
            $message = $e->getMessage();
            return Response::Error($data,$message);
        }
    }
    public function show_wishlist()
    {
        $data = [];
        try{
            $data = $this->wishlistService->show_wishlist();
            return response()->json([
                'products' => $data['wishlistProducts'],
                'offers' => $data['wishlistOffers'],
                'message' => $data['message']
            ]);
        }catch(Exception $e){
            $message = $e->getMessage();
            return Response::Error($data,$message);
        }
    }
    public function remove_from_wishlist($id)
    {
        $data = [];
        try{
            $data = $this->wishlistService->remove_from_wishlist($id);
            return Response::Success($data['wishlistItem'],$data['message']);
        }catch(Exception $e){
            $message = $e->getMessage();
            return Response::Error($data,$message);
        }
    }
}
