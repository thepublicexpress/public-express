<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Ad;
use App\Models\State;
use App\Models\District;
use App\Models\Tehsil;
use App\Models\Block;
use App\Helpers\ImageHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Illuminate\Validation\ValidationException;

class AdController extends Controller
{
    // ============================================================
    // ✅ INDEX - Get ALL ads (NO FILTERS BY DEFAULT)
    // ============================================================
    public function index(Request $request)
    {
        try {
            // ✅ IMPORTANT: Get ALL ads - no filtering by default
            $query = Ad::with(['state', 'district', 'tehsil', 'block'])
                ->orderBy('created_at', 'desc');

            // ✅ Apply filters ONLY if provided
            if ($request->filled('position')) {
                $query->where('position', $request->position);
            }

            if ($request->filled('status')) {
                $query->where('status', $request->status);
            }

            if ($request->filled('search')) {
                $query->where(function($q) use ($request) {
                    $q->where('title', 'LIKE', '%' . $request->search . '%')
                      ->orWhere('description', 'LIKE', '%' . $request->search . '%');
                });
            }

            $ads = $query->paginate(20);

            // ✅ Debug log
            Log::info('📊 Ads fetched for admin', [
                'total' => $ads->total(),
                'count' => $ads->count(),
                'filters' => $request->all()
            ]);

            $positions = ['header', 'sidebar', 'in_content', 'footer', 'mobile'];
            $statuses = ['active', 'inactive', 'paused', 'expired'];

            return view('admin.ads.index', compact('ads', 'positions', 'statuses'));
            
        } catch (\Exception $e) {
            Log::error('❌ Ad Index Error: ' . $e->getMessage());
            return back()->with('error', 'Ads लोड करने में समस्या आ रही है।');
        }
    }

    // ============================================================
    // ✅ CREATE - Show create form
    // ============================================================
    public function create()
    {
        try {
            $states = State::where('is_active', 1)->orderBy('name')->get();
            $districts = District::where('is_active', 1)->orderBy('name')->get();
            $tehsils = Tehsil::where('is_active', 1)->orderBy('name')->get();
            $blocks = Block::where('is_active', 1)->orderBy('name')->get();

            $positions = [
                'header' => 'Header Banner (Top)',
                'sidebar' => 'Sidebar',
                'in_content' => 'In-Article (Between Content)',
                'footer' => 'Footer',
                'mobile' => 'Mobile Banner',
            ];

            $types = [
                'image' => 'Image Ad',
                'code' => 'Code Ad (AdSense/Media.net)',
                'text' => 'Text Ad',
            ];

            return view('admin.ads.create', compact('states', 'districts', 'tehsils', 'blocks', 'positions', 'types'));
            
        } catch (\Exception $e) {
            Log::error('❌ Ad Create View Error: ' . $e->getMessage());
            return redirect()->route('admin.ads.index')->with('error', 'विज्ञापन बनाने का पेज लोड नहीं हो पाया।');
        }
    }

    // ============================================================
    // ✅ STORE - Save new ad
    // ============================================================
    public function store(Request $request)
    {
        try {
            $request->validate([
                'title' => 'required|string|max:255',
                'type' => 'required|in:image,code,text',
                'image' => 'nullable|image|mimes:jpeg,png,gif,webp|max:5120',
                'code' => 'nullable|string',
                'description' => 'nullable|string|max:500',
                'url' => 'nullable|url',
                'position' => 'required|string',
                'state_id' => 'nullable|exists:states,id',
                'district_id' => 'nullable|exists:districts,id',
                'tehsil_id' => 'nullable|exists:tehsils,id',
                'block_id' => 'nullable|exists:blocks,id',
                'start_date' => 'nullable|date',
                'end_date' => 'nullable|date|after:start_date',
                'status' => 'required|in:active,inactive,paused',
                'priority' => 'nullable|integer|min:0|max:100',
            ]);

            $data = $request->all();
            
            // ✅ FORCE is_active to 1
            $data['is_active'] = 1;

            // ✅ Handle Image
            if ($request->hasFile('image')) {
                $imagePath = ImageHelper::optimizeAndSave(
                    $request->file('image'),
                    'ads',
                    [
                        'max_width' => 728,
                        'max_height' => 90,
                        'quality' => 80,
                        'max_size_kb' => 50,
                        'webp' => true,
                    ]
                );
                if ($imagePath) {
                    $data['image'] = $imagePath;
                }
            }

            // ✅ For code type, ensure code is provided
            if ($request->type === 'code' && empty($request->code)) {
                return back()->with('error', 'कृपया Code Ad के लिए कोड डालें।')->withInput();
            }

            $ad = Ad::create($data);

            // ✅ Clear ALL cache
            Cache::flush();

            Log::info('✅ Ad created', [
                'ad_id' => $ad->id, 
                'title' => $ad->title, 
                'is_active' => $ad->is_active,
                'status' => $ad->status
            ]);

            return redirect()->route('admin.ads.index')
                ->with('success', '✅ विज्ञापन सफलतापूर्वक बन गया!');

        } catch (ValidationException $e) {
            Log::error('❌ Ad Store Validation Error: ' . json_encode($e->errors()));
            return back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            Log::error('❌ Ad creation error: ' . $e->getMessage());
            return back()->with('error', '❌ विज्ञापन बनाने में समस्या आ रही है: ' . $e->getMessage())->withInput();
        }
    }

