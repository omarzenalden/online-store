<?php

namespace App\Http\Controllers;

use App\Http\Requests\Auth\UserSignInRequest;
use App\Http\Requests\Auth\UserSignUpRequest;
use App\Http\Requests\Profile\UpdatePasswordRequest;
use App\Http\Requests\Profile\UpdateProfileRequest;
use App\Http\Requests\ResetPassword\CheckCodeRequest;
use App\Http\Requests\ResetPassword\ForgetPasswordRequest;
use App\Http\Requests\ResetPassword\ResetPasswordRequest;
use App\Http\Responses\Response;
use App\Jobs\SendResetCodeEmail;
use App\Models\ResetCodePassword;
use App\Models\User;
use App\Repositories\ProfileRepositoryInterface;
use App\Services\UserService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use Laravel\Socialite\Facades\Socialite;
use Throwable;

class AuthController extends Controller
{
    protected $profileRepository;
    private UserService $userService;

    public function __construct(UserService $userService, ProfileRepositoryInterface $profileRepository)
    {
        $this->userService = $userService;
        $this->profileRepository = $profileRepository;
    }

    public function register_as_client(UserSignUpRequest $request):JsonResponse
    {
        $data = [];
        try {
            $data = $request->validated();
            $data['password'] = Hash::make($request['password']);
            $data = $this->userService->register_as_client($data);
            return Response::Success($data['user'] , $data['message']);
        }catch (Throwable $th){
            $message = $th->getMessage();
            return Response::Error($data,$message);
        }
    }
    public function register_as_seller(UserSignUpRequest $request):JsonResponse
    {
        $data = [];
        try {
            $data = $request->validated();
            $data['password'] = Hash::make($request['password']);
            $data = $this->userService->register_as_seller($data);
            return Response::Success($data['user'] , $data['message']);
        }catch (Throwable $th){
            $message = $th->getMessage();
            return Response::Error($data,$message);
        }
    }

    public function login(UserSignInRequest $request):JsonResponse
    {
        $data = [];
        try {
            $data = $this->userService->login($request->validated());
            return Response::Success($data['user'] , $data['message']);
        }catch(Throwable $th){
            $message = $th->getMessage();
            return Response::Error($data , $message);
        }
    }

    public function logout():JsonResponse
    {
        $data = [];
        try {
            $data = $this->userService->logout();
            return Response::Success($data['user'] , $data['message']);
        }catch(Throwable $th){
            $message = $th->getMessage();
            return Response::Error($data , $message);
        }
    }

    /////////////////////////////////////////////////////////
    //for test session

    public function index()
    {
        return view('welcome');
    }
    public function showForgetPasswordForm()
    {
        return view('forget_password');
    }

    public function showCheckCodeForm()
    {
        return view('check_code');
    }
    public function showResetPasswordForm()
    {
        return view('reset_password');
    }

    public function forget_password(ForgetPasswordRequest  $request)
    {

        $email = $request['email'];

        // Store email in session or cache

        $request->session()->regenerate();
        Session::put('reset_email', $email);
        //Cache::put('reset_email', $email, now()->addHour());

        ResetCodePassword::query()
            ->where('email' , $email)
            ->delete();

        $code = random_int(100000,999999);

        ResetCodePassword::query()->create([
            'email' => $email,
            'code' => $code,
            'created_at' => now(),
        ]);

        $toEmail = $request['email'];
        $subject = "Reset Password";

        SendResetCodeEmail::dispatch($email,$code,$subject);
        return redirect()->route('password.code')->with('status', 'Reset code has been sent successfully to your email.');

    }

    public function resend_code()
    {
        $email = Session::get('reset_email');
        $subject = 'Reset Password';
        $code = random_int(100000,999999);

        // Optional: Add time check before resending the code
        $lastSent = Session::get('last_code_sent_at');
        if ($lastSent && now()->diffInMinutes($lastSent) < 5) {
            return redirect()->route('password.code')->withErrors(['error' => 'You must wait 5 minutes before requesting a new code.']);
        }

        SendResetCodeEmail::dispatch($email,$code,$subject);
        ResetCodePassword::query()
            ->where('email' , $email)
            ->update(['code' => $code]);

        // Store the time the code was resent
        Session::put('last_code_sent_at', now());

        return redirect()->route('password.code')->with('status', 'Reset code has been sent successfully to your email.');
    }

    //        return [
        //            'user' => $email,
        //            'message' => $message,
        //        ];


    public  function check_code(CheckCodeRequest $request)
    {
        // Retrieve email from session or cache
        $email = Session::get('reset_email');
        //$email = Cache::get('reset_email');

        if (!is_null($email)){
            $resetCode = ResetCodePassword::query()
                ->where('code', $request['code'])
                ->where('email' , $email)
                ->first();
            if (!is_null($resetCode)) {
                if ($resetCode['created_at'] > now()->addHour()){
                    $resetCode->delete();
                    return redirect()->back()->withErrors(['code' => 'This code has expired.']);
                } else {
                    return redirect()->route('password.reset')->with('status', 'The code is correct, you can now reset your password.');
                }
            }else{
                return redirect()->back()->withErrors(['code' => 'Invalid code or email.']);
            }
        }else{

            return redirect()->back()->withErrors(['code' => 'you can not reset the password right now']);
        }
    }


