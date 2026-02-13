<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Laravolt\Indonesia\Facade as Indonesia;

class LocationController extends Controller
{
    public function provinces()
    {
        try {
            $provinces = Indonesia::allProvinces();
            return response()->json($provinces);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function cities(Request $request)
    {
        try {
            $provinceId = $request->query('province_id');
            if (!$provinceId) {
                return response()->json(['error' => 'Province ID is required'], 400);
            }
            $province = Indonesia::findProvince($provinceId, ['cities']);
            return response()->json($province->cities);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
