<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\News;
use App\Models\Category;
use App\Models\PushSubscription;
use App\Models\User;
use App\Services\SEOService;
use App\Helpers\NotificationHelper;
use App\Helpers\ImageHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use App\Events\NewsApproved;
use App\Events\NewsRejected;

class NewsController extends Controller
{
    protected $seoService;

    public function __construct(SEOService $seoService)
    {
        $this->seoService = $seoService;
    }

    public function index(Request $request)
    {
        try {
            $query = News::with(['user', 'category']);

            if ($request->filled('status')) {
                $query->where('status', $request->status);
            }

            if ($request->filled('category')) {
                $query->where('category_id', $request->category);
            }

            if ($request->filled('search')) {
                $query->where('title', 'LIKE', '%' . $request->search . '%');
            }

            $news = $query->orderByRaw("FIELD(status, 'pending', 'published', 'rejected', 'draft')")->paginate(20);

            $categories = Category::where('is_active', 1)->orderBy('order')->get();
            $statuses = ['pending', 'published', 'rejected', 'draft'];

            $counts = [
                'total' => News::count(),
                'pending' => News::where('status', 'pending')->count(),
                'published' => News::where('status', 'published')->count(),
                'rejected' => News::where('status', 'rejected')->count(),
                'draft' => News::where('status', 'draft')->count(),
            ];

            return view('admin.news.index', compact('news', 'categories', 'statuses', 'counts'));

        } catch (\Exception $e) {
            Log::error('❌ Admin News Index Error: ' . $e->getMessage());
            return back()->with('error', 'खबरें लोड करने में समस्या आ रही है।');
        }
    }

    public function create()
    {
        $categories = Category::where('is_active', 1)->orderBy('order')->get();
        $reporters = User::where('role', 'reporter')->orderBy('name')->get();

        return view('admin.news.create', compact('categories', 'reporters'));
    }

    public function show($id)
    {
        try {
            $news = News::with(['user', 'category'])->findOrFail($id);
            return view('admin.news.show', compact('news'));
        } catch (\Exception $e) {
            Log::error('❌ Admin News Show Error: ' . $e->getMessage());
            return redirect()->route('admin.news.index')->with('error', 'खबर नहीं मिली।');
        }
    }

    public function edit($id)
    {
        try {
            $news = News::findOrFail($id);
            $categories = Category::where('is_active', 1)->orderBy('order')->get();
            $statuses = ['pending', 'published', 'rejected', 'draft'];

            // ✅ Fetch reporters if needed for the view
            $reporters = User::where('role', 'reporter')->get();

            return view('admin.news.edit', compact('news', 'categories', 'statuses', 'reporters'));
        } catch (\Exception $e) {
            Log::error('❌ Admin News Edit Error: ' . $e->getMessage());
            return redirect()->route('admin.news.index')->with('error', 'खबर नहीं मिली।');
        }
    }

    /**
     * ✅ Update News - With Image Upload & Progress
     */
    public function update(Request $request, $id)
    {
        try {
            $news = News::findOrFail($id);

            $request->validate([
                'title' => 'required|string|max:255',
                'category_id' => 'required|exists:news_categories,id',
                'summary' => 'nullable|string',
                'body' => 'required|string',
                'status' => 'required|in:pending,published,rejected,draft',
                'is_breaking' => 'boolean',
                'is_featured' => 'boolean',
                'featured_image' => 'nullable|image|max:5120',
                'send_notification' => 'boolean',
            ]);

            // 🔒 Keep original slug
            $originalSlug = $news->slug;

            // ✅ Handle Featured Image with Optimization
            if ($request->hasFile('featured_image')) {
                // Delete old image
                if ($news->featured_image) {
                    ImageHelper::delete($news->featured_image);
                }
                
                $imagePath = ImageHelper::optimizeAndSave(
                    $request->file('featured_image'),
                    'news',
                    [
                        'max_width' => 1200,
                        'max_height' => 800,
                        'quality' => 80,
                        'max_size_kb' => 50,
                        'webp' => true,
                        'watermark' => false,
                    ]
                );
                
                if ($imagePath) {
                    $news->featured_image = $imagePath;
                    Log::info('✅ Admin: News image updated and optimized', [
                        'path' => $imagePath,
                        'news_id' => $news->id
                    ]);
                } else {
                    // If image optimization failed, keep old image
                    Log::warning('⚠️ Image optimization failed, keeping old image');
                }
            }

            // ✅ Update ONLY existing columns
            $news->title = $request->title;
            $news->category_id = $request->category_id;
            $news->summary = $request->summary;
            $news->body = $request->body;
            $news->status = $request->status;
            $news->is_breaking = $request->has('is_breaking');
            $news->is_featured = $request->has('is_featured');
            
            // ✅ Keep original slug
            if (!empty($originalSlug)) {
                $news->slug = $originalSlug;
            }

            // ✅ Update SEO fields ONLY if columns exist (check with Schema)
            if (Schema::hasColumn('news', 'meta_title')) {
                $news->meta_title = $request->title . ' - ' . config('app.name', 'द पब्लिक एक्सप्रेस');
            }
            if (Schema::hasColumn('news', 'meta_description')) {
                $news->meta_description = $request->summary ?? strip_tags(substr($request->body, 0, 160));
            }
            if (Schema::hasColumn('news', 'meta_keywords')) {
                $news->meta_keywords = $this->generateKeywords($request->title);
            }

            $news->save();

            Log::info('✅ News updated', ['news_id' => $news->id, 'admin_id' => Auth::id()]);

            // ✅ SEND PUSH NOTIFICATION IF PUBLISHED AND CHECKBOX CHECKED
            if ($news->status === 'published' && $request->has('send_notification')) {
                $sent = $this->sendNewsNotification($news, 'update');
                return redirect()->route('admin.news.index')
                    ->with('success', "✅ खबर अपडेट हो गई! Push Notification {$sent} subscribers को भेजी गई!");
            }

            return redirect()->route('admin.news.index')
                ->with('success', '✅ खबर अपडेट हो गई!');

        } catch (\Exception $e) {
            Log::error('❌ Admin News Update Error: ' . $e->getMessage());
            return back()->with('error', 'खबर अपडेट करने में समस्या आ रही है: ' . $e->getMessage());
        }
    }

