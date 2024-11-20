<?php

namespace App\Repositories\products;

interface ProductPhotoRepositoryInterface
{
    public function create(array $data,int $productId);
    public function update(int $id, array $data);
    public function delete(int $id);
    public function getById(int $id);
    public function getPhotosByProductId(int $productId);

}
