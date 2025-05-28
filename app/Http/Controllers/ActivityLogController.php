<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\ActivityLog; // Pastikan model di-import

class ActivityLogController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        // Ambil 10 log aktivitas terbaru untuk pengguna yang sedang login
        $activityLogs = $user->activityLogs()->latest()->paginate(10); // Menggunakan relasi

        return view('profile.activity-log', compact('activityLogs'));
    }
}