    /**
     * ✅ Approve News – With Push Notification
     */
    public function approve($id)
    {
        try {
            $news = News::findOrFail($id);

            $news->update([
                'status' => 'published',
                'approval_status' => 'approved',
                'approved_by' => Auth::id(),
                'approved_at' => now(),
                'published_at' => now(),
                'rejection_reason' => null,
            ]);

            $newsUrl = route('news.show', $news->slug);
            Log::info('📰 News approved', ['news_id' => $news->id, 'admin_id' => Auth::id(), 'url' => $newsUrl]);

            // ✅ SEND PUSH NOTIFICATION TO ALL SUBSCRIBERS
            $sent = $this->sendNewsNotification($news, 'approve');

            // ✅ Fire Event: Reporter को Notification भेजें
            event(new NewsApproved($news));

            // ✅ Google Indexing API
            try {
                $googleResult = $this->seoService->submitToGoogleIndex($newsUrl);
                if ($googleResult) {
                    Log::info('✅ Google Indexing API Success', ['news_id' => $news->id, 'url' => $newsUrl]);
                } else {
                    Log::warning('⚠️ Google Indexing API Failed', ['news_id' => $news->id]);
                }
            } catch (\Exception $e) {
                Log::error('❌ Google Indexing API Exception', [
                    'news_id' => $news->id,
                    'error' => $e->getMessage()
                ]);
            }

            // ✅ Bing IndexNow
            if (env('BING_INDEXNOW_ENABLED', false)) {
                try {
                    $bingResult = $this->seoService->submitToBing($newsUrl);
                    if ($bingResult) {
                        Log::info('✅ Bing IndexNow Success', ['news_id' => $news->id, 'url' => $newsUrl]);
                    } else {
                        Log::warning('⚠️ Bing IndexNow Failed', ['news_id' => $news->id]);
                    }
                } catch (\Exception $e) {
                    Log::error('❌ Bing IndexNow Exception', [
                        'news_id' => $news->id,
                        'error' => $e->getMessage()
                    ]);
                }
            }

            return redirect()->route('admin.news.index')
                ->with('success', "✅ खबर स्वीकृत कर प्रकाशित कर दी गई! Push Notification {$sent} subscribers को भेजी गई!");

        } catch (\Exception $e) {
            Log::error('❌ Admin News Approve Error: ' . $e->getMessage());
            return back()->with('error', 'खबर स्वीकृत करने में समस्या आ रही है।');
        }
    }

