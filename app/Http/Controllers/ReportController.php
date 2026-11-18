<?php

namespace App\Http\Controllers;

use App\Models\Plant;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Illuminate\View\View;

class ReportController extends Controller
{
    /**
     * Shared algorithmic filter builder.
     * Guarantees that the web report, PDF export, and Excel export all apply
     * the exact same filters (report_type, category, and stock_status).
     */
    private function buildFilteredQuery(Request $request): Builder
    {
        $query = Plant::with('user');

        $reportType = $request->input('report_type', 'all');
        $category = $request->input('category');
        $stockStatus = $request->input('stock_status');

        // Apply preset report type logic
        if ($reportType === 'in_stock') {
            $query->where('stock_status', 'in_stock');
        } elseif ($reportType === 'out_of_stock') {
            $query->where('stock_status', 'out_of_stock');
        } elseif ($reportType === 'by_category') {
            $query->orderBy('category')->orderBy('plant_name');
        } elseif ($reportType === 'price_list') {
            $query->orderBy('unit_price', 'desc');
        }

        // Apply explicit Category dropdown filter if selected
        if (!empty($category)) {
            $query->where('category', $category);
        }

        // Apply explicit Stock Status dropdown filter if selected
        if (!empty($stockStatus)) {
            $query->where('stock_status', $stockStatus);
        }

        // Default ordering if not already ordered by category or price_list
        if (!in_array($reportType, ['by_category', 'price_list'])) {
            $query->orderBy('plant_name');
        }

        return $query;
    }

    /**
     * Display the interactive Plant Reports page with live filter results.
     */
    public function index(Request $request): View
    {
        $plants = $this->buildFilteredQuery($request)->get();
        $categories = Plant::CATEGORIES;

        // Calculate summary metrics for the currently filtered dataset
        $summary = [
            'total_count' => $plants->count(),
            'in_stock_count' => $plants->where('stock_status', 'in_stock')->count(),
            'out_of_stock_count' => $plants->where('stock_status', 'out_of_stock')->count(),
            'average_price' => $plants->count() > 0 ? $plants->avg('unit_price') : 0,
        ];

        return view('reports.index', compact('plants', 'categories', 'summary'));
    }

    /**
     * Export the currently filtered plant report to PDF.
     * Uses barryvdh/laravel-dompdf if installed, or renders a print-ready PDF view automatically.
     */
    public function exportPdf(Request $request): Response|View
    {
        $plants = $this->buildFilteredQuery($request)->get();
        $filters = [
            'report_type' => $request->input('report_type', 'all'),
            'category' => $request->input('category', 'All Categories'),
            'stock_status' => $request->input('stock_status', 'All Statuses'),
            'generated_at' => now()->format('F d, Y h:i A'),
        ];

        // Check if barryvdh/laravel-dompdf is installed
        if (class_exists(\Barryvdh\DomPDF\Facade\Pdf::class)) {
            $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('reports.pdf', compact('plants', 'filters'))
                ->setPaper('a4', 'landscape');
            return $pdf->download('plant_inventory_report_' . now()->format('Ymd_His') . '.pdf');
        }

        // Fallback: Render print-ready PDF view that triggers browser Save-as-PDF dialog
        return view('reports.pdf', compact('plants', 'filters'));
    }

    /**
     * Export the currently filtered plant report to Excel (.csv / Excel-compatible stream).
     * Matches all active category, stock status, and report type filters.
     */
    public function exportExcel(Request $request): StreamedResponse
    {
        $plants = $this->buildFilteredQuery($request)->get();
        $fileName = 'plant_inventory_report_' . now()->format('Ymd_His') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$fileName}\"",
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        $callback = function () use ($plants, $request) {
            $file = fopen('php://output', 'w');

            // Add UTF-8 BOM so Microsoft Excel opens currency and special characters cleanly
            fprintf($file, chr(0xEF) . chr(0xBB) . chr(0xBF));

            // Report Metadata Header Rows
            fputcsv($file, ['PLANT NURSERY INVENTORY MANAGEMENT SYSTEM - OFFICIAL REPORT']);
            fputcsv($file, [
                'Report Type:', strtoupper(str_replace('_', ' ', $request->input('report_type', 'all'))),
                'Category Filter:', $request->input('category') ?: 'All Categories',
                'Stock Filter:', $request->input('stock_status') ?: 'All Statuses',
                'Generated On:', now()->format('Y-m-d H:i:s'),
            ]);
            fputcsv($file, []); // Blank separator row

            // Table Column Headers
            fputcsv($file, [
                'Plant ID',
                'Plant Name',
                'Category',
                'Unit Price (PHP)',
                'Stock Status',
                'Description',
                'Created By (Admin)',
                'Date Added',
            ]);

            // Data Rows matching selected filters
            foreach ($plants as $plant) {
                fputcsv($file, [
                    $plant->id,
                    $plant->plant_name,
                    $plant->category,
                    number_format((float) $plant->unit_price, 2, '.', ''),
                    $plant->formatted_status,
                    $plant->description,
                    $plant->user->name ?? 'System Admin',
                    $plant->created_at?->format('Y-m-d'),
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
