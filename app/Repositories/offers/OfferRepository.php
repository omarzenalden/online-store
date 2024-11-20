<?php

namespace App\Repositories\offers;
use Illuminate\Support\Facades\Auth;
use App\Models\Offer;
use App\Models\product;
use DB;


class OfferRepository implements OfferRepositoryInterface
{

    public function getAllOffers()
    {
        $offer = Offer::with('products')->get();
        $offer ? $message = 'getting all offers successfully' : $message = 'not found';
        return [
            'offers' => $offer,
            'message' => $message
        ];
    }

    public function createOffer(array $data)
    {
        if (Auth::user()->hasRole('admin')) {
        DB::beginTransaction();

            $offer = Offer::create($data);

             $productsData = [];
            foreach ($data['products'] as $product) {
                if (isset($product['id'])) {
                    $productsData[$product['id']] = ['quantity' => $product['quantity']];
                }
            }

            $offer->products()->attach($productsData);

            DB::commit();

            $offer = Offer::with('products')->find($offer->id);
            $message = 'offer created successfully';

    }else{
        $offer = null;
        $message = 'you do not have access';
    }
        return [
            'offer' =>  $offer,
            'message' => $message
        ];

    }

    public function updateOffer(int $id, array $data)
    {
        $offer = Offer::query()->find($id);
        if ($offer){
            if(Auth::user()>hasRole('admin')) {
                $offer->update($data);

                $productsData = [];
                foreach ($data['products'] as $product) {
                    $productsData[$product['id']] = ['quantity' => $product['quantity']];
                }

                $offer=$offer->products()->sync($productsData);
                $message = 'offer updated successfully';
            }else{
                $offer=null;
                $message = 'you do not have access';
            }
        }else{
            $message = 'not found';
        }
        return [
            'offer' =>  $offer,
            'message' => $message
        ];
    }

    public function deleteOffer(int $id)
    {
        $offer = Offer::find($id);
        if ($offer) {
            if (Auth::user()->hasRole('admin')){
                $offer->delete();
                $message = 'offer deleted successfully';
            }else{
                $offer = null;
                $message = 'you do not have access';
            }
        }else{
            $message = 'not found';
        }
        return [
            'offer' => $offer,
            'message' => $message
        ];
    }

    public function findOfferById(int $id)
    {
        $offer= Offer::with('products')->find($id);
        $offer ? $message = 'getting offer successfully' : $message = 'not found';
        return [
            'offer' => $offer,
            'message' => $message
        ];
    }
    public function searchOffer($query)
    {
        $offers = offer::where('name', 'like', "%{$query}%")->get();
        $offers ? $message = 'getting offers' : $message = 'there is no result';
        return [
            'offers' => $offers,
            'message' => $message
        ];
    }
    public function addProducts(Offer $offer, array $products)
    {
        if ($offer) {
            if (Auth::user()->hasRole('admin')) {
                foreach ($products as $productData) {
                    $offer->products()->sync([
                        $productData['id'] => ['quantity' => $productData['quantity']]
                    ]);
                }
                $offers = $offer->load('products');
                $message = 'Products added to offer successfully';
            } else {
                $offers = null;
                $message = 'you do not have access';
            }
            return [
                'offers' => $offers,
                'message' => $message
            ];
        }
        return [
            'offers' => null,
            'message' => 'This offer not found',
        ];
    }

    }

