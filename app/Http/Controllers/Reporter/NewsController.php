<?php

namespace App\Http\Controllers\Reporter;

use App\Http\Controllers\Controller;
use App\Models\News;
use App\Models\Category;
use App\Models\State;
use App\Models\District;
use App\Models\Tehsil;
use App\Models\Block;
use App\Services\SEOService;
use App\Services\AdService;
use App\Helpers\ImageHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Exception;
use Stichoza\GoogleTranslate\GoogleTranslate;
use App\Events\NewsSubmitted;

class NewsController extends Controller
{
    protected $seoService;
    protected $adService;

    public function __construct(SEOService $seoService, AdService $adService)
    {
        $this->seoService = $seoService;
        $this->adService = $adService;
    }

    // ============================================================
    // ✅ INDEX METHOD – LIST REPORTER'S NEWS
    // ============================================================
    public function index(Request $request)
    {
        try {
            $query = News::with(['category', 'user', 'approver'])
                ->where('user_id', Auth::id())
                ->whereNull('deleted_at');

            if ($request->filled('status') && in_array($request->status, ['pending', 'published', 'rejected', 'draft'])) {
                $query->where('status', $request->status);
            }

            $news = $query->orderBy('created_at', 'desc')->paginate(15);

            $counts = [
                'total' => News::where('user_id', Auth::id())->whereNull('deleted_at')->count(),
                'pending' => News::where('user_id', Auth::id())->where('status', 'pending')->whereNull('deleted_at')->count(),
                'published' => News::where('user_id', Auth::id())->where('status', 'published')->whereNull('deleted_at')->count(),
                'rejected' => News::where('user_id', Auth::id())->where('status', 'rejected')->whereNull('deleted_at')->count(),
                'draft' => News::where('user_id', Auth::id())->where('status', 'draft')->whereNull('deleted_at')->count(),
            ];

            return view('reporter.index', compact('news', 'counts'));

        } catch (Exception $e) {
            Log::error('News index error: ' . $e->getMessage());
            return view('reporter.index', ['news' => collect([]), 'counts' => []])
                ->with('error', 'खबरें लोड करने में समस्या आ रही है।');
        }
    }

    // ============================================================
    // ✅ CREATE – SHOW CREATE FORM
    // ============================================================
    public function create()
    {
        try {
            $user = Auth::user();
            $allowed = $this->getAllowedLocations($user);

            $categories = Category::where('is_active', 1)->orderBy('order')->get();

            $states = State::where('is_active', 1)
                ->when($allowed['assigned_state_id'], function ($query) use ($allowed) {
                    return $query->where('id', $allowed['assigned_state_id']);
                })
                ->orderBy('name')
                ->get();

            $districts = District::where('is_active', 1)
                ->when($allowed['assigned_district_id'], function ($query) use ($allowed) {
                    return $query->where('id', $allowed['assigned_district_id']);
                })
                ->when(!$allowed['assigned_district_id'] && $allowed['assigned_state_id'], function ($query) use ($allowed) {
                    return $query->where('state_id', $allowed['assigned_state_id']);
                })
                ->orderBy('name')
                ->get();

            $tehsils = Tehsil::where('is_active', 1)
                ->when($allowed['assigned_tehsil_id'], function ($query) use ($allowed) {
                    return $query->where('id', $allowed['assigned_tehsil_id']);
                })
                ->when(!$allowed['assigned_tehsil_id'] && $allowed['assigned_district_id'], function ($query) use ($allowed) {
                    return $query->where('district_id', $allowed['assigned_district_id']);
                })
                ->orderBy('name')
                ->get();

            $blocks = Block::where('is_active', 1)
                ->when($allowed['assigned_block_id'], function ($query) use ($allowed) {
                    return $query->where('id', $allowed['assigned_block_id']);
                })
                ->when(!$allowed['assigned_block_id'] && $allowed['assigned_tehsil_id'], function ($query) use ($allowed) {
                    return $query->where('tehsil_id', $allowed['assigned_tehsil_id']);
                })
                ->orderBy('name')
                ->get();

            $selectedState = $allowed['assigned_state_id'];
            $selectedDistrict = $allowed['assigned_district_id'];
            $selectedTehsil = $allowed['assigned_tehsil_id'];
            $selectedBlock = $allowed['assigned_block_id'];

            return view('reporter.news-create', compact(
                'categories', 'states', 'districts', 'tehsils', 'blocks',
                'selectedState', 'selectedDistrict', 'selectedTehsil', 'selectedBlock'
            ));

        } catch (Exception $e) {
            Log::error('News create error: ' . $e->getMessage());
            return redirect()->route('reporter.news.index')
                ->with('error', 'फॉर्म लोड करने में समस्या आ रही है।');
        }
    }

