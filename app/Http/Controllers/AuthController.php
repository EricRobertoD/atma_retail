<?php

namespace App\Http\Controllers;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use Illuminate\Auth\Events\Verified;

class AuthController extends Controller
{
    public function verify(Request $request) {
        $user = User::findOrFail($request->id);

        if (! hash_equals((string) $request->hash, sha1($user->getEmailForVerification()))) {
            return response()->json([
                "message" => "Unauthorized",
                "success" => false
            ], 403);
        }

        if ($user->hasVerifiedEmail()) {
            return response()->json([
                "message" => "User already verified!",
                "success" => false
            ]);
        }

        if ($user->markEmailAsVerified()) {
            event(new Verified($user));
        }

        return response()->json([
            "message" => "Email verified successfully!",
            "success" => true
        ]);
    }

    public function register(Request $request)
    {
        $registerData = $request->all();

        $validate = Validator::make($registerData, [
            'email' => 'required|string|email|max:255|unique:users',
            'name' => 'required',
            'password' => 'required',
            'no_telp' => 'required',
            'alamat' => 'required',
        ]);

        if ($validate->fails()) {
            $errors = $validate->errors();
            $response = [
                'message' => 'Registrasi gagal. Silakan periksa semua bagian yang ditandai.',
                'errors' => $errors->toArray()
            ];
            
            return response()->json($response, 400);
        }

        $registerData['password'] = bcrypt($registerData['password']);
        $registerData['role'] = 'User';

        $user = User::create($registerData);
        $user->sendEmailVerificationNotification();
        return response()->json([
            'status' => 'success',
            'message' => 'Register Berhasil! Silakan verifikasi email Anda.',
            'data' => $user
        ], 200);
    }

    public function loginUser(Request $request)
    {
        $loginData = $request->all();
    
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < 10; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
    
        $validate = Validator::make($loginData, [
            'email' => 'required',
            'password' => 'required',
        ]);
    
        if ($validate->fails()) {
            return response(['message' => $validate->errors()->first(), 'errors' => $validate->errors()], 400);
        }
    
        $user = User::where('email', $loginData['email'])->first();
    
        if ($user && $user->email_verified_at == null) {
            return response(['message' => 'Email not verified. Please verify your email first.'], 401);
        }
    
        if (Auth::guard('User')->attempt($loginData)) {
            $users = Auth::user();
            $token = $randomString;
    
            return response([
                'message' => 'Authenticated',
                'data' => [
                    'message' => 'berhasil login sebagai customer',
                    'data' => $users,
                    'token' => $token,
                ],
            ]);
        } else {
            return response(['message' => 'Invalid Credentials user'], 401);
        }
    }
    


}
