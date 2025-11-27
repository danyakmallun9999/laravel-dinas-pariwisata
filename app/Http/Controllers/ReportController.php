<?php

namespace App\Http\Controllers;

use App\Services\ReportExportService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ReportController extends Controller
{
    public function __construct(
        protected ReportExportService $exportService
    ) {
    }

    /**
     * Show export form
     */
    public function index(): View
    {
        return view('admin.reports.index');
    }

    /**
     * Export data to CSV
     */
    public function exportCsv(Request $request)
    {
        $request->validate([
            'type' => 'required|in:places,boundaries,infrastructures,land_uses,all',
        ]);

        $path = $this->exportService->exportToCsv($request->type);

        return Storage::disk('public')->download($path);
    }

    /**
     * Export HTML report (for PDF printing)
     */
    public function exportHtml(): Response
    {
        $html = $this->exportService->generateHtmlReport();

        return response($html, 200, [
            'Content-Type' => 'text/html; charset=utf-8',
        ]);
    }
}

