<?php
namespace App\Http\Controllers;

use App\Models\Classification;

class DashboardController extends Controller
{
    public function index()
    {
        $totalScans = Classification::count();

        $plastic = Classification::where(
            'category',
            'Plastic Waste'
        )->count();

        $organic = Classification::where(
            'category',
            'Organic Waste'
        )->count();

        $ewaste = Classification::where(
            'category',
            'E-Waste'
        )->count();

        return view('dashboard.index',compact(
            'totalScans',
            'plastic',
            'organic',
            'ewaste'
        ));
    }
}