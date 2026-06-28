<?php
// app/Http/Controllers/Reporter/NewsController.php

namespace App\Http\Controllers\Reporter;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\News;
use App\Models\Category;
use App\Models\State;
use App\Models\District;
use App\Models\Tehsil;
use App\Models\Block;
use App\Models\Notification;
use App\Models\ReporterPoint;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;

class NewsController extends Controller
{
    /**
     * Show create news form with dynamic location based on user role
     */
    public function create()
    {
        $user = auth()->user();
        
        // Get allowed categories based on assignment
        $categories = $user->getAllowedCategories();
        
        // Get location data based on role
        $locationData = $user->getAllowedLocations();
        
        // Get states for dropdown
        $states = State::where('is_active', true)->get();
        
        // Get districts, tehsils, blocks based on assignment
        $districts = collect();
        $tehsils = collect();
        $blocks = collect();
        
        if ($user->assigned_state_id) {
            $districts = District::where('state_id', $user->assigned_state_id)
                ->where('is_active', true)
                ->get();
        }
        
        if ($user->assigned_district_id) {
            $tehsils = Tehsil::where('district_id', $user->assigned_district_id)
                ->where('is_active', true)
                ->get();
        }
        
        if ($user->assigned_tehsil_id) {
            $blocks = Block::where('tehsil_id', $user->assigned_tehsil_id)
                ->where('is_active', true)
                ->get();
        }

        return view('reporter.news-create', compact(
            'categories', 'states', 'districts', 'tehsils', 'blocks', 'locationData', 'user'
        ));
    }

    /**
     * Store news with validation for category and location
     */
    public function store(Request $request)
    {
        $user = auth()->user();

        $request->validate([
            'title' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'summary' => 'nullable|string|max:500',
            'body' => 'required|string',
            'featured_image' => 'nullable|image|max:2048',
            'state_id' => 'nullable|exists:states,id',
            'district_id' => 'nullable|exists:districts,id',
            'tehsil_id' => 'nullable|exists:tehsils,id',
            'block_id' => 'nullable|exists:blocks,id',
        ]);

        // ===== CHECK 1: Category Permission =====
        if (!$user->canWriteCategory($request->category_id)) {
            return back()->with('error', 'You are not allowed to write in this category. Please select an allowed category.')
                ->withInput();
        }

        // ===== CHECK 2: Location Permission =====
        // If not national reporter, location is required
        if (!$user->isNationalReporter()) {
            if (!$request->state_id) {
                return back()->with('error', 'Please select a state.')->withInput();
            }
            
            if (!$user->canWriteLocation(
                $request->state_id, 
                $request->district_id, 
                $request->tehsil_id, 
                $request->block_id
            )) {
                return back()->with('error', 'You are not allowed to write for this location.')->withInput();
            }
        }

        // Upload image
        $imagePath = null;
        if ($request->hasFile('featured_image')) {
            $image = $request->file('featured_image');
            $filename = time() . '_' . $image->getClientOriginalName();
            $yearMonth = date('Y/m');
            $path = $image->storeAs('public/news/' . $yearMonth, $filename);
            $imagePath = 'news/' . $yearMonth . '/' . $filename;
        }

        // Create news
        $news = News::create([
            'user_id' => auth()->id(),
            'category_id' => $request->category_id,
            'state_id' => $request->state_id,
            'district_id' => $request->district_id,
            'tehsil_id' => $request->tehsil_id,
            'block_id' => $request->block_id,
            'is_national' => $user->isNationalReporter() && !$request->state_id,
            'title' => $request->title,
            'slug' => Str::slug($request->title) . '-' . time(),
            'summary' => $request->summary,
            'body' => $request->body,
            'featured_image' => $imagePath,
            'status' => 'pending',
        ]);

        // Notify admins
        $this->notifyAdmins($news);

        return redirect()->route('reporter.dashboard')
            ->with('success', 'News submitted for approval!');
    }

    /**
     * Notify admins about new news
     */
    private function notifyAdmins($news)
    {
        $admins = User::where('role', 'admin')
            ->orWhere('role', 'super_admin')
            ->get();

        foreach ($admins as $admin) {
            Notification::create([
                'user_id' => $admin->id,
                'news_id' => $news->id,
                'type' => 'news_pending',
                'title' => '📰 नई खबर लंबित',
                'message' => 'एक नई खबर "' . $news->title . '" सबमिट हुई है।',
                'is_read' => false,
            ]);
        }
    }

    /**
     * Display a listing of reporter's news
     */
    public function index()
    {
        $user = auth()->user();
        
        $query = News::where('user_id', $user->id);

        $news = $query->with(['category', 'state', 'district', 'tehsil', 'block'])
            ->latest()
            ->paginate(10);

        return view('reporter.news-list', compact('news'));
    }

