<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Post;

class PostController extends Controller
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

        $posts = Post::
            // where('post_author', $currentUserId)->
            status('publish')
            ->latest('post_date')
            ->take(10)
            ->get();

        // Pass user to the view
        return view('post', compact('posts'));
    }
}
