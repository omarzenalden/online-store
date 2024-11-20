<?php

namespace App\Http\Controllers;
use App\Http\Requests\BrandRequest;
use App\Http\Responses\Response;
use App\Repositories\brands\BrandRepositoryInterface;
use Exception;
use Illuminate\Http\Request;

class BrandController extends Controller
{

    protected BrandRepositoryInterface $BrandRepository;
    public function __construct(BrandRepositoryInterface $BrandRepository)
    {
        $this->BrandRepository = $BrandRepository;
    }

    public function GetAllBrand(Request $request)
    {
        $data = [];
        try {
            $data = $this->BrandRepository->all();
            return Response::Success($data['brands'],$data['message']);
        }catch (Exception $e){
            $message = $e->getMessage();
            return Response::Error($data,$message);
        }
    }

    public function GetOneBrand($id)
    {
        $data = [];
        try {
            $data = $this->BrandRepository->find($id);
            return Response::Success($data['brand'],$data['message']);
        }catch (Exception $e){
            $message = $e->getMessage();
            return Response::Error($data,$message);
        }
    }



    public function CreateBrand(BrandRequest $request)
    {
        $data = [];
        try {
            $data = $this->BrandRepository->create($request->validated());
            return Response::Success($data['brand'],$data['message']);
        }catch (Exception $e){
            $message = $e->getMessage();
            return Response::Error($data,$message);
        }
    }



    public function UpdateBrand(BrandRequest $request, $id)
    {
        $data = [];
        try {
            $data = $this->BrandRepository->update($id,$request->validated());
            return Response::Success($data['brand'],$data['message']);
        }catch (Exception $e){
            $message = $e->getMessage();
            return Response::Error($data,$message);
        }
    }

    public function DestroyBrand($id)
    {
        $data = [];
        try {
            $data = $this->BrandRepository->delete($id);
            return Response::Success($data['brand'],$data['message']);
        }catch (Exception $e){
            $message = $e->getMessage();
            return Response::Error($data,$message);
        }
    }
    public function SearchForBrand(Request $request)
    {
        $data = [];
        try {
            $query = $request->input('query');
            $data = $this->BrandRepository->searchBrand($query);
            return Response::Success($data['brands'],$data['message']);
        }catch (Exception $e){
            $message = $e->getMessage();
            return Response::Error($data,$message);
        }
     }
     public function GetBrandByCategory($categoryId)
     {
         $data = [];
         try {
             $data = $this->BrandRepository->getBrandByCategory($categoryId);
             return Response::Success($data['brands'],$data['message']);
         }catch (Exception $e){
             $message = $e->getMessage();
             return Response::Error($data,$message);
         }
    }
}
