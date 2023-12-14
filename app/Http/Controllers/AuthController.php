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
    public function profile(){
            $id = auth()->user()->id;
            $user = User::where('id', $id)->get();
    
            if(count($user) > 0){
                return response([
                    'status' => 'success',
                    'data' => $user
                ], 200);
            }
    
        return response([
            'status' => 'error',
            'message' => 'Empty',
            'data' => null
        ], 400); 
    }
    
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
            'status' => 'success',
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
                'status' => 'error',
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

    
    public function update(Request $request)
    {
        $id = auth()->user()->id;
        $user = User::find($id);

        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'no_telp' => 'required',
            'alamat' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Update gagal. Silakan periksa semua bagian yang ditandai.',
                'errors' => $validator->errors()->toArray()
            ], 400);
        }
        if ($request->hasFile('gambar')) {
            $filenameWithExt = $request->file('gambar')->getClientOriginalName();
            $filename = pathinfo($filenameWithExt, PATHINFO_FILENAME);
            $extension = $request->file('gambar')->getClientOriginalExtension();
            $fileNameToStore = $filename . '_' . time() . '.' . $extension;
            $request->file('gambar')->storeAs('images', $fileNameToStore, 'images');
            $user->update(['gambar' => $fileNameToStore]);
        } 

        $user->update([
            'name' => $request->input('name'),
            'no_telp' => $request->input('no_telp'),
            'alamat' => $request->input('alamat'),
        ]);
        $user->refresh();

        return response()->json([
            'status' => 'success',
            'message' => 'Update Data Berhasil !',
            'data' => $user
        ], 200);
    }

    public function updateAdmin(Request $request, User $user)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'no_telp' => 'required',
            'alamat' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Update gagal. Silakan periksa semua bagian yang ditandai.',
                'errors' => $validator->errors()->toArray()
            ], 400);
        }
        $user->update([
            'name' => $request->input('name'),
            'no_telp' => $request->input('no_telp'),
            'alamat' => $request->input('alamat'),
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Update Data Berhasil !',
            'data' => $user
        ], 200);
    }

    public function loginUser(Request $request)
    {
        $loginData = $request->all();
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
    
        if (Auth::guard('web')->attempt($loginData)) {
            $users = Auth::user();
            $token = $users->createToken('Authentication Token',['web'])->plainTextToken;
    
            return response([
                'message' => 'Authenticated',
                'data' => [
                    'status' => 'success',
                    'User' => $users,
                    'token_type' => 'Bearer',
                    'access_token' => $token,
                ],
            ]);
        } else {
            return response(['message' => 'Invalid Credentials user'], 401);
        }
    }
    
    public function loginAdmin(Request $request)
    {
        $loginData = $request->all();
        $validate = Validator::make($loginData, [
            'email' => 'required',
            'password' => 'required',
        ]);
    
        if ($validate->fails()) {
            return response(['message' => $validate->errors()->first(), 'errors' => $validate->errors()], 400);
        }
        
    
        if (Auth::guard('admin')->attempt($loginData)) {
            $users = Auth::guard('admin')->user();
            $token = $users->createToken('Authentication Token', ['admin'])->plainTextToken;
    
            return response([
                'message' => 'Authenticated',
                'data' => [
                    'status' => 'success',
                    'User' => $users,
                    'token_type' => 'Bearer',
                    'access_token' => $token,
                ],
            ]);
        } else {
            return response(['message' => 'Invalid Credentials user'], 401);
        }
    }

    
    public function logout(Request $request)
    {
        if (Auth::guard('sanctum')->check()) {
            $user = Auth::guard('sanctum')->user();
            $user->tokens->each(function ($token) {
                $token->delete();
            });
    
            return response()->json([
                'status' => 'success',
                'message' => 'Logout Success',
                'user' => $user
            ], 200);
        } else {
            return response()->json([
                'status' => 'error',
                'message' => 'User not authenticated',
            ], 401); 
        }
    }

    public function destroy(User $user){
        $user->delete();
    
        return response([
            'status' => 'success',
            'message' => 'User deleted successfully',
            'data' => $user
        ], 200);
    }
    


}
