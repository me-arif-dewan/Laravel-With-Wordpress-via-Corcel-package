<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Project;

class ProjectController extends Controller
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

        $projects = Project::
            // where('post_author', $currentUserId)->
            status('publish')
            ->latest('post_date')
            ->take(10)
            ->get();

        // Pass user to the view
        return view('project', compact('projects'));
    }
}
