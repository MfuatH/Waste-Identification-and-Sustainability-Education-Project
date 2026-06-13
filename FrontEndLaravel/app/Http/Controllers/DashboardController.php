<?php
namespace App\Http\Controllers;

use App\Models\Classification;

class DashboardController extends Controller
{
    public function index()
    {
        $totalScans = Classification::where('user_id', auth()->id())->count();

        $distributionData = Classification::where('user_id', auth()->id())
            ->selectRaw('category, COUNT(*) as total')
            ->groupBy('category')
            ->pluck('total', 'category')
            ->toArray();

        $anorganic = $distributionData['anorganik'] ?? 0;
        $organic = $distributionData['organik'] ?? 0;
        $ewaste = $distributionData['e-waste'] ?? 0;

        $distributionLabels = [
            'Inorganic Waste',
            'Organic Waste',
            'E-Waste',
        ];

        $distributionValues = [
            $anorganic,
            $organic,
            $ewaste,
        ];

        $recentActivities = Classification::where('user_id', auth()->id())
            ->latest()
            ->take(5)
            ->get();

        // Classification trend: scan count per day for the last 7 days
        $trendData = Classification::where('user_id', auth()->id())
            ->where('created_at', '>=', now()->subDays(6)->startOfDay())
            ->selectRaw('DATE(created_at) as date, COUNT(*) as total')
            ->groupBy('date')
            ->pluck('total', 'date');

        $trendLabels = [];
        $trendValues = [];

        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $trendLabels[] = $date->format('D, d M');
            $trendValues[] = $trendData[$date->format('Y-m-d')] ?? 0;
        }

        return view('dashboard.index',compact(
            'totalScans',
            'anorganic',
            'organic',
            'ewaste',
            'distributionLabels',
            'distributionValues',
            'recentActivities',
            'trendLabels',
            'trendValues'
        ));
    }
}
