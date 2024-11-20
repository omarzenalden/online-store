<?php

namespace App\Repositories\reviews;
use App\Models\Product;
use App\Models\review;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Repositories\reviews\ReviewRepositoryinterface;
class ReviewRepository implements ReviewRepositoryinterface
{

    public function getAll()
    {
        $rev= Review::all();
        $rev?$message= 'getting all reviews successfully':$message=' reviews not found';
        return[
          'reviews'=>$rev,
          'message'=>$message
        ];

    }

    public function create(array $data)
    {
//        if (Auth::check()&& Auth::user()->hasRole('seller')) {
            DB::beginTransaction();
            $rev = Review::create($data);
            DB::commit();
            $message = 'review created successfully';
//        }else{
//            $rev=null;
//            $message='you do not have access';
//        }
            return [
                'review' => $rev,
                'message' => $message,
            ];

    }

    public function update( array $data,$id)
    {
      $Review = review::query()->find($id);
        if ($Review) {
//            if(Auth::check()&& Auth::user()->hasRole('seller')){
                $Review->update($data);
                $message='review updated successfully';
//            }else{
//                $rev=null;
//                $message='you do not have access';

//            }
        }else{
            $message='review not found';

        }
        return [
            'Review' => $Review,
            'message' => $message,
        ];
    }
    public function delete($id)
    {
        $review = review::find($id);
        if ($review) {
            if (Auth::user()->hasRole('saller')){
                $review->delete();
                $message = 'review deleted successfully';
            }else{
                $review = null;
                $message = 'you do not have access';
            }
        } else {
            $message = 'not found';
        }
        return [
            'review' => $review,
            'message' => $message,
        ];
    }

    public function findById($id)
    {
        $review =  Review::query()->find($id);
        $review ? $message = 'getting review successfully' : $message = 'not found';
        return [
            'review' => $review,
            'message' => $message,
        ];
    }
//////////////////////
    public function getReviewsByProductId($productId)
    {
        $Review= Review::query()->where('product_id', $productId)->get();
        $Review?$message= 'getting  reviews successfully':$message=' reviews not found';
        return[
            'reviews'=>$Review,
            'message'=>$message
        ];
    }

    public function getReviewsByUserId($userId)
    {
        $Review= Review::query()->where('user_id', $userId)->get();
        $Review?$message= 'getting  reviews successfully':$message=' reviews not found';
        return[
            'reviews'=>$Review,
            'message'=>$message
            ];
    }
    public function getCommentsByTime($productId, $sortOrder = 'desc')
    {
        $Review= Review::query()->where('product_id', $productId)
            ->orderBy('created_at', $sortOrder)
            ->get(['comment', 'created_at']);
        $Review?$message= 'getting  reviews successfully':$message=' reviews not found';
        return[
            'reviews'=>$Review,
            'message'=>$message
        ];


    }

    public function getCommentsByMostLikes($productId)
    {
        $Review= Review::query()->where('product_id', $productId)
            ->orderBy('likes_count', 'desc') // ترتيب حسب عدد اللايكات تنازلياً
            ->get();
        $Review?$message= 'getting  reviews successfully':$message=' reviews not found';
        return[
            'reviews'=>$Review,
            'message'=>$message
        ];
    }
    public function getCommentsByLessLikes($productId)
    {
        $Review= Review::query()->where('product_id', $productId)
            ->orderBy('likes_count', 'asc') // ترتيب حسب عدد اللايكات تصاعدياً
            ->get();
        $Review?$message= 'getting  reviews successfully':$message=' reviews not found';
        return[
            'reviews'=>$Review,
            'message'=>$message
        ];
    }

    public function addLikeOrDislike( array $data,$reviewId, $userId)
    {

        $like_status = $data['like_status'];
//
//        // التحقق من الحالة المدخلة
//        if (!in_array($like_status, ['like', 'dislike'])) {
//            $message = 'Invalid like status';
//
////            return response()->json(['message' => 'Invalid like status'], 400);
//        }

        // الحصول على المراجعة
        $review = Review::query()->find($reviewId);


        // التحقق إذا كان المستخدم قد تفاعل مسبقًا مع هذه المراجعة
        $existingLike = DB::table('likes')
            ->where('review_id', $reviewId)
            ->where('user_id', $userId)
            ->first();

        if ($existingLike) {
            // إذا كان هناك سجل موجود، تحقق من الفرق بين الحالة القديمة والجديدة
            if ($existingLike->like_status === 'like' && $like_status === 'dislike') {
                // إذا كانت الحالة القديمة "like" وتم التبديل إلى "dislike"
                $review->decrement('likes_count');
                $review->increment('dislikes_count');
            } elseif ($existingLike->like_status === 'dislike' && $like_status === 'like') {
                // إذا كانت الحالة القديمة "dislike" وتم التبديل إلى "like"
                $review->decrement('dislikes_count');
                $review->increment('likes_count');
            }

            // تحديث الحالة في جدول likes
            DB::table('likes')
                ->where('review_id', $reviewId)
                ->where('user_id', $userId)
                ->update(['like_status' => $like_status, 'updated_at' => now()]);
            return response()->json(['message' => ucfirst($like_status) . ' updated successfully.']);
        } else {
            // إذا لم يكن هناك سجل، أضف سجل جديد
            if ($like_status === 'like') {
                $review->increment('likes_count');
            } elseif ($like_status === 'dislike') {
                $review->increment('dislikes_count');
            }

            DB::table('likes')->insert([
                'review_id' => $reviewId,
                'user_id' => $userId,
                'like_status' => $like_status,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            return response()->json(['message' => ucfirst($like_status) . ' added successfully.']);
        }
    }






    public function deleteLikeOrDislike($reviewId, $userId)
    {
            // التحقق مما إذا كان هناك سجل موجود
            $existingLike = DB::table('likes')
                ->where('review_id', $reviewId)
                ->where('user_id', $userId)
                ->first();

            if ($existingLike) {
                $review = Review::findOrFail($reviewId);

                // تقليل العدادات بناءً على الحالة الحالية
                if ($existingLike->like_status === 'like') {
                    $review->decrement('likes_count');
                } elseif ($existingLike->like_status === 'dislike') {
                    $review->decrement('dislikes_count');
                }

                // حذف السجل من جدول likes
                DB::table('likes')
                    ->where('review_id', $reviewId)
                    ->where('user_id', $userId)
                    ->delete();

                return response()->json(['message' => 'Like/Dislike removed successfully.']);
            }

        return response()->json(['message' => 'No like/dislike found for this review.'], 404);
    }
}
