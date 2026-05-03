<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdoptionApplication;
use App\Models\Payment;
use App\Models\Pet;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $year  = $request->get('year', now()->year);
        $month = $request->get('month');

        // --- Monthly Applications Chart (12 months of selected year) ---
        $monthlyApplications = AdoptionApplication::selectRaw(
                'MONTH(created_at) as month, COUNT(*) as total'
            )
            ->whereYear('created_at', $year)
            ->groupBy('month')
            ->pluck('total', 'month')
            ->toArray();

        $applicationChartData = [];
        for ($m = 1; $m <= 12; $m++) {
            $applicationChartData[] = $monthlyApplications[$m] ?? 0;
        }

        // --- Monthly Revenue Chart ---
        $monthlyRevenue = Payment::selectRaw(
                'MONTH(created_at) as month, SUM(amount) as total'
            )
            ->whereYear('created_at', $year)
            ->where('status', 'completed')
            ->groupBy('month')
            ->pluck('total', 'month')
            ->toArray();

        $revenueChartData = [];
        for ($m = 1; $m <= 12; $m++) {
            $revenueChartData[] = (float)($monthlyRevenue[$m] ?? 0);
        }

        // --- Application Status Breakdown (Pie) ---
        $statusBreakdown = AdoptionApplication::selectRaw('status, COUNT(*) as total')
            ->whereYear('created_at', $year)
            ->groupBy('status')
            ->pluck('total', 'status')
            ->toArray();

        // --- Pet Category Distribution ---
        $categoryDist = Pet::join('pet_categories', 'pets.pet_category_id', '=', 'pet_categories.id')
            ->selectRaw('pet_categories.name, COUNT(pets.id) as total')
            ->groupBy('pet_categories.name')
            ->pluck('total', 'name')
            ->toArray();

        // --- Summary Totals ---
        $totalRevenue    = Payment::where('status', 'completed')->whereYear('created_at', $year)->sum('amount');
        $totalApps       = AdoptionApplication::whereYear('created_at', $year)->count();
        $successfulAdopt = AdoptionApplication::where('status', 'completed')->whereYear('created_at', $year)->count();
        $newAdopters     = User::whereHas('roles', fn($q) => $q->where('name', 'adopter'))
                               ->whereYear('created_at', $year)->count();

        // --- Top Breeds Adopted ---
        $topBreeds = AdoptionApplication::join('pets', 'adoption_applications.pet_id', '=', 'pets.id')
            ->join('breeds', 'pets.breed_id', '=', 'breeds.id')
            ->selectRaw('breeds.name, COUNT(adoption_applications.id) as total')
            ->where('adoption_applications.status', 'completed')
            ->whereYear('adoption_applications.created_at', $year)
            ->groupBy('breeds.name')
            ->orderByDesc('total')
            ->limit(5)
            ->get();

        // --- Recent Completed Applications ---
        $recentAdoptions = AdoptionApplication::with(['pet', 'adopter'])
            ->where('status', 'completed')
            ->whereYear('created_at', $year)
            ->latest('completed_at')
            ->limit(8)
            ->get();

        // --- Available Years for filter ---
        $years = range(now()->year, 2020);

        return view('admin.reports.index', compact(
            'year', 'month',
            'applicationChartData', 'revenueChartData',
            'statusBreakdown', 'categoryDist',
            'totalRevenue', 'totalApps', 'successfulAdopt', 'newAdopters',
            'topBreeds', 'recentAdoptions', 'years'
        ));
    }

    /**
     * Export report as CSV.
     */
    public function export(Request $request)
    {
        $year = $request->get('year', now()->year);

        $applications = AdoptionApplication::with(['pet', 'adopter', 'payments'])
            ->whereYear('created_at', $year)
            ->get();

        $filename = "paws_report_{$year}.csv";
        $headers  = [
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function () use ($applications) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, [
                'App#', 'Pet', 'Adopter', 'Status',
                'Submitted', 'Completed', 'Fee Paid',
            ]);
            foreach ($applications as $app) {
                fputcsv($handle, [
                    $app->application_number,
                    $app->pet->name ?? 'N/A',
                    $app->adopter->full_name ?? 'N/A',
                    ucfirst($app->status),
                    $app->created_at->format('Y-m-d'),
                    optional($app->completed_at)->format('Y-m-d') ?? '-',
                    $app->payments->where('status', 'completed')->sum('amount'),
                ]);
            }
            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }
}