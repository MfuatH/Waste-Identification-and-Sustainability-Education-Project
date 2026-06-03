<?php
namespace App\Http\Controllers;

use App\Models\Classification;
use Illuminate\Http\Request;

class ScannerController extends Controller
{
    public function index()
    {
        return view('dashboard.scanner');
    }

    public function history()
    {
        $histories = Classification::latest()
            ->paginate(10);

        return view(
            'dashboard.history',
            compact('histories')
        );
    }
}