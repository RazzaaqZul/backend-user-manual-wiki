<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserLoginRequest;
use App\Http\Requests\UserRegisterRequest;
use App\Http\Resources\UserLoginResource;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class AuthController extends Controller
{
    public function login(UserLoginRequest $request) : JsonResponse {

        $data = $request->validated();
        $user = User::where('email', $data['email'])->first();

        if(!$user || !Hash::check($data['password'], $user->password)){
            throw new HttpResponseException(response([
                "message" => "Failed to logged in",
                "errors" => [
                        "email or password wrong"
                ]
            ], 401));
        }

        
        if ($user->role === 'admin'){
            $user->tokens()->delete();
            $accessToken = $user->createToken('admin_access_token', ['*'], now()->addHours(2));
              // access the plain text value from NewAccessToken Object
            $user->token = $accessToken->plainTextToken;
            $user->save();
        } else {
            $user->tokens()->delete();
            // createToken() for issue a token (hashed using SH6-256)
            $accessToken = $user->createToken('access_token', [
            'user_manual:create', 
            'user_manual:read',
            'user_manual:update',
            'user_manual:delete',
            'history:delete'
            ], now()->addHours(2));
            // access the plain text value from NewAccessToken Object
            $user->token = $accessToken->plainTextToken;
            $user->save();
        }
       
        // return new UserLoginResource($user);
        return response()->json([
            'message' => 'Successfully Logged in',
            'data' => new UserLoginResource($user)
        ], 200);
    }


    public function register(UserRegisterRequest $request) : JsonResponse {
        $data = $request->validated();

        $user = new User($data);
        Log::info($request);

        $user->password = Hash::make($data['password']);
        $user->save();

        return response()->json([
            'message' => 'Successful registration',
            'data' => new UserResource($user),
        ], 201);
    }


    public function logout() : JsonResponse {
        $user = Auth::user();
        $user = User::where('user_id', $user->user_id)->first();

        if(!$user) {
            return response()->json([
                'message' => 'Failed to logout'
            ], 404);
        }

        $user->tokens()->delete();
        
        return response()->json([
            'message' => 'Successfully logout'
        ], 200);
    }
    
    public function show() : JsonResponse {
        $user = Auth::user();
        
        return response()->json([
            'message' => 'Successfully get user',
            'data' => new UserResource($user)
        ]);
    }

  
    // Method to return the authenticated user
    public function user(Request $request)
    {
        return response()->json(Auth::user()); // Returns the authenticated user's data
    }


}