    /**
     * Show the form for editing the specified news
     */
    public function edit($id)
    {
        $user = auth()->user();
        
        $news = News::where('user_id', $user->id)
            ->where('id', $id)
            ->firstOrFail();

        // Only allow edit if status is pending or draft
        if (!in_array($news->status, ['pending', 'draft'])) {
            if ($news->status == 'rejected') {
                return redirect()->route('reporter.news.show', $news->id)
                    ->with('error', 'This news was rejected. You can view it but cannot edit.');
            }
            return redirect()->route('reporter.news.index')
                ->with('error', 'You cannot edit published news.');
        }

        $categories = $user->getAllowedCategories();
        $states = State::where('is_active', true)->get();
        $districts = District::where('is_active', true)->get();
        $tehsils = Tehsil::where('is_active', true)->get();
        $blocks = Block::where('is_active', true)->get();

        return view('reporter.news-edit', compact('news', 'categories', 'states', 'districts', 'tehsils', 'blocks'));
    }

    /**
     * Update the specified news
     */
    public function update(Request $request, $id)
    {
        $user = auth()->user();
        
        $news = News::where('user_id', $user->id)
            ->where('id', $id)
            ->firstOrFail();

        // Only allow update if status is pending or draft
        if (!in_array($news->status, ['pending', 'draft'])) {
            return redirect()->route('reporter.news.index')
                ->with('error', 'You cannot edit this news.');
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'summary' => 'nullable|string|max:500',
            'body' => 'required|string',
            'featured_image' => 'nullable|image|max:2048',
            'state_id' => 'nullable|exists:states,id',
            'district_id' => 'nullable|exists:districts,id',
            'tehsil_id' => 'nullable|exists:tehsils,id',
            'block_id' => 'nullable|exists:blocks,id',
        ]);

        // ===== CHECK 1: Category Permission =====
        if (!$user->canWriteCategory($request->category_id)) {
            return back()->with('error', 'You are not allowed to write in this category.')->withInput();
        }

        // ===== CHECK 2: Location Permission =====
        if (!$user->isNationalReporter()) {
            if (!$request->state_id) {
                return back()->with('error', 'Please select a state.')->withInput();
            }
            
            if (!$user->canWriteLocation(
                $request->state_id, 
                $request->district_id, 
                $request->tehsil_id, 
                $request->block_id
            )) {
                return back()->with('error', 'You are not allowed to write for this location.')->withInput();
            }
        }

        // Upload image
        if ($request->hasFile('featured_image')) {
            if ($news->featured_image && file_exists(storage_path('app/public/' . $news->featured_image))) {
                unlink(storage_path('app/public/' . $news->featured_image));
            }
            
            $image = $request->file('featured_image');
            $filename = time() . '_' . $image->getClientOriginalName();
            $yearMonth = date('Y/m');
            $path = $image->storeAs('public/news/' . $yearMonth, $filename);
            $news->featured_image = 'news/' . $yearMonth . '/' . $filename;
        }

        $news->title = $request->title;
        $news->slug = Str::slug($request->title) . '-' . time();
        $news->category_id = $request->category_id;
        $news->summary = $request->summary;
        $news->body = $request->body;
        $news->state_id = $request->state_id;
        $news->district_id = $request->district_id;
        $news->tehsil_id = $request->tehsil_id;
        $news->block_id = $request->block_id;
        $news->is_national = $user->isNationalReporter() && !$request->state_id;
        $news->status = 'pending'; // Resubmit for approval
        $news->save();

        return redirect()->route('reporter.news.index')
            ->with('success', 'News updated and resubmitted for approval!');
    }

    /**
     * Display the specified news
     */
    public function show($id)
    {
        $news = News::where('user_id', auth()->id())
            ->where('id', $id)
            ->with(['category', 'state', 'district', 'tehsil', 'block'])
            ->firstOrFail();

        return view('reporter.news-show', compact('news'));
    }

    /**
     * Remove the specified news
     */
    public function destroy($id)
    {
        $user = auth()->user();
        
        $news = News::where('user_id', $user->id)
            ->where('id', $id)
            ->firstOrFail();

        if (!in_array($news->status, ['pending', 'draft'])) {
            return redirect()->route('reporter.news.index')
                ->with('error', 'You cannot delete this news.');
        }

        if ($news->featured_image && file_exists(storage_path('app/public/' . $news->featured_image))) {
            unlink(storage_path('app/public/' . $news->featured_image));
        }

        $news->delete();

        return redirect()->route('reporter.news.index')
            ->with('success', 'News deleted successfully.');
    }
}