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

    public function store(Request $request)
    {
        $request->validate([
            'image' => 'required|image|max:10240',
            'category' => 'required|string|max:255',
            'confidence' => 'required|numeric|min:0|max:100',
            'recommendation' => 'nullable|string',
        ]);

        $imagePath = $request->file('image')->store('scans', 'public');

        $classification = Classification::create([
            'user_id' => auth()->id(),
            'image' => $imagePath,
            'category' => $request->category,
            'confidence' => $request->confidence,
            'recommendation' => $request->recommendation,
        ]);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Scan berhasil disimpan.',
                'classification' => $classification,
            ]);
        }

        return back()->with('success', 'Scan berhasil disimpan.');
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