<?php

namespace App\Repositories\products;

use App\Models\Product;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Repositories\products\ProductRepositoryInterface;


class ProductRepository implements ProductRepositoryInterface
{
    public function all()
    {
        $products = product::query()
            ->select()
            ->paginate(50);
        $products ? $message = 'getting all products successfully' : $message = 'not found';

        return [
            'products' => $products,
            'message' => $message,
        ];
    }

    public function find($id)
    {

        $product = Product::find($id);
        $product ? $message = 'getting product successfully' : $message = 'not found';
        return [
            'product' => $product,
            'message' => $message,
        ];
    }

    public function create(array $attributes)
    {
        if (Auth::user()->hasRole('admin')){
            $product = Product::create($attributes);
            $message = 'product created successfully';
        }else{
            $product = null;
            $message = 'you do not have access';
        }
        return [
            'product' => $product,
            'message' => $message,
        ];

    }

    public function update($id, array $attributes)
    {
        $product = product::find($id);

        if ($product) {
            if (Auth::user()->hasRole('admin')){
                $product->update($attributes);
                $message = 'product updated successfully';
            }else{
                $product = null;
                $message = 'you do not have access';
            }
        } else {
            $message = 'not found';
        }
        return [
            'product' => $product,
            'message' => $message,
        ];

    }

    public function delete($id)
    {
        $product = Product::find($id);
        if ($product) {
            if (Auth::user()->hasRole('admin')){
                $product->delete();
                $message = 'product deleted successfully';
            }else{
                $product = null;
                $message = 'you do not have access';
            }
        } else {
            $message = 'not found';
        }
        return [
            'product' => $product,
            'message' => $message,
        ];

    }


    public function getProductsByCategory($categoryId)
    {
        $products = Product::where('category_id', $categoryId)->get();
        $products ? $message = 'getting products successfully' : $message = 'there are no products for this category';
        return [
            'products' => $products,
            'message' => $message,
        ];
    }

    public function getProductsByBrand($brandId)
    {
        $products = Product::where('brand_id', $brandId)->get();
        $products ? $message = 'getting products successfully' : $message = 'there are no products for this brand';
        return [
            'products' => $products,
            'message' => $message,
        ];
    }

    public function searchProducts($query)
    {
        $products = Product::where('name', 'like', "%{$query}%")->get();
        $products ? $message = 'getting products successfully' : $message = 'no results';
        return [
            'products' => $products,
            'message' => $message,
        ];
    }

// تحتاج للتعديل
    public function getPopularProducts($limit = 5)
    {
        return Product::orderBy('quantity', 'desc')->take($limit)->get();
    }

    public function getProductsByCategoryAndBrand($categoryId, $brandId = null)
    {
            $query = Product::where('category_id', $categoryId);

            if ($brandId) {
                $query->where('brand_id', $brandId);
            }

            $products = $query->get();

            $products ? $message = 'getting products successfully' : $message = 'there are no products for this section';
            return [
                'products' => $products,
                'message' => $message,
            ];
    }
}