    public function reset_password(ResetPasswordRequest $request)
    {

        // Retrieve email from session or cache
        $email = Session::get('reset_email');
        //$email = Cache::get('reset_email');

        $user = User::query()
            ->where('email' , $email)
            ->first();
        if (!is_null($email)){
            if (Hash::check($request['password'], $user->password)){
                return redirect()->back()->withErrors(['password' => 'The new password cannot be the same as the old password.']);
            }else {
                User::query()
                    ->update([
                        'password' => Hash::make($request['password']),
                    ]);
                ResetCodePassword::query()
                    ->where('email' , $email)
                    ->delete();
            }
        }else{
            return redirect()->back()->withErrors(['code' => 'you can not reset the password right now']);

        }
        return redirect()->route('index')->with('status', 'New password has been set successfully. You can now log in.');

    }
    //end test session
    /////////////////////////////////////////////////////////

//the main api

//    public function forget_password(ForgetPasswordRequest $request)
//    {
//        $data = [];
//        try {
//            $data = $this->userService->forget_password($request);
//            return Response::Success($data['user'] , $data['message']);
//        }catch(Throwable $th){
//            $message = $th->getMessage();
//            return Response::Error($data , $message);
//        }
//    }
//
//    public function check_code(CheckCodeRequest $request)
//    {
//        $data = [];
//        try {
//            $data = $this->userService->check_code($request);
//            return Response::Success($data['code'] , $data['message']);
//        }catch(Throwable $th){
//            $message = $th->getMessage();
//            return Response::Error($data , $message);
//        }
//    }
//
//    public function reset_password(ResetPasswordRequest $request)
//    {
//        $data = [];
//        try {
//            $data = $this->userService->reset_password($request);
//            return Response::Success($data['data'] , $data['message']);
//        }catch(Throwable $th){
//            $message = $th->getMessage();
//            return Response::Error($data , $message);
//        }
//    }


    public function redirect_to_google()
    {
        return Socialite::driver('google')->redirect();
    }

    public function google_handle_call_back()
    {
        $data = [];
        try {
            $data = $this->userService->google_handle_call_back();
            return Response::Success($data['data'] , $data['message']);
        }catch(Throwable $th){
            $message = $th->getMessage();
            return Response::Error($data , $message);
        }
    }

    public function redirect_to_apple()
    {
        return Socialite::driver('apple')->redirect();
    }

    public function apple_handle_call_back()
    {
        $data = [];
        try {
            $data = $this->userService->apple_handle_call_back();
            return Response::Success($data['data'] , $data['message']);
        }catch(Throwable $th){
            $message = $th->getMessage();
            return Response::Error($data , $message);
        }
    }

    public function show_all_profiles(): JsonResponse
    {
        $data = [];
        try {
            $data = $this->profileRepository->show_all_profiles();
            return Response::Success($data['users'],$data['message']);
        }catch(Exception $e){
            $message = $e->getMessage();
            return Response::Error($data, $message);
        }
    }
    public function show_user_profile($id):JsonResponse
    {
        $data = [];
        try {
            $data = $this->profileRepository->show_profile($id);
            return Response::Success($data['user'],$data['message']);
        }catch(Exception $e){
            $message = $e->getMessage();
            return Response::Error($data, $message);
        }
    }

    public function update_profile(UpdateProfileRequest $request):JsonResponse
    {
        $user = Auth::user();
        $data = [];
        try {
            $data =  $request->validated();
                $imagePath = $request->file('profile_photo_path')->store('images', 'public');
                $imageUrl = Storage::disk('public')->path($imagePath);
                $data['profile_photo_path'] = $imageUrl;
            $data = $this->profileRepository->update_profile($user,$data);
            return Response::Success($data['user'],$data['message']);
        }catch(Exception $e){
            $message = $e->getMessage();
            return Response::Error($data, $message);
        }
    }

    public function update_password(UpdatePasswordRequest $request): JsonResponse
    {
        $data = [];
        try {
            $user = Auth::user();
            $data = $this->profileRepository->update_password($user, $request->new_password);
            return Response::Success($data['user'],$data['message']);
        }catch (Exception $e){
            $message = $e->getMessage();
            return Response::Error($data,$message);
        }
    }

    public function delete_my_profile(): JsonResponse
    {
        $data = [];
        try {
            $user = Auth::user();
            $data = $this->profileRepository->delete_my_profile($user);
            return Response::Success($data['user'],$data['message']);
        }catch(Exception $e){
            $message = $e->getMessage();
            return Response::Error($data,$message);
        }
    }

    public function delete_profile($id): JsonResponse
    {
        $data = [];
        try {
            $data = $this->profileRepository->delete_profile($id);
            return Response::Success($data['user'],$data['message']);
        }catch(Exception $e){
            $message = $e->getMessage();
            return Response::Error($data,$message);
        }
    }
}
