<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Show the global dashboard.
     *
     * Directors see a summary with charts (dummy data for now).
     * Foremen see a motivational message.
     */
    public function index(Request $request)
    {
        return view('dash.index');
    }
}
