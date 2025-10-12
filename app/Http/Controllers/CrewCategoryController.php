<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\CrewCategory;

class CrewCategoryController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware(middleware: 'auth:wordpress');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $currentUserId = Auth::id();

        $crewCategories = CrewCategory::status('publish')
            ->latest('post_date')
            ->paginate(5);

        // Pass user to the view
        return view('crew-category', compact('crewCategories'));
    }
}
