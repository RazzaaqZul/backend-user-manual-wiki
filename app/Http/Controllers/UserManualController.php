<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserManualUpdateRequest;
use App\Http\Resources\UserManualCollection;
use App\Http\Resources\UserManualResource;
use App\Http\Requests\UserManualStoreRequest;
use App\Models\UserManual;
use App\Models\UserManualHistory;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;

class UserManualController extends Controller
{
    
    public function index(): JsonResponse
    {
        $userManual = UserManual::all();

        if (count($userManual) == 0) {
            return response()->json([
                'message' => 'No user manuals available at the moment',
                'errors' => 'User manual not found'
            ], 404);
        }

        return response()->json([
            'message' => 'Succesfully get all User Manuals',
            'data' => new UserManualCollection($userManual)
        ]);
    }

    public function show(int $id): JsonResponse
    {
        $userManual = UserManual::where('user_manual_id', $id)->first();
    
        if (!$userManual) {
            return response()->json([
                'message' => "Failed to get User Manuals by ID: {$id}",
                'errors' => [
                    "user_manual_id" => "User manual by id {$id} not found"
                ]
            ], 404);
        }
    
        return response()->json([
            'message' => "Succesfully get User Manual by ID : {$id}",
            'data' => new UserManualResource($userManual)
        ]);
    }

    public function store(UserManualStoreRequest $request) : JsonResponse 
    {
        $data = $request->validated();
        $user = Auth::user();
        // Pastikan pengguna terautentikasi
        if (!$user) {
            return response()->json([
                'message' => 'User not authenticated'
            ], 401);
        }

        // Menggunakan user_id sebagai foreign key
        $userManual = new UserManual($data);
        $userManual->initial_editor = $user->name;
        $userManual->latest_editor = $user->name;
        $userManual->user_id = $user->user_id; 
        $userManual->save();
    
        return response()->json([
            'message' => 'User Manual Created',
            'data' => new UserManualResource($userManual)
        ], 201);
    }

    public function update(UserManualUpdateRequest $request, int $id) : JsonResponse {
        
        $user = Auth::user();

        // Start the transaction
        return DB::transaction(function () use ($request, $id, $user) {
            $currentUserManual = UserManual::where('user_manual_id', $id)->first();

            // Check if the user manual exists
            if (!$currentUserManual) {
                return response()->json([
                    'message' => "Failed to update User Manuals",
                    'errors' => [
                        'user_manual_id' => "User manual by id {$id} not found"
                    ]
                ], 404);
            }

            // Periksa apakah title dalam request sudah ada di UserManual lainnya
            $title = $request->input('title');
            $existingTitle = UserManual::where('title', $title)->where('user_manual_id', '!=', $id)->first();

            if ($existingTitle) {
                return response()->json([
                    'message' => 'Failed to update user manual',
                    'errors' => 'Title must be unique. The specified title already exists.'
                ], 400);
            }

            $version = $request->input('version');
            $existingVersion = $currentUserManual->version;

            if ($version <= $existingVersion) {
                return response()->json([
                    'message' => 'Failed to update user manual',
                    'errors' => 'Version must be higher than ' . $existingVersion
                ], 400);    
            }
            
            // Log the history before updating
            $userManualHistory = new UserManualHistory($currentUserManual->toArray());
            $userManualHistory->save();

            // Update the user manual with validated data
            $validatedData = $request->validated(); 
           
            $validatedData['latest_editor'] = $user->name;
            $currentUserManual->fill($validatedData); 
            $currentUserManual->save();

    
            return response()->json([
                'message' => "Successfully updated user manual by ID : {$id}",
                'data' => new UserManualResource($currentUserManual)
            ], 200);
        });
    }

    public function destroy(int $id) : JsonResponse 
    {
        $userManual = UserManual::where('user_manual_id', $id)->first();

         // Check if the user manual exists
         if (!$userManual) {
            return response()->json([
                'message' => "Failed to delete User Manuals",
                'errors' => [
                    'user_manual_id' => "User manual by id {$id} not found"
                ]
            ], 404);
        }
        // Delete the user manual
        $userManual->delete();

        // Return the deleted user manual's resource
        return response()->json([
            'message' => 'User Manual Deleted', 
            'data' => new UserManualResource($userManual)
        ], 200);
    }


    public function trash() : JsonResponse 
    {
        Gate::authorize('isAdmin');
    
        $userManuals = UserManual::onlyTrashed()->get();
        Log::info($userManuals);
    
        return response()->json([
            'message' => "Successfully get trashed data",
            'data' => UserManualResource::collection($userManuals) // Use the collection method here
        ]);
    }
    
    public function restoreUserManual(int $id) : JsonResponse
    {
        Gate::authorize('isAdmin');

        $userManual = UserManual::onlyTrashed()->where('user_manual_id', $id);
        
        // Check if the user manual exists
        if (!$userManual) {
            return response()->json([
                'message' => "Failed to restore User Manuals",
                'errors' => [
                    'user_manual_id' => "User manual by id {$id} not found"
                ]
            ], 404);
        }
        
        $userManual->restore();

        return response()->json([
            'message' => 'Successfully Restored',
            'data' => new UserManualResource($userManual)
        ]);
        
    }

    public function deletePermanent(int $id) : JsonResponse
    {
        Gate::authorize('isAdmin');

        $userManual = UserManual::onlyTrashed()->where('user_manual_id', $id)->first();
        // Check if the user manual exists
        if (!$userManual) {
            return response()->json([
                'message' => "Failed to restore User Manuals",
                'errors' => [
                    'user_manual_id' => "User manual by id {$id} not found"
                ]
            ], 404);
        }

        $userManual->forceDelete();

        return response()->json([
            'message' => 'Successfully Delete Permanent',
            'data' => new UserManualResource($userManual)
        ]);

    }
}
