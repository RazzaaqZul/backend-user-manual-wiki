<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserUpdateRequest;
use App\Http\Resources\UserCollection;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Models\UserManual;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;


class UserController extends Controller
{
    public function index(): JsonResponse
    {
        Gate::authorize('isAdmin');
      
        $users = User::all();
        return response()->json([
            'message' => 'Successfully get all users',
            'data' => new UserCollection($users)
        ], 200);
    }

    public function destroy(int $id): JsonResponse
    {
        Gate::authorize('isAdmin');
        
        $user = User::where('user_id', $id)->first();
        $currentUser = Auth::user()->user_id;
        
        if ($currentUser == $id) {
            return response()->json([
                'message' => 'Failed to delete a user',
                'errors' => 'Action prohibited: You cannot delete your own account'
            ], 403);
        }
        
        if (!$user) {
            return response()->json([
                'message' => 'Failed to delete a user',
                'errors' => "The user with the provided ID: {$id} was not found"
            ], 404);
        }
        // Update user_id pada User Manual menjadi null
        UserManual::where('user_id', $id)->update(['user_id' => null]);
    
        $user->delete();
        return response()->json([
            'message' => 'User has already deleted',
            'data' => new UserResource($user)
        ], 200);
    }

    public function update(UserUpdateRequest $request, int $id): JsonResponse
    {
        Gate::authorize('isAdmin');

        $user = User::where('user_id', $id)->first();
        
        if(!$user){
            return response()->json([
                'message' => 'Failed to update a user',
                'errors' => "The user with the provided ID: {$id} was not found"    
            ], 404);
        } 

        $validatedData = $request->validated();
        // Hanya update jika field tidak null, jika null tetap gunakan data lama
        $user->name = $validatedData['name'] ?? $user->name;
        $user->role = $validatedData['role'] ?? $user->role;
        $user->save();

        return response()->json([
            'message' => 'Successfully update a user',
            'data' => new UserResource($user)
        ], 200);
    }
}
