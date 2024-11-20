<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProductPhotoRequest;
use App\Http\Responses\Response;
use App\Repositories\products\ProductPhotoRepositoryInterface;
use Exception;

class ProductPhotoController extends Controller
{
    protected ProductPhotoRepositoryInterface $productPhotoRepository;

    public function __construct(ProductPhotoRepositoryInterface $productPhotoRepository)
    {
        $this->productPhotoRepository = $productPhotoRepository;
    }

    public function CreatePhotoesProduct(ProductPhotoRequest $request,$productId)
    {
        $data=[];
        try {
            $data = $request->validated();
            $productPhoto = $this->productPhotoRepository->create($data, $productId);
            return Response::Success($data['productPhoto'],$data['message']);

        } catch (Exception $e) {
            $message = $e->getMessage();
            return Response::Error($data,$message);

        }

    }

    public function UpdatePhotoesProduct(ProductPhotoRequest $request, $id)
    {
        try {
            $data = $request->validated();
            $productPhoto = $this->productPhotoRepository->update($id, $data);
            return response()->json($productPhoto);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to update product photo', 'message' => $e->getMessage()], 500);
        }
    }

    public function DeletePhotoesProduct($id)
    {
        try {
            $this->productPhotoRepository->delete($id);
            return response()->json(['message' => 'Product photo deleted successfully']);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to delete product photo', 'message' => $e->getMessage()], 500);
        }
    }

    public function ShowOnePhotoesProduct($id)
    {
        try {
            $productPhoto = $this->productPhotoRepository->getById($id);
            return response()->json($productPhoto);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to retrieve product photo', 'message' => $e->getMessage()], 500);
        }

    }
    public function GetPhotosByProduct($productId)
    {
        try {
            $photos = $this->productPhotoRepository->getPhotosByProductId($productId);
            return response()->json($photos);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to retrieve product photos', 'message' => $e->getMessage()], 500);
        }
    }
}
