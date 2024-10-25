<?php

namespace App\Http\Controllers;

use App\Http\Resources\UserManualCollection;
use App\Http\Resources\UserManualResource;
use App\Http\Resources\UserResource;
use App\Models\UserManual;
use App\Models\UserManualHistory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UserManualHistoryController extends Controller
{
    //
    public function destroy(int $id, int $histories_id){
        $userManualHistory = UserManualHistory::where('user_manual_id', $id)
        ->where('user_manual_history_id', $histories_id)
        ->first();
        $userManual = UserManual::where('user_manual_id', $id)->first();

        if (!$userManual) {
            return response()->json([
                'message' => "Failed to delete User Manual History",
                'errors' => [
                    'user_manual_id' => "User manual by ID: {$id} not found"
                ]
            ],404);
        }

        // Check if the history record exists
        if (!$userManualHistory) {
            return response()->json([
                'message' => "Failed to delete User Manual History",
                'errors' => [
                    'user_manual_history_id' => "User manual history by ID: {$histories_id} not found"
                ]
            ],404);
        }

        // Delete the user manual history
        $userManualHistory->delete();

        // Respond with success message after deletion
        return response()->json([
            'message' => 'User manual history deleted',
            'data' => new UserManualResource($userManualHistory)
        ], 200);
    }

    public function index(int $id) : JsonResponse 
    {
        $userManualHistory = UserManualHistory::where('user_manual_id', $id)->get();
        $userManual = UserManual::where('user_manual_id', $id)->first();
        if (!$userManual) {
            return response()->json([
                'message' => "Failed to get User Manual",
                'errors' => [
                    'user_manual_id' => "User manual by ID: {$id} not found"
                ]
            ],404);
        }
        
        if(count($userManualHistory) === 0){
            return response()->json([
                'message' => "Successfully to get User Manual History",
                'errors' => [
                    'user_manual_id' => "User manual by ID: {$id} history is Empty"
                ]
            ],404);
        }

        return response()->json([
            'message' => "Successfully to get User Manual by ID: {$id} History",
            "data" => new UserManualCollection($userManualHistory)
        ], 200);    
    }

    public function show(int $id, int $history_id) : JsonResponse
    {
        // Cari UserManualHistory berdasarkan user_manual_id dan user_manual_history_id
        $userManualHistory = UserManualHistory::where([
            ['user_manual_id', '=', $id],
            ['user_manual_history_id', '=', $history_id]
        ])->first();

        // Jika UserManual tidak ditemukan
        $userManual = UserManual::where('user_manual_id', $id)->first();
        if (!$userManual) {
            return response()->json([
                'message' => "Failed to get User Manual",
                'errors' => [
                    'user_manual_id' => "User manual by ID: {$id} not found"
                ]
            ], 404);
        }

        // Jika UserManualHistory tidak ditemukan
        if (!$userManualHistory) {
            return response()->json([
                'message' => "Failed to get User Manual History",
                'errors' => [
                    'user_manual_history_id' => "User manual history by history ID: {$history_id} not found"
                ]
            ], 404);
        }

        // Berhasil menemukan user manual dan user manual history
        return response()->json([
            'message' => "Successfully retrieved User Manual by ID: {$id} and History ID: {$history_id}",
            "data" => new UserManualResource($userManualHistory)
        ], 200);    
    }

}
