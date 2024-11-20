<?php
namespace App\Repositories;
use App\Models\User;
use App\Services\UserService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Testing\Fluent\Concerns\Has;

class ProfileRepository implements ProfileRepositoryInterface
{
    protected $userService;
    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }
    public function show_all_profiles():array
    {
        if (Auth::user()->hasRole('admin')) {

                /*
                    for 10000 users:
                    users: 50 user per page
                    speed: about 200ms for every page
                    memory usage: 10kb for every page
                    same fast but better efficient for memory
                */
            $users = User::query()
                ->select('id' , 'name' , 'email', 'profile_photo_path' , 'referral_code', 'referred_by_code')
                ->paginate(50);

            /*
                for 10000 users:
                users: all users together
                speed: about 200ms total
                memory usage: 1.25mb for all users
                can't append roles in the result
            */
    //        $users = DB::table('users')
    //            ->select('id' , 'name' , 'email' , 'profile_photo_path' , 'referral_code', 'referred_by_code')
    //            ->get();

            if ($users){
                    foreach ($users as $user) {
                        $user->load('roles', 'permissions');
                        $user = $this->userService->appendRolesAndPermissions($user);
                    }
                    $message = 'getting all users successfully';
            }else{
                $users = null;
                $message = 'there is no users at the moment';
            }
        }else{
            $users = null;
            $message = 'only admin can reach for this page';
        }
        return [
            'users' => $users,
            'message' => $message,
        ];
    }
    public function show_profile($id): array
    {
        $user = User::query()->
        where('id' , $id)
        ->select('id' , 'name' , 'email' , 'profile_photo_path' , 'referral_code', 'referred_by_code')
        ->first();
        if (!is_null($user)) {
            $user->load('roles', 'permissions');
            $user = $this->userService->appendRolesAndPermissions($user);
            $message = 'getting user successfully';
        }else{
            $user = null;
            $message = 'the user not found';
        }
        return [
            'user' => $user,
            'message' => $message,
        ];
    }

    public function update_profile($user, $data):array
    {
            $user = Auth::user();
            User::query()
                ->where('id' , Auth::id())->update($data);
            $user->refresh();
            $message = 'profile updated successfully';
        return[
            'user' => $user,
            'message' => $message,
        ];
    }


    public function update_password($user, $newPassword)
    {
        $user->password = Hash::make($newPassword);
        $user->save();
        $message = 'Password updated successfully';
        return [
            'user' => $user,
            'message' => $message,
        ];
    }

    public function delete_my_profile($user):array
    {
        //should I delete all related data in another tables for this user
        $user = Auth::user();
            $user->delete();
            $message = 'user deleted successfully';
        return [
            'user' => $user,
            'message' => $message,
        ];
    }

    public function delete_profile($id)
    {
        //should I delete all related data in another tables for this user
        $user = User::query()->find($id);
        if ($user){
            if (Auth::user()->hasRole('admin')){
            $user->delete();
            $message = 'user deleted successfully';
            }else{
                $user = null;
                $message = 'only admin can reach for this page';
            }
        }else{
            $user = null;
            $message = 'user not found';
        }
        return [
            'user' => $user,
            'message' => $message,
        ];
    }
}
