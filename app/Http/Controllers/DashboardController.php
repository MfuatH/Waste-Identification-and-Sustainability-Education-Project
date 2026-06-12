<?php
namespace App\Http\Controllers;

use App\Models\Classification;

class DashboardController extends Controller
{
    public function index()
    {
        $totalScans = Classification::where('user_id', auth()->id())->count();

        $plastic = Classification::where('user_id', auth()->id())->where(
            'category',
            'Plastic Waste'
        )->count();

        $organic = Classification::where('user_id', auth()->id())->where(
            'category',
            'Organic Waste'
        )->count();

        $ewaste = Classification::where('user_id', auth()->id())->where(
            'category',
            'E-Waste'
        )->count();

        $recentActivities = Classification::where('user_id', auth()->id())
            ->latest()
            ->take(5)
            ->get();

        return view('dashboard.index',compact(
            'totalScans',
            'plastic',
            'organic',
            'ewaste',
            'recentActivities'
        ));
    }
}
