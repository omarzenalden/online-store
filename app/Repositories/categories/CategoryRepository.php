<?php

namespace App\Repositories\categories;

use App\Models\Category;
use App\Repositories\categories\CategoryRepositoryInterface;
use Illuminate\Support\Facades\Auth;


class CategoryRepository implements CategoryRepositoryInterface
{
    public function all()
    {
        $categories = category::query()
        ->select("name")
        ->paginate(50);

        $categories ? $message = 'getting all categories successfully' : $message = 'not found';
        return [
            'categories' => $categories,
            'message' => $message
        ];
    }

    public function find($id){
       $category=Category::find($id);
       $category ? $message = 'getting category successfully' : $message = 'not found';
       return [
           'category' => $category,
           'message' => $message
       ];
    }

    public function create(array $attributes)
    {
            if (Auth::user()->hasRole('admin')){
                $category =  Category::query()->create($attributes);
                $message = 'category created successfully';
            }else{
                $category = null;
                $message = 'you do not have access';
            }
            return [
                'category' => $category,
                'message' => $message
            ];
    }

    public function update($id, array $attributes)
    {

        $category =category::find($id);
        if ($category) {
            if (Auth::user()->hasRole('admin')){
                $category->update($attributes);
                $message = 'category updated successfully';
            }else{
                $category = null;
                $message = 'you do not have access';
            }
        }else{
            $message = 'not found';
        }
        return [
            'category' => $category,
            'message' => $message
        ];
    }

    public function delete($id)
    {
            $category = Category::find($id);
            if ($category) {
                if (Auth::user()->hasRole('admin')){
                    $category->delete();
                    $message = 'category deleted successfully';
                }else{
                    $category = null;
                    $message = 'you do not have access';
                }
            }else{
                $message = 'not found';
            }
            return [
                'category' => $category,
                'message' => $message
            ];
    }

    public function searchCategory($query)
    {
            $categories = category::where('name', 'like', "%{$query}%")->get();
            $categories ? $message = 'getting categories' : $message = 'there is no result';
            return [
                'categories' => $categories,
                'message' => $message
            ];
    }
}