    // ============================================================
    // ✅ EDIT - Show edit form
    // ============================================================
    public function edit($id)
    {
        try {
            $ad = Ad::findOrFail($id);
            
            $states = State::where('is_active', 1)->orderBy('name')->get();
            $districts = District::where('is_active', 1)->orderBy('name')->get();
            $tehsils = Tehsil::where('is_active', 1)->orderBy('name')->get();
            $blocks = Block::where('is_active', 1)->orderBy('name')->get();

            $positions = [
                'header' => 'Header Banner (Top)',
                'sidebar' => 'Sidebar',
                'in_content' => 'In-Article (Between Content)',
                'footer' => 'Footer',
                'mobile' => 'Mobile Banner',
            ];

            $types = [
                'image' => 'Image Ad',
                'code' => 'Code Ad (AdSense/Media.net)',
                'text' => 'Text Ad',
            ];

            return view('admin.ads.edit', compact('ad', 'states', 'districts', 'tehsils', 'blocks', 'positions', 'types'));
            
        } catch (\Exception $e) {
            Log::error('❌ Ad Edit View Error: ' . $e->getMessage());
            return redirect()->route('admin.ads.index')->with('error', 'विज्ञापन नहीं मिला।');
        }
    }

    // ============================================================
    // ✅ UPDATE - Update ad
    // ============================================================
    public function update(Request $request, $id)
    {
        try {
            $ad = Ad::findOrFail($id);

            $request->validate([
                'title' => 'required|string|max:255',
                'type' => 'required|in:image,code,text',
                'image' => 'nullable|image|mimes:jpeg,png,gif,webp|max:5120',
                'code' => 'nullable|string',
                'description' => 'nullable|string|max:500',
                'url' => 'nullable|url',
                'position' => 'required|string',
                'state_id' => 'nullable|exists:states,id',
                'district_id' => 'nullable|exists:districts,id',
                'tehsil_id' => 'nullable|exists:tehsils,id',
                'block_id' => 'nullable|exists:blocks,id',
                'start_date' => 'nullable|date',
                'end_date' => 'nullable|date|after:start_date',
                'status' => 'required|in:active,inactive,paused',
                'priority' => 'nullable|integer|min:0|max:100',
            ]);

            $data = $request->all();
            
            // ✅ Keep is_active if provided, otherwise keep existing
            if (!isset($data['is_active'])) {
                $data['is_active'] = $ad->is_active ?? 1;
            }

            // ✅ Handle Image
            if ($request->hasFile('image')) {
                if ($ad->image) {
                    ImageHelper::delete($ad->image);
                }
                $imagePath = ImageHelper::optimizeAndSave(
                    $request->file('image'),
                    'ads',
                    [
                        'max_width' => 728,
                        'max_height' => 90,
                        'quality' => 80,
                        'max_size_kb' => 50,
                        'webp' => true,
                    ]
                );
                if ($imagePath) {
                    $data['image'] = $imagePath;
                }
            }

            // ✅ For code type, ensure code is provided
            if ($request->type === 'code' && empty($request->code)) {
                return back()->with('error', 'कृपया Code Ad के लिए कोड डालें।')->withInput();
            }

            $ad->update($data);

            // ✅ Clear ALL cache
            Cache::flush();

            Log::info('✅ Ad updated', [
                'ad_id' => $ad->id, 
                'title' => $ad->title, 
                'is_active' => $ad->is_active,
                'status' => $ad->status
            ]);

            return redirect()->route('admin.ads.index')
                ->with('success', '✅ विज्ञापन सफलतापूर्वक अपडेट हो गया!');

        } catch (ValidationException $e) {
            Log::error('❌ Ad Update Validation Error: ' . json_encode($e->errors()));
            return back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            Log::error('❌ Ad update error: ' . $e->getMessage());
            return back()->with('error', '❌ विज्ञापन अपडेट करने में समस्या आ रही है: ' . $e->getMessage())->withInput();
        }
    }

