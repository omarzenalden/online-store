<?php
namespace App\Services;

use App\Mail\ResetPasswordMail;
use App\Models\Cart;
use App\Models\ResetCodePassword;
use App\Models\User;
use App\Models\Wallet;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;
use Spatie\Permission\Models\Role;

class UserService{
    public function register_as_client($request):array
    {

        do {
            $referralCode = strtoupper(Str::random(8));
        } while (User::where('referral_code', $referralCode)->exists());

        $request['referral_code'] = $referralCode;
        $user = User::query()->create($request);

        Cart::query()->create([
           'user_id' => $user->id
        ]);
        Wallet::query()->create([
           'user_id' => $user->id,
        ]);

        $clientRole = Role::query()
            ->where('name' , '=' , 'client')
            ->first();

        $user->assignRole($clientRole);

        $permissions = $clientRole->permissions()->pluck('name')->toArray();
        $user->givePermissionTo($permissions);

        //show roles and permissions on response
        $user->load('roles' , 'permissions');

        //reload user instance to get updated roles and permissions
        $user = User::query()->find($user['id']);
        $user = $this->appendRolesAndPermissions($user);
        $user['token'] = $user->createToken("token")->plainTextToken;

        event(new Registered($user)); // Triggers and queue email verification

        $message = 'user has been registered successfully. A verification link has been sent to your email address. Please check your inbox.';
        return [
            'user' => $user,
            'message' => $message,
        ];
    }

    public function register_as_seller($request):array
    {
        do {
            $referralCode = strtoupper(Str::random(8));
        } while (User::where('referral_code', $referralCode)->exists());

        $request['referral_code'] = $referralCode;

        $user = User::query()->create($request);

        Cart::query()->create([
            'user_id' => $user->id
        ]);

        $clientRole = Role::query()
            ->where('name' , '=' , 'seller')
            ->first();

        $user->assignRole($clientRole);

        $permissions = $clientRole->permissions()->pluck('name')->toArray();
        $user->givePermissionTo($permissions);
        //show roles and permissions on response
        $user->load('roles' , 'permissions');

        //reload user instance to get updated roles and permissions
        $user = User::query()->find($user['id']);
        $user = $this->appendRolesAndPermissions($user);
        $user['token'] = $user->createToken("token")->plainTextToken;

        event(new Registered($user));
        $message = 'user has been registered successfully. A verification link has been sent to your email address. Please check your inbox.';
        return [
            'user' => $user,
            'message' => $message,
        ];
    }

    public function login($request):array
    {
        $user = User::query()
            ->where('email' , $request['email'])
            ->first();
        if (!is_null($user)){
        if (Auth::attempt(['email' => $request['email'], 'password' => $request['password']])) {
            $user = $this->appendRolesAndPermissions($user);
            $user['token'] = $user->createToken("token")->plainTextToken;
            $message = 'logged in successfully';
        }else{
            $user = [];
            $message = 'email and password do not match';
        }
        }else{
            $user = [];
            $message = 'user not found please sign up first';
        }
        return [
            'user' => $user,
            'message' => $message,
        ];
    }

    public function logout():array
    {
        $user = Auth::user();
        if (!is_null($user)){
            auth()->user()->tokens()->delete();
            $message = 'logged out successfully';
        }else{
            $user = [];
            $message = 'invalid token';
        }
        return [
            'user' => $user,
            'message' => $message,
        ];
    }

    //function to add roles and permissions to user array
    public function appendRolesAndPermissions($user)
    {
        $roles=[];
        foreach ($user->roles as $role){
            $roles []= $role->name;
        }
        unset($user['roles']);
        $user['roles']=$roles;

        $permissions=[];
        foreach ($user->permissions as $permission) {
            $permissions [] =$permission->name;
        }

        unset($user['permissions']);
        $user['permissions']=$permissions;

        return $user;
    }


    public function forget_password($request)
    {
        $email = $request['email'];

        //store email in session
        $request->session()->regenerate();
        Session::put('reset_email',$email);
        ResetCodePassword::query()
            ->where('email' , $email)
            ->delete();

        $code = random_int(100000,999999);

        ResetCodePassword::query()
            ->create([
                'code' =>  $code,
                'email' => $email,
            ]);
       // $message = "You Can Use This code To Reset Password";
        $subject = 'Reset Password';
        Mail::to($email)->send(new ResetPasswordMail($subject,$code));
        return redirect()->route('password.code')->with('status', 'Reset code has been sent successfully to your email.');
    }
//        return [
//            'user' => $email,
//            'message' => $message,
//        ];


    public function resend_code($request)
    {
        $email = Session::get('reset_email');
        $subject = 'Reset Password';
        $code = random_int(100000,999999);
        Mail::to($email)->send(new ResetPasswordMail($subject , $code));
        ResetCodePassword::query()
            ->where('email' , $email)
            ->update([
                'code' => $code,
            ]);
        return redirect()->route('password.code')->with('status', 'Reset code has been sent successfully to your email.');
        //        return [
        //            'user' => $email,
        //            'message' => $message,
        //        ];
    }

