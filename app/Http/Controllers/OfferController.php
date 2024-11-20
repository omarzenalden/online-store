<?php

namespace App\Http\Controllers;

use App\Http\Requests\AddProductsToOfferRequest;
use App\Http\Requests\OfferRequest;
use App\Http\Responses\Response;
use App\Model\Product;
use App\Models\Offer;
use App\Repositories\offers\OfferRepositoryInterface;
use Exception;
use Illuminate\Http\Request;


class OfferController extends Controller
{
    protected OfferRepositoryInterface $OfferRepository;

    public function __construct(OfferRepositoryInterface $OfferRepository)
    {
        $this->OfferRepository = $OfferRepository;
    }


    public function ShowAllOffers()
    {
        $data = [];
        try {
            $data = $this->OfferRepository->getAllOffers();
            return Response::Success($data['offers'],$data['message']);
        }catch(Exception $e){
            $message = $e->getMessage();
            return Response::Error($data,$message);
        }
    }

    public function CreateOffer(OfferRequest $request)
    {

        $data = [];
        try {
            $data = $this->OfferRepository->createOffer($request->validated());
            return Response::Success($data['offer'],$data['message']);
        }catch(Exception $e){
            $message = $e->getMessage();
            return Response::Error($data,$message);
        }
    }
    public function ShowOneOffer($id)
{
    $data = [];
    try {
        $data = $this->OfferRepository->findOfferById($id);
        return Response::Success($data['offer'],$data['message']);
    }catch(Exception $e){
        $message = $e->getMessage();
        return Response::Error($data,$message);
    }
}

    public function UpdateOffer(OfferRequest $request, $id)
    {
        $data = [];
        try {
            $data =  $this->OfferRepository->updateOffer($id, $request->validated());
            return Response::Success($data['offer'],$data['message']);
        }catch(Exception $e){
            $message = $e->getMessage();
            return Response::Error($data,$message);
        }
    }

    public function DestroyOffer($id)
    {
        $data = [];
        try {
            $data =$this->OfferRepository->deleteOffer($id);
            return Response::Success($data['offer'],$data['message']);
        }catch(Exception $e){
            $message = $e->getMessage();
            return Response::Error($data,$message);
        }
    }
    public function SearchOffer(Request $request)
    {
        $data = [];
        $query = $request->input('query');
        try {
            $data = $this->OfferRepository->searchOffer($query);
            return Response::Success($data['offers'], $data['message']);
        } catch (Exception $e) {
            $message = $e->getMessage();
            return Response::Error($data, $message);
        }
    }
    public function AddProductsForOffer(AddProductsToOfferRequest $request, Offer $offer)
    {
        $validatedData = $request->validated();
        $data = [];
        try {
            $data = $this->OfferRepository->addProducts($offer, $validatedData['products']);
            return Response::Success($data['offers'],$data['message']);
        }catch(Exception $e){
            $message = $e->getMessage();
            return Response::Error($data,$message);
        }
     }
    }
