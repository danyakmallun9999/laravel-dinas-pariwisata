<?php

namespace App\Http\Controllers;

use App\Models\Import;
use App\Services\GeoJsonImportService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class ImportController extends Controller
{
    public function __construct(
        protected GeoJsonImportService $importService
    ) {
    }

    /**
     * Show the import form
     */
    public function index(): View
    {
        $imports = Import::with('importer')->latest()->paginate(10);

        return view('admin.import', compact('imports'));
    }

    /**
     * Process the import
     */
    public function import(Request $request): RedirectResponse
    {
        $request->validate([
            'file' => 'required|file|mimes:json,geojson|max:10240',
            'type' => 'required|in:boundary,infrastructure,land_use',
        ]);

        try {
            $file = $request->file('file');
            $content = file_get_contents($file->getRealPath());
            $geoJsonData = json_decode($content, true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                return redirect()
                    ->route('admin.import.index')
                    ->with('error', 'File tidak valid. Pastikan file adalah GeoJSON yang valid.');
            }

            $result = $this->importService->import($geoJsonData, $request->type);

            // Save import record
            Import::create([
                'filename' => $file->getClientOriginalName(),
                'type' => $request->type,
                'records_count' => $result['count'],
                'status' => $result['success'] ? 'completed' : 'failed',
                'errors' => !empty($result['errors']) ? json_encode($result['errors']) : null,
                'imported_by' => Auth::id(),
            ]);

            if ($result['success']) {
                return redirect()
                    ->route('admin.import.index')
                    ->with('status', "Berhasil mengimpor {$result['count']} data dari file {$file->getClientOriginalName()}.");
            } else {
                return redirect()
                    ->route('admin.import.index')
                    ->with('error', 'Import gagal. ' . implode(', ', $result['errors']));
            }
        } catch (\Exception $e) {
            return redirect()
                ->route('admin.import.index')
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
}