    /**
     * ✅ Send Push Notification for News
     */
    private function sendNewsNotification($news, $action = 'approve')
    {
        try {
            $totalSubscribers = PushSubscription::where('is_active', true)->count();
            
            if ($totalSubscribers === 0) {
                Log::info('ℹ️ No active push subscribers to notify');
                return 0;
            }

            $categoryName = $news->category->name ?? 'नई खबर';
            
            if ($action === 'update') {
                $title = '📰 अपडेट: ' . $news->title;
            } else {
                $title = '📰 ' . $categoryName . ': ' . $news->title;
            }
            
            // ✅ Clean title for FCM
            $cleanTitle = mb_convert_encoding($title, 'UTF-8', 'UTF-8');
            $cleanTitle = preg_replace('/[\x00-\x1F\x7F-\x9F]/u', '', $cleanTitle);
            $cleanTitle = preg_replace('/[^\x{0009}\x{000A}\x{000D}\x{0020}-\x{D7FF}\x{E000}-\x{FFFD}]+/u', '', $cleanTitle);
            
            if (strlen($cleanTitle) > 100) {
                $cleanTitle = mb_substr($cleanTitle, 0, 97) . '...';
            }
            
            $body = $news->summary ?? strip_tags(substr($news->body, 0, 150));
            $cleanBody = mb_convert_encoding($body, 'UTF-8', 'UTF-8');
            $cleanBody = preg_replace('/[\x00-\x1F\x7F-\x9F]/u', '', $cleanBody);
            $cleanBody = preg_replace('/[^\x{0009}\x{000A}\x{000D}\x{0020}-\x{D7FF}\x{E000}-\x{FFFD}]+/u', '', $cleanBody);
            
            if (empty($cleanTitle)) {
                $cleanTitle = 'द पब्लिक एक्सप्रेस';
            }
            if (empty($cleanBody)) {
                $cleanBody = 'ताजा खबर!';
            }

            $data = [
                'news_id' => (string) $news->id,
                'category' => (string) $categoryName,
                'type' => 'news',
                'action' => (string) $action,
                'timestamp' => now()->toISOString(),
                'sound' => 'default',
                'vibrate' => '200,100,200',
            ];

            $clickAction = route('news.show', $news->slug);

            $sent = NotificationHelper::sendToAll($cleanTitle, $cleanBody, $data, $clickAction);

            Log::info("📢 Push notification sent for news ID: {$news->id}, Action: {$action}, Sent to: {$sent} subscribers");

            return $sent;

        } catch (\Exception $e) {
            Log::error('❌ Failed to send news notification: ' . $e->getMessage());
            return 0;
        }
    }

    /**
     * ✅ Generate Keywords
     */
    private function generateKeywords($title)
    {
        $keywords = [];
        $words = preg_split('/\s+/', strip_tags($title), -1, PREG_SPLIT_NO_EMPTY);
        $keywords = array_merge($keywords, $words);
        $keywords = array_unique($keywords);
        $keywords = array_slice($keywords, 0, 10);
        $keywords[] = 'द पब्लिक एक्सप्रेस';
        return implode(', ', $keywords);
    }

    /**
     * ✅ Reject News with Reason
     */
    public function reject(Request $request, $id)
    {
        try {
            $request->validate([
                'rejection_reason' => 'required|string|max:500',
            ]);

            $news = News::findOrFail($id);

            $news->update([
                'status' => 'rejected',
                'approval_status' => 'rejected',
                'rejected_at' => now(),
                'rejection_reason' => $request->rejection_reason,
            ]);

            Log::info('❌ News rejected', [
                'news_id' => $news->id,
                'admin_id' => Auth::id(),
                'reason' => $request->rejection_reason
            ]);

            event(new NewsRejected($news, $request->rejection_reason));

            return redirect()->route('admin.news.index')
                ->with('success', '❌ खबर अस्वीकृत कर दी गई!');

        } catch (\Exception $e) {
            Log::error('❌ Admin News Reject Error: ' . $e->getMessage());
            return back()->with('error', 'खबर अस्वीकृत करने में समस्या आ रही है।');
        }
    }

    public function destroy($id)
    {
        try {
            $news = News::find($id);
            if (!$news) {
                return redirect()->route('admin.news.index')->with('error', 'खबर नहीं मिली।');
            }

            if ($news->featured_image) {
                ImageHelper::delete($news->featured_image);
            }

            $news->delete();

            return redirect()->route('admin.news.index')->with('success', '✅ खबर डिलीट कर दी गई!');

        } catch (\Exception $e) {
            Log::error('❌ Admin News Delete Error: ' . $e->getMessage());
            return redirect()->route('admin.news.index')->with('error', 'खबर डिलीट करने में समस्या आ रही है।');
        }
    }

    public function breaking($id)
    {
        try {
            $news = News::findOrFail($id);
            $news->update(['is_breaking' => !$news->is_breaking]);

            if ($news->is_breaking && $news->status === 'published') {
                $this->sendBreakingNotification($news);
            }

            return redirect()->route('admin.news.index')
                ->with('success', $news->is_breaking ? '✅ Breaking News बन गई!' : '❌ Breaking News हटा दी गई!');

        } catch (\Exception $e) {
            Log::error('❌ Admin News Breaking Error: ' . $e->getMessage());
            return back()->with('error', 'समस्या आ रही है।');
        }
    }