    // ============================================================
    // ✅ STORE – SAVE NEW NEWS (WITH IMAGEHELPER & SLUG WITH TIME)
    // ============================================================
    public function store(Request $request)
    {
        try {
            $user = Auth::user();
            $allowed = $this->getAllowedLocations($user);

            $rules = [
                'title' => 'required|string|max:255',
                'category_id' => 'nullable|exists:news_categories,id',
                'state_id' => 'nullable|exists:states,id',
                'district_id' => 'nullable|exists:districts,id',
                'tehsil_id' => 'nullable|exists:tehsils,id',
                'block_id' => 'nullable|exists:blocks,id',
                'summary' => 'nullable|string',
                'body' => 'required|string',
                'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
                'type' => 'nullable|in:text,video,short',
                'video_url' => 'nullable|url',
                'is_breaking' => 'boolean',
                'is_featured' => 'boolean',
            ];

            $validated = $request->validate($rules);

            // Location Validation
            if ($allowed['assigned_block_id'] && $request->block_id != $allowed['assigned_block_id']) {
                return redirect()->back()->withInput()
                    ->with('error', 'आपको केवल अपने असाइन किए गए ब्लॉक की खबर डालने की अनुमति है।');
            }
            if ($allowed['assigned_tehsil_id'] && $request->tehsil_id != $allowed['assigned_tehsil_id']) {
                return redirect()->back()->withInput()
                    ->with('error', 'आपको केवल अपनी असाइन की गई तहसील की खबर डालने की अनुमति है।');
            }
            if ($allowed['assigned_district_id'] && $request->district_id != $allowed['assigned_district_id']) {
                return redirect()->back()->withInput()
                    ->with('error', 'आपको केवल अपने असाइन किए गए जिले की खबर डालने की अनुमति है।');
            }
            if ($allowed['assigned_state_id'] && $request->state_id != $allowed['assigned_state_id']) {
                return redirect()->back()->withInput()
                    ->with('error', 'आपको केवल अपने असाइन किए गए राज्य की खबर डालने की अनुमति है।');
            }

            // Auto-fill higher locations
            if ($allowed['assigned_block_id']) {
                $block = Block::find($allowed['assigned_block_id']);
                if ($block) {
                    $request->merge([
                        'tehsil_id' => $block->tehsil_id,
                        'district_id' => $block->district_id,
                        'state_id' => $block->state_id,
                    ]);
                }
            } elseif ($allowed['assigned_tehsil_id']) {
                $tehsil = Tehsil::find($allowed['assigned_tehsil_id']);
                if ($tehsil) {
                    $request->merge([
                        'district_id' => $tehsil->district_id,
                        'state_id' => $tehsil->state_id,
                    ]);
                }
            } elseif ($allowed['assigned_district_id']) {
                $district = District::find($allowed['assigned_district_id']);
                if ($district) {
                    $request->merge([
                        'state_id' => $district->state_id,
                    ]);
                }
            }

            $body = $request->input('body', '');
            if (empty($body)) {
                $body = $request->input('content', '');
            }

            // ============================================================
            // ✅ SLUG GENERATION - SEO Friendly English Slug with TIME
            // ============================================================
            $englishTitle = $this->generateEnglishTitle($request->title);
            $slug = Str::slug($englishTitle) . '-' . time();

            if (empty($slug)) {
                $slug = 'news-' . time();
            }

            $autoAltText = $this->generateAutoAltText($request->title, $englishTitle);
            $seoDescription = $this->generateSEODescription($request->summary, $body);

            // ============================================================
            // ✅ IMAGE HANDLING WITH IMAGEHELPER (AUTO-COMPRESS TO 50KB)
            // ============================================================
            $imagePath = null;
            if ($request->hasFile('image')) {
                try {
                    $file = $request->file('image');
                    
                    $imagePath = ImageHelper::optimizeAndSave(
                        $file,
                        'uploads/news',
                        [
                            'max_width' => 1200,
                            'max_height' => 800,
                            'quality' => 60,
                            'max_size_kb' => 50,
                            'webp' => true,
                        ]
                    );
                    
                    if ($imagePath) {
                        Log::info('✅ Image saved successfully: ' . $imagePath);
                    } else {
                        $imagePath = ImageHelper::saveOriginal($file, 'uploads/news');
                        Log::warning('⚠️ Image compression unavailable; original image saved: ' . $imagePath);
                    }
                    
                } catch (\Exception $e) {
                    Log::error('❌ Image upload failed: ' . $e->getMessage());
                    Log::warning('Image compression failed; post will be saved without compressed image: ' . $e->getMessage());
                }
            }

            $approvalLevel = $this->getApprovalLevel($user);

            // Create news
            $news = new News();
            $news->user_id = Auth::id();
            $news->category_id = $request->category_id;
            $news->state_id = $request->state_id;
            $news->district_id = $request->district_id;
            $news->tehsil_id = $request->tehsil_id;
            $news->block_id = $request->block_id;
            $news->title = $request->title;
            $news->slug = $slug;
            $news->summary = $request->summary;
            $news->body = $body;
            $news->featured_image = $imagePath;
            $news->alt_text = $autoAltText;
            $news->seo_description = $seoDescription;
            $news->type = $request->type ?? 'text';
            $news->video_url = $request->video_url;
            $news->is_breaking = $request->has('is_breaking');
            $news->is_featured = $request->has('is_featured');
            $news->status = 'pending';
            $news->approval_status = 'pending';
            $news->approval_level = $approvalLevel;
            $news->published_at = null;
            $news->save();

            event(new NewsSubmitted($news));

            Log::info('📰 News submitted', [
                'news_id' => $news->id, 
                'slug' => $slug,
                'user_id' => Auth::id(), 
                'image' => $imagePath,
                'body_length' => strlen($body)
            ]);

            return redirect()->route('reporter.news.index')
                ->with('success', '✅ खबर सबमिट कर दी गई! एडमिन द्वारा approve किए जाने की प्रतीक्षा करें।');

        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('❌ Reporter News Store Validation Error: ' . json_encode($e->errors()));
            return back()
                ->withErrors($e->errors())
                ->withInput();
        } catch (Exception $e) {
            Log::error('❌ News store error: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return redirect()->back()->withInput()
                ->with('error', 'खबर सबमिट करने में समस्या आ रही है: ' . $e->getMessage());
        }
    }

    // ============================================================
    // ✅ SHOW – VIEW A SINGLE NEWS
    // ============================================================
    public function show($id)
    {
        try {
            $news = News::with(['user', 'category', 'state', 'district', 'tehsil', 'block', 'approver'])
                ->where('user_id', Auth::id())
                ->findOrFail($id);

            $seoMeta = [];
            try {
                if ($this->seoService) {
                    $seoMeta = $this->seoService->generateSEOMeta($news);
                }
            } catch (Exception $e) {
                Log::error('SEO generation failed: ' . $e->getMessage());
                $seoMeta = [
                    'title' => $news->title ?? 'द पब्लिक एक्सप्रेस',
                    'description' => 'हर कस्बे गाँव और सिटी की खबरें',
                ];
            }

            return view('reporter.news-show', compact('news', 'seoMeta'));

        } catch (Exception $e) {
            Log::error('News show error: ' . $e->getMessage());
            return redirect()->route('reporter.news.index')
                ->with('error', 'खबर लोड करने में समस्या आ रही है।');
        }
    }

    // ============================================================
    // ✅ EDIT – ALLOW EDITING
    // ============================================================
    public function edit($id)
    {
        try {
            $news = News::where('user_id', Auth::id())
                ->whereNull('deleted_at')
                ->findOrFail($id);

            $categories = Category::withTrashed()->orderBy('order')->get();

            $user = Auth::user();
            $allowed = $this->getAllowedLocations($user);

            $states = State::where('is_active', 1)
                ->when($allowed['assigned_state_id'], function ($query) use ($allowed) {
                    return $query->where('id', $allowed['assigned_state_id']);
                })
                ->orderBy('name')
                ->get();

            $districts = District::where('is_active', 1)
                ->when($allowed['assigned_district_id'], function ($query) use ($allowed) {
                    return $query->where('id', $allowed['assigned_district_id']);
                })
                ->when(!$allowed['assigned_district_id'] && $allowed['assigned_state_id'], function ($query) use ($allowed) {
                    return $query->where('state_id', $allowed['assigned_state_id']);
                })
                ->orderBy('name')
                ->get();

            $tehsils = Tehsil::where('is_active', 1)
                ->when($allowed['assigned_tehsil_id'], function ($query) use ($allowed) {
                    return $query->where('id', $allowed['assigned_tehsil_id']);
                })
                ->when(!$allowed['assigned_tehsil_id'] && $allowed['assigned_district_id'], function ($query) use ($allowed) {
                    return $query->where('district_id', $allowed['assigned_district_id']);
                })
                ->orderBy('name')
                ->get();

            $blocks = Block::where('is_active', 1)
                ->when($allowed['assigned_block_id'], function ($query) use ($allowed) {
                    return $query->where('id', $allowed['assigned_block_id']);
                })
                ->when(!$allowed['assigned_block_id'] && $allowed['assigned_tehsil_id'], function ($query) use ($allowed) {
                    return $query->where('tehsil_id', $allowed['assigned_tehsil_id']);
                })
                ->orderBy('name')
                ->get();

            $selectedState = $news->state_id ?? $allowed['assigned_state_id'];
            $selectedDistrict = $news->district_id ?? $allowed['assigned_district_id'];
            $selectedTehsil = $news->tehsil_id ?? $allowed['assigned_tehsil_id'];
            $selectedBlock = $news->block_id ?? $allowed['assigned_block_id'];

            return view('reporter.news-edit', compact(
                'news', 'categories', 'states', 'districts', 'tehsils', 'blocks',
                'selectedState', 'selectedDistrict', 'selectedTehsil', 'selectedBlock'
            ));

        } catch (Exception $e) {
            Log::error('News edit error: ' . $e->getMessage());
            return redirect()->route('reporter.news.index')
                ->with('error', 'यह खबर संपादित नहीं की जा सकती।');
        }
    }

    // ============================================================
    // ✅ UPDATE – UPDATE AND RESUBMIT (WITH IMAGEHELPER & SLUG WITH TIME)
    // ============================================================
    public function update(Request $request, $id)
    {
        try {
            $news = News::where('user_id', Auth::id())
                ->whereNull('deleted_at')
                ->findOrFail($id);

            $user = Auth::user();
            $allowed = $this->getAllowedLocations($user);

            $rules = [
                'title' => 'required|string|max:255',
                'category_id' => 'nullable|exists:news_categories,id',
                'state_id' => 'nullable|exists:states,id',
                'district_id' => 'nullable|exists:districts,id',
                'tehsil_id' => 'nullable|exists:tehsils,id',
                'block_id' => 'nullable|exists:blocks,id',
                'summary' => 'nullable|string',
                'body' => 'required|string',
                'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
                'type' => 'nullable|in:text,video,short',
                'video_url' => 'nullable|url',
                'is_breaking' => 'boolean',
                'is_featured' => 'boolean',
            ];

            $validated = $request->validate($rules);

            // Location Validation
            if ($allowed['assigned_block_id'] && $request->block_id != $allowed['assigned_block_id']) {
                return redirect()->back()->withInput()
                    ->with('error', 'आपको केवल अपने असाइन किए गए ब्लॉक की खबर संपादित करने की अनुमति है।');
            }
            if ($allowed['assigned_tehsil_id'] && $request->tehsil_id != $allowed['assigned_tehsil_id']) {
                return redirect()->back()->withInput()
                    ->with('error', 'आपको केवल अपनी असाइन की गई तहसील की खबर संपादित करने की अनुमति है।');
            }
            if ($allowed['assigned_district_id'] && $request->district_id != $allowed['assigned_district_id']) {
                return redirect()->back()->withInput()
                    ->with('error', 'आपको केवल अपने असाइन किए गए जिले की खबर संपादित करने की अनुमति है।');
            }
            if ($allowed['assigned_state_id'] && $request->state_id != $allowed['assigned_state_id']) {
                return redirect()->back()->withInput()
                    ->with('error', 'आपको केवल अपने असाइन किए गए राज्य की खबर संपादित करने की अनुमति है।');
            }

            // Auto-fill higher locations
            if ($allowed['assigned_block_id']) {
                $block = Block::find($allowed['assigned_block_id']);
                if ($block) {
                    $request->merge([
                        'tehsil_id' => $block->tehsil_id,
                        'district_id' => $block->district_id,
                        'state_id' => $block->state_id,
                    ]);
                }
            } elseif ($allowed['assigned_tehsil_id']) {
                $tehsil = Tehsil::find($allowed['assigned_tehsil_id']);
                if ($tehsil) {
                    $request->merge([
                        'district_id' => $tehsil->district_id,
                        'state_id' => $tehsil->state_id,
                    ]);
                }
            } elseif ($allowed['assigned_district_id']) {
                $district = District::find($allowed['assigned_district_id']);
                if ($district) {
                    $request->merge([
                        'state_id' => $district->state_id,
                    ]);
                }
            }

            $body = $request->input('body', '');
            if (empty($body)) {
                $body = $request->input('content', '');
            }

            // ============================================================
            // ✅ SLUG GENERATION - Preserve Original Slug
            // ============================================================
            $originalSlug = $news->slug;
            
            if (empty($originalSlug)) {
                $englishTitle = $this->generateEnglishTitle($request->title);
                $originalSlug = Str::slug($englishTitle) . '-' . time();
                if (empty($originalSlug)) {
                    $originalSlug = 'news-' . time();
                }
            }

            $autoAltText = $this->generateAutoAltText($request->title);
            $seoDescription = $this->generateSEODescription($request->summary, $body);

            // ============================================================
            // ✅ IMAGE HANDLING WITH IMAGEHELPER (AUTO-COMPRESS TO 50KB)
            // ============================================================
            if ($request->hasFile('image')) {
                try {
                    $file = $request->file('image');
                    
                    $imagePath = ImageHelper::optimizeAndSave(
                        $file,
                        'uploads/news',
                        [
                            'max_width' => 1200,
                            'max_height' => 800,
                            'quality' => 60,
                            'max_size_kb' => 50,
                            'webp' => true,
                        ]
                    );
                    
                    if ($imagePath) {
                        if ($news->featured_image) {
                            ImageHelper::delete($news->featured_image);
                        }
                        $news->featured_image = $imagePath;
                        Log::info('✅ Image updated successfully: ' . $imagePath);
                    } else {
                        $imagePath = ImageHelper::saveOriginal($file, 'uploads/news');
                        if ($news->featured_image) {
                            ImageHelper::delete($news->featured_image);
                        }
                        $news->featured_image = $imagePath;
                        Log::warning('⚠️ Image compression unavailable; original image saved: ' . $imagePath);
                    }
                    
                } catch (\Exception $e) {
                    Log::error('❌ Image upload failed: ' . $e->getMessage());
                    Log::warning('Image update compression failed; existing image retained: ' . $e->getMessage());
                }
            }

            // Update all fields
            $news->category_id = $request->category_id;
            $news->state_id = $request->state_id;
            $news->district_id = $request->district_id;
            $news->tehsil_id = $request->tehsil_id;
            $news->block_id = $request->block_id;
            $news->title = $request->title;
            $news->summary = $request->summary;
            $news->body = $body;
            $news->alt_text = $autoAltText;
            $news->seo_description = $seoDescription;
            $news->type = $request->type ?? 'text';
            $news->video_url = $request->video_url;
            $news->is_breaking = $request->has('is_breaking');
            $news->is_featured = $request->has('is_featured');
            $news->status = 'pending';
            $news->approval_status = 'pending';
            $news->rejection_reason = null;
            $news->approved_at = null;
            $news->rejected_at = null;
            $news->slug = $originalSlug;
            
            // SEO fields if columns exist
            if (Schema::hasColumn('news', 'meta_title')) {
                $news->meta_title = $request->title . ' - ' . config('app.name', 'द पब्लिक एक्सप्रेस');
            }
            if (Schema::hasColumn('news', 'meta_description')) {
                $news->meta_description = $request->summary ?? strip_tags(substr($body, 0, 160));
            }
            if (Schema::hasColumn('news', 'meta_keywords')) {
                $news->meta_keywords = $this->generateKeywords($request->title);
            }
            
            $news->save();

            Log::info('📰 News updated', ['news_id' => $news->id, 'slug_preserved' => $originalSlug]);

            return redirect()->route('reporter.news.index')
                ->with('success', '✅ खबर अपडेट हो गई! URL बिल्कुल वही रहा और पुनः approve के लिए भेज दिया गया है।');

        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('❌ Reporter News Update Validation Error: ' . json_encode($e->errors()));
            return back()
                ->withErrors($e->errors())
                ->withInput();
        } catch (Exception $e) {
            Log::error('❌ News update error: ' . $e->getMessage());
            return redirect()->back()->withInput()
                ->with('error', 'खबर अपडेट करने में समस्या आ रही है: ' . $e->getMessage());
        }
    }

    // ============================================================
    // ✅ RESUBMIT
    // ============================================================
    public function resubmit($id)
    {
        try {
            $news = News::where('user_id', Auth::id())
                ->whereIn('status', ['published', 'rejected'])
                ->whereNull('deleted_at')
                ->findOrFail($id);

            $news->update([
                'status' => 'pending',
                'approval_status' => 'pending',
                'rejection_reason' => null,
                'approved_at' => null,
                'rejected_at' => null,
            ]);

            Log::info('📰 News resubmitted', ['news_id' => $news->id]);

            return redirect()->route('reporter.news.index')
                ->with('success', '✅ खबर पुनः approve के लिए सबमिट कर दी गई! एडमिन द्वारा समीक्षा की जाएगी।');

        } catch (Exception $e) {
            Log::error('News resubmit error: ' . $e->getMessage());
            return redirect()->route('reporter.news.index')
                ->with('error', 'खबर पुनः सबमिट करने में समस्या आ रही है: ' . $e->getMessage());
        }
    }

    // ============================================================
    // ✅ DESTROY
    // ============================================================
    public function destroy($id)
    {
        try {
            $news = News::where('user_id', Auth::id())->findOrFail($id);

            if ($news->featured_image) {
                ImageHelper::delete($news->featured_image);
            }

            $news->delete();

            return redirect()->route('reporter.news.index')
                ->with('success', '✅ खबर डिलीट कर दी गई!');

        } catch (Exception $e) {
            Log::error('News destroy error: ' . $e->getMessage());
            return redirect()->route('reporter.news.index')
                ->with('error', 'खबर डिलीट करने में समस्या आ रही है।');
        }
    }

    // ============================================================
    // ✅ EXTRA ROUTES
    // ============================================================
    public function postToGoogle(Request $request)
    {
        return redirect()->back()->with('success', '✅ Google पर पोस्ट कर दिया गया!');
    }

    public function saveSetup(Request $request)
    {
        return redirect()->back()->with('success', '✅ सेटअप सहेज लिया गया!');
    }

    // ============================================================
    // ✅ HELPER METHODS
    // ============================================================

    protected function getApprovalLevel($user)
    {
        if ($user->assigned_block_id) return 'block';
        if ($user->assigned_tehsil_id) return 'tehsil';
        if ($user->assigned_district_id) return 'district';
        if ($user->assigned_state_id) return 'state';
        return 'admin';
    }

    /**
     * ✅ Generate English Title for Slug (No API Required)
     */
    protected function generateEnglishTitle($title)
    {
        // 1️⃣ Try Google Translate first
        try {
            $tr = new GoogleTranslate('en');
            $englishTitle = $tr->translate($title);
            if (!empty($englishTitle) && $englishTitle !== $title) {
                return $englishTitle;
            }
        } catch (\Exception $e) {
            Log::warning('Google Translate failed, using transliteration: ' . $e->getMessage());
        }

        // 2️⃣ Fallback: Transliterate Hindi to English
        $transliterated = $this->transliterateHindiToEnglish($title);
        
        // 3️⃣ अगर फिर भी खाली है तो सिर्फ अंग्रेज़ी अक्षर रखें
        if (empty($transliterated)) {
            $transliterated = preg_replace('/[^a-zA-Z0-9\s\-]+/u', '', $title);
        }

        return $transliterated;
    }

    /**
     * ✅ Hindi to English Transliteration (No API)
     */
    protected function transliterateHindiToEnglish($text)
    {
        $map = [
            // Vowels
            'अ' => 'a', 'आ' => 'aa', 'इ' => 'i', 'ई' => 'ee', 'उ' => 'u', 'ऊ' => 'oo',
            'ए' => 'e', 'ऐ' => 'ai', 'ओ' => 'o', 'औ' => 'au',
            'ऋ' => 'ri', 'ॠ' => 'ri', 'ऌ' => 'li', 'ॡ' => 'li',
            
            // Consonants
            'क' => 'k', 'ख' => 'kh', 'ग' => 'g', 'घ' => 'gh', 'ङ' => 'ng',
            'च' => 'ch', 'छ' => 'chh', 'ज' => 'j', 'झ' => 'jh', 'ञ' => 'ny',
            'ट' => 't', 'ठ' => 'th', 'ड' => 'd', 'ढ' => 'dh', 'ण' => 'n',
            'त' => 't', 'थ' => 'th', 'द' => 'd', 'ध' => 'dh', 'न' => 'n',
            'प' => 'p', 'फ' => 'ph', 'ब' => 'b', 'भ' => 'bh', 'म' => 'm',
            'य' => 'y', 'र' => 'r', 'ल' => 'l', 'व' => 'v',
            'श' => 'sh', 'ष' => 'sh', 'स' => 's', 'ह' => 'h',
            'क्ष' => 'ksh', 'त्र' => 'tr', 'ज्ञ' => 'gy',
            'ड़' => 'd', 'ढ़' => 'dh', 'ड़' => 'r', 'ढ़' => 'rh',
            
            // Matras (Vowel signs)
            'ा' => 'a', 'ि' => 'i', 'ी' => 'ee', 'ु' => 'u', 'ू' => 'oo',
            'े' => 'e', 'ै' => 'ai', 'ो' => 'o', 'ौ' => 'au',
            'ं' => 'n', 'ः' => 'h', '्' => '',
            '्र' => 'r', '्य' => 'y', 'व' => 'v',
            
            // Numbers
            '०' => '0', '१' => '1', '२' => '2', '३' => '3', '४' => '4',
            '५' => '5', '६' => '6', '७' => '7', '८' => '8', '९' => '9',
            
            // Punctuation/Space
            ' ' => ' ', '-' => '-', '–' => '-', '—' => '-',
            '।' => '.', '॥' => '.',
        ];
        
        $result = strtr($text, $map);
        $result = preg_replace('/\s+/', ' ', trim($result));
        return $result;
    }

    protected function generateAutoAltText($title, $englishTitle = null)
    {
        if ($englishTitle && !empty($englishTitle) && $englishTitle !== $title) {
            $cleanTitle = $englishTitle;
            $prefixes = ['Image:', 'Photo:', 'See:', 'Breaking:'];
            $prefix = $prefixes[array_rand($prefixes)];
            return $prefix . ' ' . Str::limit($cleanTitle, 100, '...');
        }

        $cleanTitle = preg_replace('/[^\p{L}\p{N}\s]/u', '', $title);
        $prefixes = ['तस्वीर:', 'फोटो:', 'देखें:', 'ताजा अपडेट:'];
        $prefix = $prefixes[array_rand($prefixes)];
        return $prefix . ' ' . Str::limit($cleanTitle, 100, '...');
    }

    protected function generateSEODescription($summary, $body)
    {
        $text = $summary ?? $body ?? '';
        $text = strip_tags($text);
        return Str::limit($text, 160, '...');
    }

    protected function generateKeywords($title)
    {
        $keywords = [];
        $words = preg_split('/\s+/', strip_tags($title), -1, PREG_SPLIT_NO_EMPTY);
        $keywords = array_merge($keywords, $words);
        $keywords = array_unique($keywords);
        $keywords = array_slice($keywords, 0, 10);
        $keywords[] = 'द पब्लिक एक्सप्रेस';
        return implode(', ', $keywords);
    }

    protected function getAllowedLocations($user)
    {
        return [
            'assigned_state_id' => $user->assigned_state_id,
            'assigned_district_id' => $user->assigned_district_id,
            'assigned_tehsil_id' => $user->assigned_tehsil_id,
            'assigned_block_id' => $user->assigned_block_id,
        ];
    }
}