<?php

namespace App\Http\Controllers;

use App\Http\Requests\categoryRequest;
use App\Http\Responses\Response;
use App\Repositories\categories\CategoryRepositoryInterface;
use Exception;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    protected $categoryRepository;

    public function __construct(CategoryRepositoryInterface $categoryRepository)
    {
        $this->categoryRepository = $categoryRepository;
    }

    public function GetAllCategories()
    {
        $data = [];
        try {
            $data = $this->categoryRepository->all();
            return Response::Success($data['categories'],$data['message']);
        }catch(Exception $e){
            $message = $e->getMessage();
            return Response::Error($data,$message);
        }
    }

    public function GetOneCategory($id)
    {
        $data = [];
        try {
            $data = $this->categoryRepository->find($id);
            return Response::Success($data['category'],$data['message']);
        }catch(Exception $e){
            $message = $e->getMessage();
            return Response::Error($data,$message);
        }
    }

    public function CreateCategory(categoryRequest $request)
    {
        $data = [];
        try {
            $data = $this->categoryRepository->create($request->validated());
            return Response::Success($data['category'],$data['message']);
        }catch(Exception $e){
            $message = $e->getMessage();
            return Response::Error($data,$message);
        }
    }

    public function UpdateCategory(categoryRequest $request, $id)
    {
        $data = [];
        try {
            $data = $this->categoryRepository->update($id,$request->validated());
            return Response::Success($data['category'],$data['message']);
        }catch(Exception $e){
            $message = $e->getMessage();
            return Response::Error($data,$message);
        }
    }

    public function DestroyCategory($id)
    {
        $data = [];
        try {
            $data = $this->categoryRepository->delete($id);
            return Response::Success($data['category'],$data['message']);
        }catch(Exception $e){
            $message = $e->getMessage();
            return Response::Error($data,$message);
        }
    }
    public function SearchCategory(Request $request)
    {
        $data = [];
        $query = $request->input('query');
        try {
            $data = $this->categoryRepository->searchCategory($query);
            return Response::Success($data['categories'], $data['message']);
        } catch (Exception $e) {
            $message = $e->getMessage();
            return Response::Error($data, $message);
        }
    }
}
