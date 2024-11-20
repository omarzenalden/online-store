<?php

namespace App\Repositories\brands;

interface BrandRepositoryInterface
{
    public function all();
    public function find($id);
    public function create(array $attributes);
    public function update($id, array $attributes);
    public function delete($id);
    public function searchBrand($id);
    public function getBrandByCategory($id);
}
