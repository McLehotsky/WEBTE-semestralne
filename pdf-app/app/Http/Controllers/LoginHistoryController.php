<?php

namespace App\Http\Controllers;

use App\Models\LoginHistory;

class LoginHistoryController extends Controller
{
    public function index()
    {
        $logs = LoginHistory::with('user')->orderByDesc('logged_in_at')->paginate(15);
        return view('admin.login-history.index', compact('logs'));
    }
}
