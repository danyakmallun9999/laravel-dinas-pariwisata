<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Laravolt\Indonesia\Models\Province;
use Laravolt\Indonesia\Models\City;

class LocationController extends Controller
{
    public function provinces()
    {
        try {
            // Fetch provinces from database using Laravolt model
            $provinces = Province::orderBy('name', 'asc')->get();
            return response()->json($provinces);
        } catch (\Exception $e) {
            Log::error('Failed to fetch provinces', ['error' => $e->getMessage()]);
            return response()->json(['error' => 'Gagal memuat data provinsi.'], 500);
        }
    }

    public function cities(Request $request)
    {
        try {
            $provinceId = $request->query('province_id');
            if (!$provinceId) {
                return response()->json(['error' => 'Province ID is required'], 400);
            }

            // Fetch cities from database
            // Note: Laravolt structure uses 'province_code' usually, but let's check if 'province_id' works or we need to query by relationship
            // Laravolt Indonesia v0.39 usually relates via code or id. Let's try standard relation or where clause.
            // Checking common usage: City::where('province_code', $province->code)->get(); 
            // OR if the input is ID, we might need to find the province first.
            // However, often the dropdown sends the ID (which might be the code in some setups, or auto-increment ID).
            // Let's assume the frontend sends what the provinces endpoint returned.
            // Laravolt provinces endpoint usually returns 'id' (int) and 'code' (string).
            
            // Let's implement robustly: try filtering by province_id (foreign key) first.
            // Inspecting package: typically `cities` table has `province_code`.
            
            // Let's try to find by ID first to get the code, or just filter.
            // If province_id is the auto-increment ID:
            $province = Province::find($provinceId);
            
            if ($province) {
                 $cities = City::where('province_code', $province->code)
                            ->orderBy('name', 'asc')
                            ->get();
                 return response()->json($cities);
            }
            
            // Fallback if province_id not found (maybe it was a code?)
             $cities = City::where('province_code', $provinceId)
                        ->orderBy('name', 'asc')
                        ->get();

            return response()->json($cities);
        } catch (\Exception $e) {
            Log::error('Failed to fetch cities', ['error' => $e->getMessage()]);
            return response()->json(['error' => 'Gagal memuat data kota.'], 500);
        }
    }
}
