<?php

namespace App\Http\Controllers;

use App\Models\CreativityType;
use Illuminate\View\View;

class PageController extends Controller
{
    public function home(): View
    {
        $types = CreativityType::orderBy('name')->get();

        $enrolledMasterClasses = collect();

        if (auth()->check() && auth()->user()->role === 'visitor') {
            $enrolledMasterClasses = auth()->user()
                ->enrolledMasterClasses()
                ->with(['creativityType', 'master'])
                ->orderBy('class_date')
                ->orderBy('start_time')
                ->get();
        }

        return view('home', compact('types', 'enrolledMasterClasses'));
    }

    public function category(int $id): View
    {
        $type = CreativityType::with([
            'masterClasses.master',
            'masterClasses.enrollments',
        ])->findOrFail($id);

        $allTypes = CreativityType::orderBy('name')->get();

        return view('category', compact('type', 'allTypes'));
    }
}
