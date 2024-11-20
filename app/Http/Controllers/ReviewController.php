<?php

namespace App\Http\Controllers;

//use App\Http\Controllers\Api\Exception;
use App\Http\Requests\ReviewRequest;
use App\Http\Responses\Response;
use App\Repositories\reviews\ReviewRepositoryinterface;
use Exception;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    protected ReviewRepositoryinterface $reviewRepository;

    public function __construct(ReviewRepositoryinterface $reviewRepository)
    {
        $this->reviewRepository = $reviewRepository;

    }
    public function GetAllReviews()
    {

        $data = [];
        try{
            $data = $this->reviewRepository->getAll();
            return Response::Success($data['reviews'],$data['message']);
        }catch(Exception $e){
            $message = $e->getMessage();
            return Response::Error($data,$message);
        }
    }
    public function CreateReview(ReviewRequest $request)
    {

        $data = [];
        try{
            $data = $this->reviewRepository->create($request->validated());
            return Response::Success($data['review'],$data['message']);
        }catch(Exception $e){
            $message = $e->getMessage();
            return Response::Error($data,$message);
        }
    }
    public function UpdateReview(ReviewRequest $request, $id)
    {

        $data = [];
        try{
            $data = $this->reviewRepository->update($request->validated(),$id);
            return Response::Success($data['Review'],$data['message']);
        }catch(Exception $e){
            $message = $e->getMessage();
            return Response::Error($data,$message);
        }
    }
    public function DestroyReview($id)
    {
        $data = [];
        try{
            $data = $this->reviewRepository->delete($id);
            return Response::Success($data['product'],$data['message']);
        }catch(Exception $e){
            $message = $e->getMessage();
            return Response::Error($data,$message);
        }
    }
    public function GetOneReview($id){

        $data = [];
        try{
            $data = $this->reviewRepository->findById($id);
            return Response::Success($data['review'],$data['message']);
        }catch(Exception $e){
            $message = $e->getMessage();
            return Response::Error($data,$message);
        }
    }
    //get the reviewes that return to userId
    public function GetByUserId($userId)
    {
        $reviews = $this->reviewRepository->getReviewsByUserId($userId);
        return response()->json(['reviews' => $reviews]);
    }
//get the reviews that is on the product Id
    public function GetByProductId($productId)
    {
        $reviews = $this->reviewRepository->getReviewsByProductId($productId);
        return response()->json(['reviews' => $reviews]);
    }
//get the comments by desc
    public function SortCommentsByTime($productId, Request $request)
    {
        $sortOrder = $request->get('sort', 'desc'); // 'desc' by default
        $comments = $this->reviewRepository->getCommentsByTime($productId, $sortOrder);
        return response()->json(['comments' => $comments]);
    }
    //sort review on productid desc
        public function GetCommentsByMostLikes($productId)
        {
            return $this->reviewRepository->getCommentsByMostLikes($productId);
        }
    //sort review on productid asc

    public function GetCommentsByLessLikes($productId)
        {
            $comments = $this->reviewRepository->getCommentsByLessLikes($productId);
            return response()->json($comments);
        }
        //add or update likes and dislikes
        public function AddDislikeOrDislike(Request $request, $reviewId,$userId)
        {
            $data = $request->only(['like_status']);

            return $this->reviewRepository->addLikeOrDislike($data, $reviewId, $userId);
        }


//delete like or dislike
        public function DeleteLikeOrDislike( $reviewId,$userId)
        {
            return $this->reviewRepository->deleteLikeOrDislike($reviewId, $userId);
        }

    }
