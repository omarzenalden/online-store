<?php

namespace App\Repositories\brands;

use App\Models\Brand;
use App\Repositories\brands\BrandRepositoryInterface;
use Illuminate\Support\Facades\Auth;

class BrandRepository implements BrandRepositoryInterface
{
    public function all()
    {
        $brands = Brand::query()
        ->select("name")
        ->paginate(50);
        if ($brands->isEmpty()) {
            $message = 'not found';
        }else{
            $message = 'getting brands successfully';
        }
        return [
            'brands' => $brands,
            'message' => $message
        ];
    }

    public function find($id)
    {
            $brand = Brand::find($id);
            if (!$brand) {
                $message = 'not found';
            }else{
                $message = 'getting brand successfully';
            }
            return [
                'brand' => $brand,
                'message' => $message
            ];
    }

    public function create(array $attributes)
    {
        if (Auth::user()->hasRole('admin')){
            $brand = Brand::create($attributes);
            $message = 'brand created successfully';
        }else{
            $brand = null;
            $message = 'you do not have access';
        }
        return [
            'brand' => $brand,
            'message' => $message,
        ];
    }

    public function update($id, array $attributes)
    {
            $brand = brand::find($id);

            if ($brand) {
                if (Auth::user()->hasRole('admin')) {
                    $message = 'brand updated successfully';
                    $brand->update($attributes);
                }else{
                    $brand = null;
                    $message = 'you do not have access';
                }
            }else {
                $message = 'not found';
            }
        return [
            'brand' => $brand,
            'message' => $message
        ];
    }

    public function delete($id)
    {

            $brand = Brand::find($id);
            if ($brand) {
                if (Auth::user()->hasRole('admin')) {
                    $brand->delete();
                    $message = 'brand deleted successfully';
                }else{
                    $brand = null;
                    $message = 'you co not have access';
                }
            }else {
                    $message = 'not found';
                }
            return [
                'brand' => $brand,
                'message' => $message
            ];
    }
    public function searchBrand($query)
    {
            $brands=Brand::where('name', 'like', "%{$query}%")->get();
            if ($brands->isEmpty()) {
                $message = 'no result';
            }else{
                $message = 'getting successfully';
            }
            return [
                'brands' => $brands,
                'message' => $message
            ];
    }
    public function getBrandByCategory($categoryId){

        $brands=Brand::where('category_id', $categoryId)->get();
        if ($brands->isEmpty()) {
            $message = 'there is no brand for this category';
        }else{
            $message = 'getting brands successfully';
        }
        return [
            'brands' => $brands,
            'message' => $message
        ];
    }


}
