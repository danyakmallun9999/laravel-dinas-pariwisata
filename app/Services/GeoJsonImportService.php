<?php

namespace App\Services;

use App\Models\Boundary;
use App\Models\Infrastructure;
use App\Models\LandUse;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class GeoJsonImportService
{
    /**
     * Import GeoJSON data into the appropriate model
     *
     * @param array $geoJsonData
     * @param string $type (boundary, infrastructure, land_use)
     * @return array ['success' => bool, 'count' => int, 'errors' => array]
     */
    public function import(array $geoJsonData, string $type): array
    {
        $errors = [];
        $count = 0;

        if (!isset($geoJsonData['type']) || $geoJsonData['type'] !== 'FeatureCollection') {
            return [
                'success' => false,
                'count' => 0,
                'errors' => ['Invalid GeoJSON format. Expected FeatureCollection.'],
            ];
        }

        if (!isset($geoJsonData['features']) || !is_array($geoJsonData['features'])) {
            return [
                'success' => false,
                'count' => 0,
                'errors' => ['No features found in GeoJSON.'],
            ];
        }

        foreach ($geoJsonData['features'] as $index => $feature) {
            try {
                $result = $this->importFeature($feature, $type);
                if ($result['success']) {
                    $count++;
                } else {
                    $errors[] = "Feature #{$index}: " . ($result['error'] ?? 'Unknown error');
                }
            } catch (\Exception $e) {
                $errors[] = "Feature #{$index}: " . $e->getMessage();
                Log::error('GeoJSON import error', [
                    'type' => $type,
                    'feature_index' => $index,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        return [
            'success' => $count > 0,
            'count' => $count,
            'errors' => $errors,
        ];
    }

    /**
     * Import a single feature
     *
     * @param array $feature
     * @param string $type
     * @return array
     */
    protected function importFeature(array $feature, string $type): array
    {
        if (!isset($feature['geometry']) || !isset($feature['properties'])) {
            return ['success' => false, 'error' => 'Missing geometry or properties'];
        }

        $geometry = $feature['geometry'];
        $properties = $feature['properties'];

        // Validate geometry type based on import type
        $expectedGeometryType = $this->getExpectedGeometryType($type);
        $allowedTypes = [$expectedGeometryType];
        
        // Allow MultiPolygon for boundaries and land_uses
        if (in_array($type, ['boundary', 'land_use'])) {
            $allowedTypes[] = 'MultiPolygon';
        }
        
        if (!in_array($geometry['type'], $allowedTypes)) {
            return [
                'success' => false,
                'error' => "Expected geometry type " . implode(' or ', $allowedTypes) . ", got {$geometry['type']}",
            ];
        }

        // Prepare data based on type
        $data = $this->prepareData($properties, $geometry, $type);

        // Validate and save
        switch ($type) {
            case 'boundary':
                return $this->importBoundary($data);
            case 'infrastructure':
                return $this->importInfrastructure($data);
            case 'land_use':
                return $this->importLandUse($data);
            default:
                return ['success' => false, 'error' => "Unknown import type: {$type}"];
        }
    }

    /**
     * Get expected geometry type for import type
     */
    protected function getExpectedGeometryType(string $type): string
    {
        return match ($type) {
            'boundary', 'land_use' => 'Polygon', // Also accepts MultiPolygon
            'infrastructure' => 'LineString',
            default => 'Point',
        };
    }

    /**
     * Prepare data from feature properties and geometry
     */
    protected function prepareData(array $properties, array $geometry, string $type): array
    {
        // Convert MultiPolygon to Polygon if needed (take first polygon)
        if ($geometry['type'] === 'MultiPolygon' && in_array($type, ['boundary', 'land_use'])) {
            $geometry = [
                'type' => 'Polygon',
                'coordinates' => $geometry['coordinates'][0] ?? [],
            ];
        }
        
        $data = [
            'geometry' => $geometry,
        ];

        // Common fields
        $data['name'] = $properties['name'] ?? $properties['NAME'] ?? 'Unnamed';
        $data['description'] = $properties['description'] ?? $properties['DESCRIPTION'] ?? null;

        // Type-specific fields
        switch ($type) {
            case 'boundary':
                $data['type'] = $properties['type'] ?? 'village_boundary';
                $data['area_hectares'] = $properties['area_hectares'] ?? $properties['area'] ?? $this->calculatePolygonArea($geometry);
                break;

            case 'infrastructure':
                $data['type'] = $properties['type'] ?? $properties['TYPE'] ?? 'road';
                $data['length_meters'] = $properties['length_meters'] ?? $properties['length'] ?? $this->calculateLineLength($geometry);
                $data['width_meters'] = $properties['width_meters'] ?? $properties['width'] ?? null;
                $data['condition'] = $properties['condition'] ?? $properties['CONDITION'] ?? null;
                $data['category_id'] = $properties['category_id'] ?? null;
                break;

            case 'land_use':
                $data['type'] = $properties['type'] ?? $properties['TYPE'] ?? 'settlement';
                $data['area_hectares'] = $properties['area_hectares'] ?? $properties['area'] ?? $this->calculatePolygonArea($geometry);
                $data['owner'] = $properties['owner'] ?? $properties['OWNER'] ?? null;
                break;
        }

        return $data;
    }

    /**
     * Import boundary
     */
    protected function importBoundary(array $data): array
    {
        $validator = Validator::make($data, [
            'name' => 'required|string|max:255',
            'type' => 'nullable|string|max:255',
            'geometry' => 'required|array',
            'area_hectares' => 'nullable|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return ['success' => false, 'error' => $validator->errors()->first()];
        }

        Boundary::create($data);
        return ['success' => true];
    }

    /**
     * Import infrastructure
     */
    protected function importInfrastructure(array $data): array
    {
        $validator = Validator::make($data, [
            'name' => 'required|string|max:255',
            'type' => 'required|string|max:255',
            'geometry' => 'required|array',
            'length_meters' => 'nullable|numeric|min:0',
            'width_meters' => 'nullable|numeric|min:0',
            'category_id' => 'nullable|exists:categories,id',
        ]);

        if ($validator->fails()) {
            return ['success' => false, 'error' => $validator->errors()->first()];
        }

        Infrastructure::create($data);
        return ['success' => true];
    }

    /**
     * Import land use
     */
    protected function importLandUse(array $data): array
    {
        $validator = Validator::make($data, [
            'name' => 'required|string|max:255',
            'type' => 'required|string|max:255',
            'geometry' => 'required|array',
            'area_hectares' => 'nullable|numeric|min:0',
            'owner' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return ['success' => false, 'error' => $validator->errors()->first()];
        }

        LandUse::create($data);
        return ['success' => true];
    }

    /**
     * Calculate polygon area in hectares (approximate)
     */
    protected function calculatePolygonArea(array $geometry): ?float
    {
        if ($geometry['type'] !== 'Polygon' || !isset($geometry['coordinates'][0])) {
            return null;
        }

        $coordinates = $geometry['coordinates'][0];
        $area = 0;
        $n = count($coordinates);

        for ($i = 0; $i < $n - 1; $i++) {
            $area += ($coordinates[$i][0] * $coordinates[$i + 1][1]) - ($coordinates[$i + 1][0] * $coordinates[$i][1]);
        }

        // Convert to hectares (rough approximation, assumes coordinates are in degrees)
        // This is a simplified calculation - for accurate results, use proper projection
        $area = abs($area) / 2;
        $areaHectares = $area * 111.32 * 111.32 * cos(deg2rad($coordinates[0][1])) / 10000;

        return round($areaHectares, 4);
    }

    /**
     * Calculate line length in meters (approximate)
     */
    protected function calculateLineLength(array $geometry): ?float
    {
        if ($geometry['type'] !== 'LineString' || !isset($geometry['coordinates']) || count($geometry['coordinates']) < 2) {
            return null;
        }

        $coordinates = $geometry['coordinates'];
        $length = 0;

        for ($i = 0; $i < count($coordinates) - 1; $i++) {
            $lat1 = $coordinates[$i][1];
            $lon1 = $coordinates[$i][0];
            $lat2 = $coordinates[$i + 1][1];
            $lon2 = $coordinates[$i + 1][0];

            $length += $this->haversineDistance($lat1, $lon1, $lat2, $lon2);
        }

        return round($length, 2);
    }

    /**
     * Calculate distance between two points using Haversine formula
     */
    protected function haversineDistance(float $lat1, float $lon1, float $lat2, float $lon2): float
    {
        $earthRadius = 6371000; // meters

        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);

        $a = sin($dLat / 2) * sin($dLat / 2) +
            cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
            sin($dLon / 2) * sin($dLon / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return $earthRadius * $c;
    }
}

