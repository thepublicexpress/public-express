<?php
// app/Http/Controllers/Admin/NewsController.php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\News;
use App\Models\Category;
use App\Models\State;
use App\Models\District;
use App\Models\Tehsil;
use App\Models\Block;
use App\Models\User;
use App\Models\Notification;
use App\Models\ReporterPoint;

class NewsController extends Controller
{
    /**
     * Display a listing of the news.
     */
    public function index(Request $request)
    {
        $query = News::with(['user', 'category', 'state', 'district', 'tehsil', 'block']);

        // Search filter
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('slug', 'like', "%{$search}%")
                  ->orWhere('summary', 'like', "%{$search}%")
                  ->orWhere('body', 'like', "%{$search}%");
            });
        }

        // Status filter
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Category filter
        if ($request->filled('category')) {
            $query->where('category_id', $request->category);
        }

        // Location filter
        if ($request->filled('state_id')) {
            $query->where('state_id', $request->state_id);
        }

        if ($request->filled('district_id')) {
            $query->where('district_id', $request->district_id);
        }

        if ($request->filled('tehsil_id')) {
            $query->where('tehsil_id', $request->tehsil_id);
        }

        if ($request->filled('block_id')) {
            $query->where('block_id', $request->block_id);
        }

        // Date filter
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $news = $query->latest()->paginate(20);

        // Get filter data
        $categories = Category::where('is_active', true)->get();
        $states = State::where('is_active', true)->get();
        $districts = District::where('is_active', true)->get();
        $tehsils = Tehsil::where('is_active', true)->get();
        $blocks = Block::where('is_active', true)->get();

        // Stats
        $stats = [
            'total' => News::count(),
            'published' => News::where('status', 'published')->count(),
            'pending' => News::where('status', 'pending')->count(),
            'rejected' => News::where('status', 'rejected')->count(),
            'draft' => News::where('status', 'draft')->count(),
            'breaking' => News::where('is_breaking', true)->count(),
            'featured' => News::where('is_featured', true)->count(),
        ];

        return view('admin.news.index', compact('news', 'categories', 'states', 'districts', 'tehsils', 'blocks', 'stats'));
    }

    /**
     * Display the specified news.
     */
    public function show(News $news)
    {
        $news->load(['user', 'category', 'state', 'district', 'tehsil', 'block', 'approver']);
        return view('admin.news.show', compact('news'));
    }

    /**
     * Show the form for editing the specified news.
     */
    public function edit(News $news)
    {
        $categories = Category::where('is_active', true)->get();
        $states = State::where('is_active', true)->get();
        $districts = District::where('is_active', true)->get();
        $tehsils = Tehsil::where('is_active', true)->get();
        $blocks = Block::where('is_active', true)->get();
        
        // Get reporters for dropdown
        $reporters = User::whereIn('role', [
            'reporter', 
            'state_reporter', 
            'district_reporter', 
            'tehsil_reporter', 
            'block_reporter', 
            'national_reporter'
        ])
        ->where('is_active', true)
        ->orderBy('name')
        ->get();

        return view('admin.news.edit', compact(
            'news', 
            'categories', 
            'states', 
            'districts', 
            'tehsils', 
            'blocks', 
            'reporters'
        ));
    }

    /**
     * Update the specified news.
     */
    public function update(Request $request, News $news)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'status' => 'required|in:draft,pending,published,rejected',
            'summary' => 'nullable|string|max:500',
            'body' => 'required|string',
            'state_id' => 'nullable|exists:states,id',
            'district_id' => 'nullable|exists:districts,id',
            'tehsil_id' => 'nullable|exists:tehsils,id',
            'block_id' => 'nullable|exists:blocks,id',
            'is_breaking' => 'nullable|boolean',
            'is_featured' => 'nullable|boolean',
            'is_national' => 'nullable|boolean',
            'is_state' => 'nullable|boolean',
        ]);

        $news->update([
            'title' => $request->title,
            'category_id' => $request->category_id,
            'status' => $request->status,
            'summary' => $request->summary,
            'body' => $request->body,
            'state_id' => $request->state_id,
            'district_id' => $request->district_id,
            'tehsil_id' => $request->tehsil_id,
            'block_id' => $request->block_id,
            'is_breaking' => $request->is_breaking ?? false,
            'is_featured' => $request->is_featured ?? false,
            'is_national' => $request->is_national ?? false,
            'is_state' => $request->is_state ?? false,
        ]);

        if ($request->status == 'published') {
            $news->published_at = $news->published_at ?? now();
        }

        $news->save();

        return redirect()->route('admin.news.index')
            ->with('success', 'News updated successfully!');
    }

    /**
     * Approve news.
     */
    public function approve(News $news)
    {
        $user = auth()->user();

        if (!$news->canBeApprovedBy($user)) {
            return back()->with('error', 'You are not authorized to approve this news.');
        }

        $news->approve($user);

        return back()->with('success', 'News approved successfully! Reporter got 10 points.');
    }

    /**
     * Reject news with reason.
     */
    public function reject(Request $request, News $news)
    {
        $user = auth()->user();

        $request->validate([
            'rejection_reason' => 'required|string|min:10|max:500',
        ]);

        if (!$news->canBeApprovedBy($user)) {
            return back()->with('error', 'You are not authorized to reject this news.');
        }

        $news->reject($user, $request->rejection_reason);

        return back()->with('success', 'News rejected successfully! Reporter has been notified.');
    }

    /**
     * Toggle breaking news.
     */
    public function breaking(News $news)
    {
        $news->is_breaking = !$news->is_breaking;
        $news->save();

        $status = $news->is_breaking ? 'added to' : 'removed from';
        return back()->with('success', "News {$status} breaking news.");
    }

    /**
     * Toggle featured news.
     */
    public function featured(News $news)
    {
        $news->is_featured = !$news->is_featured;
        $news->save();

        $status = $news->is_featured ? 'added to' : 'removed from';
        return back()->with('success', "News {$status} featured news.");
    }

    /**
     * Remove the specified news.
     */
    public function destroy(News $news)
    {
        // Delete featured image
        if ($news->featured_image && file_exists(storage_path('app/public/' . $news->featured_image))) {
            unlink(storage_path('app/public/' . $news->featured_image));
        }

        $news->delete();

        return back()->with('success', 'News deleted successfully!');
    }
}