    public function check_code($request)
    {
        $email = Session::get('reset_email');
        $resetCode = $request['code'];
        ResetCodePassword::query()
            ->where('code' , $resetCode)
            ->where('email' , $email)
            ->first();
        if ($resetCode){
            if ($resetCode['created_at'] < now()->addHour()){
                $resetCode->delete();
              //      $code = '';
              //      $message = 'the code has expired';
                return redirect()->back()->withErrors(['code' => 'This code has expired.']);
            } else{
             //   $code = $resetCode;
               // $message = 'the code is correct';
                return redirect()->route('password.reset')->with('status', 'The code is correct, you can now reset your password.');

            }
        }else{
          //  $code = '';
          //  $message = 'Invalid code or email';
            return redirect()->back()->withErrors(['code' => 'Invalid code or email.']);
        }
//        return [
//            'code' => $code,
//            'message' => $message,
//        ];
    }

    public function reset_password($request)
    {
        $email = Session::get('reset_email');
        $user = User::query()
            ->where('email' , $email)
            ->first();
        if (Hash::check($user->password , $request['password'])){
          //  $data = '';
          //  $message = 'The new password cannot be the same as the old password';
            return redirect()->back()->withErrors(['password' => 'The new password cannot be the same as the old password.']);
        }else{
            User::query()
                ->update([
                    'password' => Hash::make($request['password'])
                ]);
            ResetCodePassword::query()
                ->where('email' , $email)
                ->delete();
        //    $data = $user;
        //    $message = 'New password has been set successfully. You can now log in';
        }
        return redirect()->route('login')->with('status', 'New password has been set successfully. You can now log in.');
        //return [
        //'data' => $data,
        //'message' => $message
        //];
    }


    public function google_handle_call_back()
    {
        // Retrieve user from Google
        $googleUser = Socialite::driver('google')->user();

        // Check if the user already exists in the database
        $findUser = User::where('email', $googleUser->email)->first();

        if ($findUser) {
            // Update only if needed (Google ID or social type has changed)
            if ($findUser->social_id !== $googleUser->id || $findUser->social_type !== 'google') {
                $findUser->update([
                    'social_id' => $googleUser->id,
                    'social_type' => 'google',
                ]);
            }
            $clientRole = Role::query()
                ->where('name' , '=' , 'client')
                ->first();

            $findUser->assignRole($clientRole);

            $permissions = $clientRole->permissions()->pluck('name')->toArray();
            $findUser->givePermissionTo($permissions);
            //show roles and permissions on response
            $findUser->load('roles' , 'permissions');

            //reload user instance to get updated roles and permissions
            $findUser = User::query()->find($findUser['id']);
            $findUser = $this->appendRolesAndPermissions($findUser);
            $findUser['token'] = $findUser->createToken("token")->plainTextToken;

            Auth::login($findUser);
            return [
                'data' => $findUser,
                'message' => 'logged in successfully',
            ];
        } else {
            $user = User::create([
                'name' => $googleUser->name,
                'email' => $googleUser->email,
                'social_id' => $googleUser->id,
                'social_type' => 'google',
                'password' => Hash::make(Str::random(8)),
            ]);

            $clientRole = Role::query()
                ->where('name' , '=' , 'client')
                ->first();

            $user->assignRole($clientRole);

            $permissions = $clientRole->permissions()->pluck('name')->toArray();
            $user->givePermissionTo($permissions);
            //show roles and permissions on response
            $user->load('roles' , 'permissions');

            //reload user instance to get updated roles and permissions
            $user = User::query()->find($user['id']);
            $user = $this->appendRolesAndPermissions($user);
            $user['token'] = $user->createToken("token")->plainTextToken;

            Auth::login($user);

            return [
                'data' => $user,
                'message' => 'logged in successfully',
            ];
        }
    }


    public function apple_handle_call_back()
    {
        // Retrieve user from Google
        $appleUser = Socialite::driver('apple')->user();

        // Check if the user already exists in the database
        $findUser = User::where('social_id', $appleUser->id)->first();

        if ($findUser) {
            // Log in the existing user
            Auth::login($findUser);
            return [
                'data' => $findUser,
                'message' => 'logged in successfully',
            ];
        } else {
            // Create a new user in the database
            $user = User::create([
                'name' => $appleUser->name,
                'email' => $appleUser->email,
                'social_id' => $appleUser->id, // Google ID
                'social_type' => 'apple',
                'password' => Hash::make(Str::random(8)),
            ]);

            $clientRole = Role::query()
                ->where('name' , '=' , 'client')
                ->first();

            $user->assignRole($clientRole);

            $permissions = $clientRole->permissions()->pluck('name')->toArray();
            $user->givePermissionTo($permissions);
            //show roles and permissions on response
            $user->load('roles' , 'permissions');

            //reload user instance to get updated roles and permissions
            $user = User::query()->find($user['id']);
            $user = $this->appendRolesAndPermissions($user);
            $user['token'] = $user->createToken("token")->plainTextToken;


            Auth::login($user);
            return [
                'data' => $user,
                'message' => 'logged in successfully',
            ];
        }

    }
}