    private function sendBreakingNotification($news)
    {
        try {
            $title = '🚨 BREAKING: ' . $news->title;
            $body = $news->summary ?? strip_tags(substr($news->body, 0, 150));
            
            $cleanTitle = mb_convert_encoding($title, 'UTF-8', 'UTF-8');
            $cleanTitle = preg_replace('/[\x00-\x1F\x7F-\x9F]/u', '', $cleanTitle);
            $cleanTitle = preg_replace('/[^\x{0009}\x{000A}\x{000D}\x{0020}-\x{D7FF}\x{E000}-\x{FFFD}]+/u', '', $cleanTitle);
            
            if (empty($cleanTitle)) {
                $cleanTitle = '🚨 BREAKING NEWS';
            }
            
            $cleanBody = mb_convert_encoding($body, 'UTF-8', 'UTF-8');
            $cleanBody = preg_replace('/[\x00-\x1F\x7F-\x9F]/u', '', $cleanBody);
            $cleanBody = preg_replace('/[^\x{0009}\x{000A}\x{000D}\x{0020}-\x{D7FF}\x{E000}-\x{FFFD}]+/u', '', $cleanBody);
            
            if (empty($cleanBody)) {
                $cleanBody = 'ताजा खबर!';
            }
            
            $data = [
                'news_id' => (string) $news->id,
                'type' => 'breaking',
                'timestamp' => now()->toISOString(),
                'sound' => 'default',
                'vibrate' => '300,100,200,100,300',
            ];

            $clickAction = route('news.show', $news->slug);

            $sent = NotificationHelper::sendToAll($cleanTitle, $cleanBody, $data, $clickAction);
            
            Log::info("🚨 Breaking news notification sent for news ID: {$news->id}, Sent to: {$sent} subscribers");

        } catch (\Exception $e) {
            Log::error('❌ Failed to send breaking notification: ' . $e->getMessage());
        }
    }

    public function featured($id)
    {
        try {
            $news = News::findOrFail($id);
            $news->update(['is_featured' => !$news->is_featured]);

            return redirect()->route('admin.news.index')
                ->with('success', $news->is_featured ? '✅ Featured News बन गई!' : '❌ Featured News हटा दी गई!');

        } catch (\Exception $e) {
            Log::error('❌ Admin News Featured Error: ' . $e->getMessage());
            return back()->with('error', 'समस्या आ रही है।');
        }
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'title' => 'required|string|max:255',
                'category_id' => 'required|exists:news_categories,id',
                'summary' => 'nullable|string',
                'body' => 'required|string',
                'status' => 'required|in:pending,published,rejected,draft',
                'is_breaking' => 'boolean',
                'is_featured' => 'boolean',
                'featured_image' => 'nullable|image|max:5120',
            ]);

            $news = new News();
            $news->title = $request->title;
            $news->slug = Str::slug($request->title) . '-' . time();
            $news->category_id = $request->category_id;
            $news->summary = $request->summary;
            $news->body = $request->body;
            $news->status = $request->status;
            $news->is_breaking = $request->has('is_breaking');
            $news->is_featured = $request->has('is_featured');
            $news->user_id = Auth::id();
            
            // ✅ Handle Featured Image with Optimization
            if ($request->hasFile('featured_image')) {
                $imagePath = ImageHelper::optimizeAndSave(
                    $request->file('featured_image'),
                    'news',
                    [
                        'max_width' => 1200,
                        'max_height' => 800,
                        'quality' => 80,
                        'max_size_kb' => 50,
                        'webp' => true,
                        'watermark' => false,
                    ]
                );
                
                if ($imagePath) {
                    $news->featured_image = $imagePath;
                    Log::info('✅ Admin: News image optimized and saved', [
                        'path' => $imagePath,
                        'news_id' => $news->id
                    ]);
                }
            }

            $news->save();

            Log::info('✅ News created', ['news_id' => $news->id, 'admin_id' => Auth::id()]);

            if ($news->status === 'published') {
                $sent = $this->sendNewsNotification($news, 'approve');
                return redirect()->route('admin.news.index')
                    ->with('success', "✅ खबर बन गई! Push Notification {$sent} subscribers को भेजी गई!");
            }

            return redirect()->route('admin.news.index')
                ->with('success', '✅ खबर बन गई!');

        } catch (\Exception $e) {
            Log::error('❌ Admin News Store Error: ' . $e->getMessage());
            return back()->with('error', 'खबर बनाने में समस्या आ रही है।');
        }
    }
}