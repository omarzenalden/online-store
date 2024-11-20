<?php

namespace App\Repositories\reviews;

interface ReviewRepositoryinterface
{
    public function getAll();

    public function create(array $data);

    public function update(array $data, $id);

    public function delete($id);

    public function findById($id);

    public function getReviewsByProductId($productId);

    public function getReviewsByUserId($userId);

    public function getCommentsByTime($productId, $sortOrder = 'desc');

    public function getCommentsByMostLikes($productId);

    public function getCommentsByLessLikes($productId);

    public function addLikeOrDislike(array $data, $reviewId, $userId);

    public function deleteLikeOrDislike($reviewId, $userId);


}
