<?php


namespace App\Traits;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

trait ActivityLogger
{
    protected function storeActivity($description)
    {
        try {
            DB::table('activities')->insert([
                'user_id'    => Auth::id(),
                'description' => $description,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            Log::info("Activity stored: {$description}");
            return true;
        } catch (\Exception $e) {
            Log::error("Failed to store activity: " . $e->getMessage());
            return false;
        }
    }
}
