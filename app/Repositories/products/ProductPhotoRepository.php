<?php
namespace App\Repositories\products;
use App\Models\ProductPhoto;
use Illuminate\Support\Facades\Auth;

class ProductPhotoRepository implements ProductPhotoRepositoryInterface
{
    public function create(array $data, int $productId)
    {    if (Auth::check() && Auth::user()->hasRole('admin')){
        $photos = [];
        foreach ($data['photos'] as $photo) {
            $path = $photo->store('product_photos', 'public');
            $photos[] = ProductPhoto::create([
                'product_photo_path' => $path,
                'product_id' => $productId,
            ]);
        }
        $message='photoes add successfully';
    }else{
        $photos = null;
        $message = 'you do not have access';
    }
        return [
            'product' => $photos,
            'message' => $message,
        ];



    }

       public function update(int $id, array $data)
    {
        $productPhoto = ProductPhoto::query()->find($id);
        $productPhoto->update($data);
        return $productPhoto;
    }

    public function delete(int $id)
    {
        $productPhoto = ProductPhoto::find($id);
        if ($productPhoto){
            if(Auth::check() && Auth::user()->hasRole('admin')){
                $productPhoto->delete();
                $message='delete successfully ';
            }else{
                $productPhoto=null;
                $message='you dont have access';
            }

        }else{
            $message='not found';
        }
        return [
            'photoies'=> $productPhoto,
            'message'=>$message
        ];

    }

    public function getById(int $id)
    {
      $photo=   ProductPhoto::query()->find($id);
      $photo ? $message='get success fully': $message='not found';
        return [
            'photo'=> $photo,
            'message'=>$message
        ];
    }
    public function getPhotosByProductId(int $productId)
    {
        $photo= ProductPhoto::query()->where('product_id', $productId)->get();
        $photo ? $message='get photo successfully':$message="there are not found for this product  ";
        return [
            'photo'=> $photo,
            'message'=>$message
        ];
    }
}
