<?php

namespace App\Http\Controllers\Reporter;

use App\Http\Controllers\Controller;
use App\Models\News;
use App\Models\Category;
use App\Models\State;
use App\Models\District;
use App\Models\Tehsil;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class NewsController extends Controller
{
    public function index()
    {
        $news = News::where('user_id', auth()->id())
            ->orderBy('id', 'desc')
            ->paginate(10);

        return view('reporter.news.index', compact('news'));
    }

    public function create()
    {
        // एडमिन पैनल द्वारा केवल चालू (Active) की गई कैटेगरीज और राज्य ही दिखाई देंगे
        $categories = Category::where('is_active', true)->orderBy('name')->get();
        $states = State::where('is_active', true)->orderBy('name')->get();

        return view('reporter.news.create', compact('categories', 'states'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|max:255',
            'content' => 'required',
            'category_id' => 'required',
            'state_id' => 'required',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048'
        ]);

        $news = new News();
        $news->title = $request->title;
        $news->slug = Str::slug($request->title) . '-' . time();
        $news->content = $request->content;
        $news->summary = $request->summary;
        $news->category_id = $request->category_id;
        $news->state_id = $request->state_id;
        $news->district_id = $request->district_id;
        $news->tehsil_id = $request->tehsil_id;
        $news->user_id = auth()->id();
        $news->status = 'pending'; // रिपोर्टर की खबर पहले पेंडिंग रहेगी, एडमिन पास करेगा

        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('news/' . date('Y/m'), 'public');
            $news->image = $imagePath;
        }

        $news->save();

        return redirect()->route('reporter.news.index')->with('success', 'खबर सफलतापूर्वक सुरक्षित कर दी गई है और अनुमति के लिए एडमिन के पास भेज दी गई है।');
    }

    public function edit($id)
    {
        $news = News::where('user_id', auth()->id())->findOrFail($id);
        
        // एडमिन पैनल द्वारा केवल चालू (Active) की गई कैटेगरीज और राज्य ही दिखाई देंगे
        $categories = Category::where('is_active', true)->orderBy('name')->get();
        $states = State::where('is_active', true)->orderBy('name')->get();
        
        // पुराने चुने हुए राज्य और जिले के आधार पर एक्टिव जिलें और तहसीलें
        $districts = District::where('state_id', $news->state_id)->where('is_active', true)->orderBy('name')->get();
        $tehsils = Tehsil::where('district_id', $news->district_id)->where('is_active', true)->orderBy('name')->get();

        return view('reporter.news.edit', compact('news', 'categories', 'states', 'districts', 'tehsils'));
    }

    public function update(Request $request, $id)
    {
        $news = News::where('user_id', auth()->id())->findOrFail($id);

        $request->validate([
            'title' => 'required|max:255',
            'content' => 'required',
            'category_id' => 'required',
            'state_id' => 'required',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048'
        ]);

        $news->title = $request->title;
        $news->content = $request->content;
        $news->summary = $request->summary;
        $news->category_id = $request->category_id;
        $news->state_id = $request->state_id;
        $news->district_id = $request->district_id;
        $news->tehsil_id = $request->tehsil_id;

        if ($request->hasFile('image')) {
            if ($news->image) {
                Storage::disk('public')->delete($news->image);
            }
            $imagePath = $request->file('image')->store('news/' . date('Y/m'), 'public');
            $news->image = $imagePath;
        }

        $news->save();

        return redirect()->route('reporter.news.index')->with('success', 'खबर सफलतापूर्वक अपडेट कर दी गई है।');
    }

    public function show($id)
    {
        $news = News::where('user_id', auth()->id())->findOrFail($id);
        return view('reporter.news.show', compact('news'));
    }

    public function destroy($id)
    {
        $news = News::where('user_id', auth()->id())->findOrFail($id);
        if ($news->image) {
            Storage::disk('public')->delete($news->image);
        }
        $news->delete();

        return redirect()->route('reporter.news.index')->with('success', 'खबर सफलतापूर्वक हटा दी गई है।');
    }
}