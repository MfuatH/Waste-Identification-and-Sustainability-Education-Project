<?php
namespace App\Http\Controllers;

use App\Models\Education;

class EducationController extends Controller
{
    public function index()
    {
        $educations = Education::latest()->get();

        return view(
            'dashboard.education',
            compact('educations')
        );
    }

    public function show($slug)
    {
        $education = Education::where(
            'slug',
            $slug
        )->firstOrFail();

        return view(
            'dashboard.education-show',
            compact('education')
        );
    }
}