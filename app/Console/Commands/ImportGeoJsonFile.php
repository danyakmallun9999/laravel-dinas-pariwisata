<?php

namespace App\Console\Commands;

use App\Services\GeoJsonImportService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Auth;
use App\Models\Import;
use App\Models\User;

class ImportGeoJsonFile extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'gis:import {file} {type} {--user=1}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import GeoJSON file directly from filesystem';

    /**
     * Execute the console command.
     */
    public function handle(GeoJsonImportService $importService): int
    {
        $filePath = $this->argument('file');
        $type = $this->argument('type');
        $userId = $this->option('user');

        if (!file_exists($filePath)) {
            $this->error("File tidak ditemukan: {$filePath}");
            return Command::FAILURE;
        }

        if (!in_array($type, ['boundary', 'infrastructure', 'land_use'])) {
            $this->error("Tipe tidak valid. Gunakan: boundary, infrastructure, atau land_use");
            return Command::FAILURE;
        }

        $this->info("Membaca file: {$filePath}");
        $content = file_get_contents($filePath);
        $geoJsonData = json_decode($content, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            $this->error("File tidak valid. Error: " . json_last_error_msg());
            return Command::FAILURE;
        }

        $this->info("Mengimport data tipe: {$type}");
        $result = $importService->import($geoJsonData, $type);

        // Save import record
        $user = User::find($userId);
        if ($user) {
            Import::create([
                'filename' => basename($filePath),
                'type' => $type,
                'records_count' => $result['count'],
                'status' => $result['success'] ? 'completed' : 'failed',
                'errors' => !empty($result['errors']) ? json_encode($result['errors']) : null,
                'imported_by' => $userId,
            ]);
        }

        if ($result['success']) {
            $this->info("✓ Berhasil mengimpor {$result['count']} data");
            if (!empty($result['errors'])) {
                $this->warn("Peringatan: " . count($result['errors']) . " error ditemukan");
                foreach ($result['errors'] as $error) {
                    $this->line("  - {$error}");
                }
            }
            return Command::SUCCESS;
        } else {
            $this->error("Import gagal!");
            foreach ($result['errors'] as $error) {
                $this->error("  - {$error}");
            }
            return Command::FAILURE;
        }
    }
}
