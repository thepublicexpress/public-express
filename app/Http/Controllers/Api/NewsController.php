<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Models\{News, NewsCategory};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
class NewsController extends Controller {
    // Public: Feed
    public function feed(Request $request) {
        $query = News::published()->with('author:id,name,avatar','category','district');
        if ($request->category) $query->where('category_id', $request->category);
        if ($request->district) $query->where('district_id', $request->district);
        if ($request->type)     $query->where('type', $request->type);
        return response()->json($query->latest('published_at')->paginate(20));
    }
    
    // Public: Single news
    public function show(News $news) {
        $news->increment('views');
        return response()->json($news->load('author:id,name,avatar','category','district','tehsil'));
    }
    
    // Reporter: Submit news
    public function store(Request $request) {
        $request->validate([
            'title'       => 'required|min:10|max:200',
            'summary'     => 'required|min:20',
            'body'        => 'nullable',
            'category_id' => 'required|exists:news_categories,id',
            'district_id' => 'required|exists:districts,id',
            'tehsil_id'   => 'nullable|exists:tehsils,id',
            'type'        => 'required|in:text,video,short',
            'video_url'   => 'nullable|url',
            'image'       => 'nullable|image|max:5120',
        ]);
        $data = $request->except('image');
        $data['user_id'] = Auth::id();
        $data['status']  = 'pending';
        if ($request->hasFile('image')) {
            $data['featured_image'] = $request->file('image')->store('news', 'public');
        }
        $news = News::create($data);
        return response()->json(['message' => 'खबर सबमिट हो गई, समीक्षा के बाद प्रकाशित होगी।', 'news' => $news], 201);
    }
    
    // Reporter: My news
    public function myNews() {
        return response()->json(Auth::user()->news()->with('category','district')->latest()->paginate(20));
    }
    
    // Like
    public function like(News $news) {
        $news->increment('likes');
        return response()->json(['likes' => $news->fresh()->likes]);
    }
}
