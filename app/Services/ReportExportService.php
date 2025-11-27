<?php

namespace App\Services;

use App\Models\Boundary;
use App\Models\Category;
use App\Models\Infrastructure;
use App\Models\LandUse;
use App\Models\Place;
use Illuminate\Support\Facades\Storage;

class ReportExportService
{
    /**
     * Export data to CSV format
     */
    public function exportToCsv(string $type): string
    {
        $filename = 'report_' . $type . '_' . date('Y-m-d_His') . '.csv';
        $path = 'reports/' . $filename;

        $data = $this->getDataForType($type);
        $headers = $this->getHeadersForType($type);

        $csvContent = $this->arrayToCsv($headers, $data);

        Storage::disk('public')->put($path, $csvContent);

        return $path;
    }

    /**
     * Generate HTML report for PDF
     */
    public function generateHtmlReport(): string
    {
        $stats = $this->getStatistics();

        $html = view('admin.reports.html', compact('stats'))->render();

        return $html;
    }

    /**
     * Get data based on type
     */
    protected function getDataForType(string $type): array
    {
        return match ($type) {
            'places' => $this->getPlacesData(),
            'boundaries' => $this->getBoundariesData(),
            'infrastructures' => $this->getInfrastructuresData(),
            'land_uses' => $this->getLandUsesData(),
            'all' => $this->getAllData(),
            default => [],
        };
    }

    /**
     * Get headers based on type
     */
    protected function getHeadersForType(string $type): array
    {
        return match ($type) {
            'places' => ['ID', 'Nama', 'Kategori', 'Latitude', 'Longitude', 'Deskripsi'],
            'boundaries' => ['ID', 'Nama', 'Tipe', 'Luas (ha)', 'Deskripsi'],
            'infrastructures' => ['ID', 'Nama', 'Tipe', 'Panjang (m)', 'Lebar (m)', 'Kondisi', 'Deskripsi'],
            'land_uses' => ['ID', 'Nama', 'Tipe', 'Luas (ha)', 'Pemilik', 'Deskripsi'],
            'all' => ['Tipe', 'ID', 'Nama', 'Detail'],
            default => [],
        };
    }

    /**
     * Get places data
     */
    protected function getPlacesData(): array
    {
        return Place::with('category')->get()->map(function ($place) {
            return [
                $place->id,
                $place->name,
                $place->category?->name ?? '-',
                $place->latitude,
                $place->longitude,
                $place->description ?? '-',
            ];
        })->toArray();
    }

    /**
     * Get boundaries data
     */
    protected function getBoundariesData(): array
    {
        return Boundary::all()->map(function ($boundary) {
            return [
                $boundary->id,
                $boundary->name,
                $boundary->type,
                $boundary->area_hectares ?? '-',
                $boundary->description ?? '-',
            ];
        })->toArray();
    }

    /**
     * Get infrastructures data
     */
    protected function getInfrastructuresData(): array
    {
        return Infrastructure::all()->map(function ($infrastructure) {
            return [
                $infrastructure->id,
                $infrastructure->name,
                $infrastructure->type,
                $infrastructure->length_meters ?? '-',
                $infrastructure->width_meters ?? '-',
                $infrastructure->condition ?? '-',
                $infrastructure->description ?? '-',
            ];
        })->toArray();
    }

    /**
     * Get land uses data
     */
    protected function getLandUsesData(): array
    {
        return LandUse::all()->map(function ($landUse) {
            return [
                $landUse->id,
                $landUse->name,
                $landUse->type,
                $landUse->area_hectares ?? '-',
                $landUse->owner ?? '-',
                $landUse->description ?? '-',
            ];
        })->toArray();
    }

    /**
     * Get all data summary
     */
    protected function getAllData(): array
    {
        $data = [];

        Place::all()->each(function ($place) use (&$data) {
            $data[] = ['Titik Lokasi', $place->id, $place->name, $place->category?->name ?? '-'];
        });

        Boundary::all()->each(function ($boundary) use (&$data) {
            $data[] = ['Batas Wilayah', $boundary->id, $boundary->name, $boundary->type];
        });

        Infrastructure::all()->each(function ($infrastructure) use (&$data) {
            $data[] = ['Infrastruktur', $infrastructure->id, $infrastructure->name, $infrastructure->type];
        });

        LandUse::all()->each(function ($landUse) use (&$data) {
            $data[] = ['Penggunaan Lahan', $landUse->id, $landUse->name, $landUse->type];
        });

        return $data;
    }

    /**
     * Convert array to CSV string
     */
    protected function arrayToCsv(array $headers, array $data): string
    {
        $output = fopen('php://temp', 'r+');

        // Add BOM for Excel UTF-8 support
        fwrite($output, "\xEF\xBB\xBF");

        // Write headers
        fputcsv($output, $headers);

        // Write data
        foreach ($data as $row) {
            fputcsv($output, $row);
        }

        rewind($output);
        $csv = stream_get_contents($output);
        fclose($output);

        return $csv;
    }

    /**
     * Get statistics for report
     */
    public function getStatistics(): array
    {
        return [
            'places_count' => Place::count(),
            'boundaries_count' => Boundary::count(),
            'infrastructures_count' => Infrastructure::count(),
            'land_uses_count' => LandUse::count(),
            'categories' => Category::withCount('places')->get(),
            'infrastructure_types' => Infrastructure::selectRaw('type, COUNT(*) as count')
                ->groupBy('type')
                ->get(),
            'land_use_types' => LandUse::selectRaw('type, COUNT(*) as count, SUM(area_hectares) as total_area')
                ->groupBy('type')
                ->get(),
            'total_infrastructure_length' => Infrastructure::sum('length_meters'),
            'total_land_area' => LandUse::sum('area_hectares'),
        ];
    }
}

