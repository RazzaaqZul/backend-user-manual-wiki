<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserManualUpdateRequest;
use App\Http\Resources\UserManualCollection;
use App\Http\Resources\UserManualResource;
use App\Http\Requests\UserManualStoreRequest;
use App\Models\UserManual;
use App\Models\UserManualHistory;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;


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
    
        // Handle image upload
        if ($request->hasFile('img')) {
            $imagePath = $request->file('img')->store('images/user_manuals', 'public'); // Store image in public/images/user_manuals
            $data['img'] = $imagePath; // Update the data with the path of the stored image
        }
    
        // Menggunakan user_id sebagai foreign key
        $userManual = new UserManual($data);
        $userManual->initial_editor = $user->name;
        $userManual->latest_editor = $user->name;
        $userManual->user_id = $user->user_id; 
        $userManual->save();
        Log::info($request);
        return response()->json([
            'message' => 'User Manual Created',
            'data' => new UserManualResource($userManual)
        ], 201);
    }
    

    public function update(UserManualUpdateRequest $request, int $id): JsonResponse 
    {
        $user = Auth::user();
      
        return DB::transaction(function () use ($request, $id, $user) {
            $currentUserManual = UserManual::where('user_manual_id', $id)->first();
    
            if (!$currentUserManual) {
                return response()->json([
                    'message' => "Failed to update User Manuals",
                    'errors' => [
                        'user_manual_id' => "User manual by id {$id} not found"
                    ]
                ], 404);
            }
    
            // Check for field updates
            $updatedFields = false;
            $fieldsToCheck = ['title', 'img', 'short_desc', 'category', 'content'];
            
            foreach ($fieldsToCheck as $field) {
                if ($request->has($field) && $request->input($field) !== $currentUserManual->$field) {
                    $updatedFields = true;
                    break;
                }
            }
    
            if (!$updatedFields) {
                return response()->json([
                    'message' => 'Failed to update user manual',
                    'errors' => [
                        'update' => 'Tidak ada perubahan pada kolom. Harap perbarui setidaknya satu kolom: Judul, Sampul, Kategori, atau Isi'
                    ]
                ], 400);
            }
    
            // Check unique title
            $title = $request->input('title');
            $existingTitle = UserManual::where('title', $title)
                                     ->where('user_manual_id', '!=', $id)
                                     ->first();
            if ($existingTitle) {
                return response()->json([
                    'message' => 'Failed to update user manual',
                    'errors' => [
                        'title' => 'Judul harus unik. Judul yang ditentukan telah dipakai.'
                    ]
                ], 400);
            }
    
            // Version comparison logic
            $newVersion = $request->input('version');
            $currentVersion = $currentUserManual->version;
            
            if (!$this->isValidVersionFormat($newVersion)) {
                return response()->json([
                    'message' => 'Failed to update user manual',
                    'errors' => [
                        'version' => 'Format versi tidak valid. Gunakan format X.X.X (misalnya, 1.1.2).'
                    ]
                ], 400);
            }

            if (!$this->isNewVersionHigher($newVersion, $currentVersion)) {
                return response()->json([
                    'message' => 'Failed to update user manual',
                    'errors' => [
                        'version' => "Versi harus lebih tinggi dari {$currentVersion}"
                    ]
                ], 400);
            }
            
            // Log history before updating
            $userManualHistory = new UserManualHistory($currentUserManual->toArray());
            $userManualHistory->save();
    
            // Update manual with validated data
            $validatedData = $request->validated();
            
            if ($request->hasFile('img')) {
                $imagePath = $request->file('img')->store('images/user_manuals', 'public');
                $validatedData['img'] = $imagePath;
            }
    
            try {
                Log::info($request);
    
                $validatedData['latest_editor'] = $user->name;
                $currentUserManual->fill($validatedData);
                $currentUserManual->save();
    
                return response()->json([
                    'message' => "Successfully updated user manual by ID : {$id}",
                    'data' => new UserManualResource($currentUserManual)
                ], 200);
    
            } catch (QueryException $e) {
                Log::error('Database query error: ' . $e->getMessage());
    
                if ($e->getCode() === '23000') {
                    return response()->json([
                        'message' => 'Failed to update user manual',
                        'errors' => [
                            'title' => 'The title must be unique, but the given title already exists.'
                        ]
                    ], 400);
                }
    
                return response()->json([
                    'message' => 'An error occurred while updating the user manual.',
                    'errors' => [
                        'database' => $e->getMessage()
                    ]
                ], 500);
            }
        });
    }


     /**
     * Validate version format (X.X.X)
     */
    private function isValidVersionFormat(string $version): bool
    {
        return preg_match('/^\d+\.\d+\.\d+$/', $version);
    }

    /**
     * Compare versions properly
     */
    private function isNewVersionHigher(string $newVersion, string $currentVersion): bool
    {
        $new = array_map('intval', explode('.', $newVersion));
        $current = array_map('intval', explode('.', $currentVersion));

        // Compare major version
        if ($new[0] !== $current[0]) {
            return $new[0] > $current[0];
        }

        // Compare minor version
        if ($new[1] !== $current[1]) {
            return $new[1] > $current[1];
        }

        // Compare patch version
        return $new[2] > $current[2];
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
    
        // Check if an image exists and delete it
        if ($userManual->img && Storage::disk('public')->exists($userManual->img)) {
            Storage::disk('public')->delete($userManual->img);
        }
    
        // Permanently delete the user manual
        $userManual->forceDelete();
    
        return response()->json([
            'message' => 'Successfully Deleted Permanently',
            'data' => new UserManualResource($userManual)
        ]);
    }


    public function userManualSearch(Request $request): JsonResponse
{
    // Ambil kata kunci pencarian dari parameter `q`
    $searchTerm = $request->input('q');

    // Gunakan Laravel Scout untuk mencari berdasarkan title dan content
    $userManuals = UserManual::search($searchTerm)->get();

    // Cek jika hasil pencarian kosong
    if ($userManuals->isEmpty()) {
        return response()->json([
            'message' => 'No user manuals found matching your search criteria',
            'errors' => 'User manual not found'
        ], 404);
    }

    // Jika ada hasil, kembalikan dalam JSON
    return response()->json([
        'message' => 'Successfully found User Manuals',
        'data' => new UserManualCollection($userManuals)
    ]);
}
}
