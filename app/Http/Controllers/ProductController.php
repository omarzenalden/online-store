<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProductRequest;
use App\Http\Responses\Response;
use App\Repositories\products\ProductRepositoryInterface;
use Exception;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    protected ProductRepositoryInterface $productRepository;
    public function __construct(ProductRepositoryInterface $productRepository)
    {
        $this->productRepository = $productRepository;
    }


    public function GetAllProducts()
    {
        $data = [];
        try{
           $data = $this->productRepository->all();
           return Response::Success($data['products'],$data['message']);
        }catch(Exception $e){
           $message = $e->getMessage();
           return Response::Error($data,$message);
        }
    }
    public function CreateProduct(ProductRequest $request)
    {
        $data = [];
        try{
            $data = $this->productRepository->create($request->validated());
            return Response::Success($data['product'],$data['message']);
        }catch(Exception $e){
            $message = $e->getMessage();
            return Response::Error($data,$message);
        }
    }


    public function GetOneProduct($id)
    {
        $data = [];
        try{
            $data = $this->productRepository->find($id);
            return Response::Success($data['product'],$data['message']);
        }catch(Exception $e){
            $message = $e->getMessage();
            return Response::Error($data,$message);
        }
    }



    public function UpdateProduct(ProductRequest $request, $id)
    {
        $data = [];
        try{
            $data = $this->productRepository->update($id,$request->validated());
            return Response::Success($data['product'],$data['message']);
        }catch(Exception $e){
            $message = $e->getMessage();
            return Response::Error($data,$message);
        }
     }


    public function DestroyProduct($id)
    {
        $data = [];
        try{
            $data = $this->productRepository->delete($id);
            return Response::Success($data['product'],$data['message']);
        }catch(Exception $e){
            $message = $e->getMessage();
            return Response::Error($data,$message);
        }
    }
    public function getByCategory($categoryId)
    {
        $data = [];
        try{
            $data = $this->productRepository->getProductsByCategory($categoryId);
            return Response::Success($data['products'],$data['message']);
        }catch(Exception $e){
            $message = $e->getMessage();
            return Response::Error($data,$message);
        }
      }
    public function getByBrand($brandId)
    {
        $data = [];
        try{
            $data = $this->productRepository->getProductsByBrand($brandId);
            return Response::Success($data['products'],$data['message']);
        }catch(Exception $e){
            $message = $e->getMessage();
            return Response::Error($data,$message);
        }
      }
    public function GetProductsByCategoryAndBrand($categoryId,$brandId=null)
    {
        $data = [];
        try{
            $data = $this->productRepository->getProductsByCategoryAndBrand($categoryId,$brandId);
            return Response::Success($data['products'],$data['message']);
        }catch(Exception $e){
            $message = $e->getMessage();
            return Response::Error($data,$message);
        }
      }

    public function SearchProduct(Request $request)
    {
        $query = $request->input('query');
        $data = [];
        try{
            $data = $this->productRepository->searchProducts($query);
            return Response::Success($data['products'],$data['message']);
        }catch(Exception $e){
            $message = $e->getMessage();
            return Response::Error($data,$message);
        }
     }

    public function GetPapular($limit = 5)
    {
        $data = [];
        try{
            $data = $this->productRepository->getPopularProducts($limit);
            return Response::Success($data['products'],$data['message']);
        }catch(Exception $e){
            $message = $e->getMessage();
            return Response::Error($data,$message);
        }
    }
}
