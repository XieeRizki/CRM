<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class TrendPageController extends Controller
{
    public function index()
    {
        $currentUser = auth()->user();

        // Ambil sales list hanya jika superadmin (role_id = 1)
        $salesList = [];
        if (($currentUser->role_id ?? null) == 1) {
            $salesList = User::where('role_id', 12)
                ->select('user_id', 'username')
                ->orderBy('username', 'asc')
                ->get();
        }

        return view('trendbulanan', [
            'currentUser' => $currentUser,
            'salesList' => $salesList,
        ]);
    }
}
