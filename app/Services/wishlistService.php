<?php
namespace App\Services;

use App\Models\Offer;
use App\Models\Product;
use App\Models\Wishlist;
use Illuminate\Support\Facades\Auth;
use function Laravel\Prompts\select;

class wishlistService{
    public function add_to_wishlist($model)
    {
        if ($model) {
            $existing_item = Wishlist::query()
                ->where('user_id', Auth::id())
                ->where('favoriteable_id', $model->id)
                ->where('favoriteable_type', get_class($model))
                ->first();

            if (!$existing_item) {
                $wishlist_item = Wishlist::create([
                    'user_id' => Auth::id(),
                    'favoriteable_id' => $model->id,
                    'favoriteable_type' => get_class($model)
                ]);
                $message = 'Item added to wishlist successfully';
            } else {
                $wishlist_item = null;
                $message = 'Item already exists in the wishlist';
            }
        } else {
            $wishlist_item = null;
            $message = 'Item not found';
        }
        return [
            'wishlistItem' => $wishlist_item,
            'message' => $message
        ];
    }

    public function show_wishlist()
    {
        $wishlist_items = Wishlist::query()
            ->where('user_id', Auth::id())
            ->get(['favoriteable_id', 'favoriteable_type']);

        $product_ids = $wishlist_items->where('favoriteable_type', '=', Product::class)
            ->pluck('favoriteable_id');
        $offer_ids = $wishlist_items->where('favoriteable_type', '=', Offer::class)
            ->pluck('favoriteable_id');

        $wishlist_products = Product::query()
            ->whereIn('id', $product_ids)->get();
        $wishlist_offers = Offer::query()
            ->whereIn('id', $offer_ids)->get();

        if ($wishlist_products->isNotEmpty() || $wishlist_offers->isNotEmpty()) {
            $message = 'Retrieved all wishlist items successfully';
        } else {
            $message = 'The wishlist is empty';
        }

        return [
            'wishlistProducts' => $wishlist_products,
            'wishlistOffers' => $wishlist_offers,
            'message' => $message,
        ];
    }

    public function remove_from_wishlist($id)
    {
        $wishlist_item = Wishlist::where('user_id', Auth::id())
            ->where('id', $id)
            ->first();

        if ($wishlist_item) {
            $wishlist_item->delete();
            $message = 'Item removed from wishlist successfully';
        } else {
            $message = 'Item not found';
        }

        return [
            'wishlistItem' => $wishlist_item,
            'message' => $message
        ];
    }

}