    // ============================================================
    // ✅ DESTROY - Delete ad
    // ============================================================
    public function destroy($id)
    {
        try {
            $ad = Ad::findOrFail($id);

            if ($ad->image) {
                ImageHelper::delete($ad->image);
            }

            $ad->delete();

            // ✅ Clear ALL cache
            Cache::flush();

            Log::info('🗑️ Ad deleted', ['ad_id' => $id]);

            return redirect()->route('admin.ads.index')
                ->with('success', '✅ विज्ञापन डिलीट कर दिया गया!');

        } catch (\Exception $e) {
            Log::error('❌ Ad deletion error: ' . $e->getMessage());
            return back()->with('error', '❌ विज्ञापन डिलीट करने में समस्या आ रही है।');
        }
    }

    // ============================================================
    // ✅ TOGGLE STATUS - Toggle active/inactive
    // ============================================================
    public function toggleStatus($id)
    {
        try {
            $ad = Ad::findOrFail($id);
            $ad->status = $ad->status === 'active' ? 'inactive' : 'active';
            $ad->save();

            Cache::flush();

            Log::info('🔄 Ad status toggled', ['ad_id' => $id, 'status' => $ad->status]);

            return redirect()->route('admin.ads.index')
                ->with('success', '✅ विज्ञापन स्टेटस अपडेट हो गया!');

        } catch (\Exception $e) {
            Log::error('❌ Ad status toggle error: ' . $e->getMessage());
            return back()->with('error', '❌ स्टेटस अपडेट करने में समस्या आ रही है।');
        }
    }

    // ============================================================
    // ✅ TOGGLE ACTIVE - Toggle is_active
    // ============================================================
    public function toggle($id)
    {
        try {
            $ad = Ad::findOrFail($id);
            $ad->is_active = !$ad->is_active;
            $ad->save();

            Cache::flush();

            Log::info('🔄 Ad active toggled', ['ad_id' => $id, 'is_active' => $ad->is_active]);

            return redirect()->route('admin.ads.index')
                ->with('success', $ad->is_active ? '✅ विज्ञापन सक्रिय कर दिया गया!' : '❌ विज्ञापन निष्क्रिय कर दिया गया!');

        } catch (\Exception $e) {
            Log::error('❌ Ad active toggle error: ' . $e->getMessage());
            return back()->with('error', '❌ समस्या आ रही है।');
        }
    }

    // ============================================================
    // ✅ STATS - View ad statistics
    // ============================================================
    public function stats($id)
    {
        try {
            $ad = Ad::findOrFail($id);
            return view('admin.ads.stats', compact('ad'));
        } catch (\Exception $e) {
            Log::error('❌ Ad stats error: ' . $e->getMessage());
            return redirect()->route('admin.ads.index')
                ->with('error', '❌ स्टेट्स लोड करने में समस्या आ रही है।');
        }
    }

    // ============================================================
    // ✅ AJAX: Get districts by state
    // ============================================================
    public function getDistricts($stateId)
    {
        try {
            $districts = District::where('state_id', $stateId)
                ->where('is_active', 1)
                ->orderBy('name')
                ->get();
            return response()->json($districts);
        } catch (\Exception $e) {
            Log::error('❌ Get Districts Error: ' . $e->getMessage());
            return response()->json([], 500);
        }
    }

    // ============================================================
    // ✅ AJAX: Get tehsils by district
    // ============================================================
    public function getTehsils($districtId)
    {
        try {
            $tehsils = Tehsil::where('district_id', $districtId)
                ->where('is_active', 1)
                ->orderBy('name')
                ->get();
            return response()->json($tehsils);
        } catch (\Exception $e) {
            Log::error('❌ Get Tehsils Error: ' . $e->getMessage());
            return response()->json([], 500);
        }
    }

    // ============================================================
    // ✅ AJAX: Get blocks by tehsil
    // ============================================================
    public function getBlocks($tehsilId)
    {
        try {
            $blocks = Block::where('tehsil_id', $tehsilId)
                ->where('is_active', 1)
                ->orderBy('name')
                ->get();
            return response()->json($blocks);
        } catch (\Exception $e) {
            Log::error('❌ Get Blocks Error: ' . $e->getMessage());
            return response()->json([], 500);
        }
    }
}