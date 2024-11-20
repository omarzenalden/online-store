<?php

namespace App\Repositories\offers;
use App\Models\Offer;

interface OfferRepositoryInterface
{
    public function getAllOffers();
    public function createOffer(array $data);
    public function updateOffer(int $id, array $data);
    public function deleteOffer(int $id);
    public function findOfferById(int $id);
    public function searchOffer($query);
    public function addProducts(Offer $offer, array $products);

}
