<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TrackController extends Controller
{
    /**
     * Tampilkan daftar seluruh aktivitas foreman/mandor,
     * terbaru di atas, bisa difilter per user.
     */
    public function index(Request $request): View
    {
        $logs = ActivityLog::with('user')
            ->when($request->filled('user_id'), fn ($q) => $q->where('user_id', $request->user_id))
            ->when($request->filled('action'), fn ($q) => $q->where('action', $request->action))
            ->latest()
            ->paginate(20)
            ->withQueryString();

        // Daftar user yang pernah tercatat untuk filter dropdown
        $users = User::whereIn('id',
            ActivityLog::distinct()->pluck('user_id')
        )->orderBy('name')->get(['id', 'name']);

        return view('dash.track.index', [
            'logs' => $logs,
            'users' => $users,
        ]);
    }
}
