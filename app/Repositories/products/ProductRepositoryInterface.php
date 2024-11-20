<?php

namespace App\Repositories\products;

interface ProductRepositoryInterface
{
    public function all();
    public function find($id);
    public function create(array $attributes);
    public function update($id, array $attributes);
    public function delete($id);
    public function getProductsByCategoryAndBrand($brandId);
    public function getProductsByBrand($brandId);
    public function getProductsByCategory($categoryId);
    public function searchProducts($query);
    public function getPopularProducts($limit = 5);
}
