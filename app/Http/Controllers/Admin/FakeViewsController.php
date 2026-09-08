<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class FakeViewsController extends Controller
{
    public function index()
    {
        return view('admin.fake-views.index');
    }

    public function report()
    {
        return view('admin.fake-views.report');
    }

    public function markReal($id)
    {
        return redirect()->back()->with('success', '✅ Marked as real view');
    }

    public function markFake($id)
    {
        return redirect()->back()->with('success', '✅ Marked as fake view');
    }

    public function reporterAnalytics()
    {
        return view('admin.reporter-views.index');
    }

    public function reporterDetail($user)
    {
        return view('admin.reporter-views.detail', compact('user'));
    }
}