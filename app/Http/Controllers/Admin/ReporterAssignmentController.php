<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ReporterAssignment;
use App\Models\User;
use App\Models\State;
use App\Models\District;
use App\Models\Tehsil;
use App\Models\Block;
use App\Models\Category; // ✅ NewsCategory → Category
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class ReporterAssignmentController extends Controller
{
    public function index(Request $request)
    {
        try {
            $query = ReporterAssignment::with([
                'reporter', 'assignedState', 'assignedDistrict',
                'assignedTehsil', 'assignedBlock', 'assignedCategory'
            ]);

            if ($request->filled('reporter_id')) {
                $query->where('reporter_id', $request->reporter_id);
            }

            if ($request->filled('state_id')) {
                $query->where('assigned_state_id', $request->state_id);
            }

            $assignments = $query->orderBy('assigned_at', 'desc')->paginate(20);

            $reporters = User::whereIn('role', [
                'reporter', 'state_reporter', 'district_reporter',
                'tehsil_reporter', 'block_reporter', 'national_reporter'
            ])->orderBy('name')->get();

            $states = State::where('is_active', 1)->orderBy('name')->get();

            return view('admin.reporter-assignments.index', compact('assignments', 'reporters', 'states'));

        } catch (\Exception $e) {
            Log::error('Reporter Assignment Index Error: ' . $e->getMessage());
            return back()->with('error', 'Assignments लोड करने में समस्या आ रही है।');
        }
    }

    public function create()
    {
        try {
            $reporters = User::whereIn('role', [
                'reporter', 'state_reporter', 'district_reporter',
                'tehsil_reporter', 'block_reporter', 'national_reporter'
            ])->orderBy('name')->get();

            $states = State::where('is_active', 1)->orderBy('name')->get();
            $categories = Category::where('is_active', 1)->orderBy('order')->get(); // ✅ Category model, order by 'order'

            return view('admin.reporter-assignments.create', compact('reporters', 'states', 'categories'));

        } catch (\Exception $e) {
            Log::error('Reporter Assignment Create Error: ' . $e->getMessage());
            return back()->with('error', 'Form लोड करने में समस्या आ रही है।');
        }
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'reporter_id' => 'required|exists:users,id',
                'assigned_state_id' => 'nullable|exists:states,id',
                'assigned_district_id' => 'nullable|exists:districts,id',
                'assigned_tehsil_id' => 'nullable|exists:tehsils,id',
                'assigned_block_id' => 'nullable|exists:blocks,id',
                'assigned_category_id' => 'nullable|exists:news_categories,id', // ✅ table name remains 'news_categories'
                'assigned_categories' => 'nullable|array',
                'assigned_categories.*' => 'exists:news_categories,id',
            ]);

            $assignment = ReporterAssignment::create([
                'reporter_id' => $request->reporter_id,
                'assigned_by' => Auth::id(),
                'assigned_state_id' => $request->assigned_state_id,
                'assigned_district_id' => $request->assigned_district_id,
                'assigned_tehsil_id' => $request->assigned_tehsil_id,
                'assigned_block_id' => $request->assigned_block_id,
                'assigned_category_id' => $request->assigned_category_id,
                'assigned_categories' => $request->assigned_categories ?? [],
                'is_active' => true,
                'assigned_at' => now(),
            ]);

            Log::info('Reporter assignment created', [
                'assignment_id' => $assignment->id,
                'reporter_id' => $request->reporter_id
            ]);

            return redirect()->route('admin.reporter-assignments.index')
                ->with('success', '✅ Assignment created successfully!');

        } catch (\Exception $e) {
            Log::error('Reporter Assignment Store Error: ' . $e->getMessage());
            return back()->with('error', 'Assignment बनाने में समस्या आ रही है।')->withInput();
        }
    }

    public function edit($id)
    {
        try {
            $assignment = ReporterAssignment::findOrFail($id);

            $reporters = User::whereIn('role', [
                'reporter', 'state_reporter', 'district_reporter',
                'tehsil_reporter', 'block_reporter', 'national_reporter'
            ])->orderBy('name')->get();

            $states = State::where('is_active', 1)->orderBy('name')->get();
            $categories = Category::where('is_active', 1)->orderBy('order')->get(); // ✅ Category model

            $districts = [];
            if ($assignment->assigned_state_id) {
                $districts = District::where('state_id', $assignment->assigned_state_id)
                    ->where('is_active', 1)->orderBy('name')->get();
            }

            $tehsils = [];
            if ($assignment->assigned_district_id) {
                $tehsils = Tehsil::where('district_id', $assignment->assigned_district_id)
                    ->where('is_active', 1)->orderBy('name')->get();
            }

            $blocks = [];
            if ($assignment->assigned_tehsil_id) {
                $blocks = Block::where('tehsil_id', $assignment->assigned_tehsil_id)
                    ->where('is_active', 1)->orderBy('name')->get();
            }

            return view('admin.reporter-assignments.edit', compact(
                'assignment', 'reporters', 'states', 'categories', 'districts', 'tehsils', 'blocks'
            ));

        } catch (\Exception $e) {
            Log::error('Reporter Assignment Edit Error: ' . $e->getMessage());
            return redirect()->route('admin.reporter-assignments.index')->with('error', 'Assignment नहीं मिला।');
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $assignment = ReporterAssignment::findOrFail($id);

            $request->validate([
                'reporter_id' => 'required|exists:users,id',
                'assigned_state_id' => 'nullable|exists:states,id',
                'assigned_district_id' => 'nullable|exists:districts,id',
                'assigned_tehsil_id' => 'nullable|exists:tehsils,id',
                'assigned_block_id' => 'nullable|exists:blocks,id',
                'assigned_category_id' => 'nullable|exists:news_categories,id',
                'assigned_categories' => 'nullable|array',
                'assigned_categories.*' => 'exists:news_categories,id',
            ]);

            $assignment->update([
                'reporter_id' => $request->reporter_id,
                'assigned_state_id' => $request->assigned_state_id,
                'assigned_district_id' => $request->assigned_district_id,
                'assigned_tehsil_id' => $request->assigned_tehsil_id,
                'assigned_block_id' => $request->assigned_block_id,
                'assigned_category_id' => $request->assigned_category_id,
                'assigned_categories' => $request->assigned_categories ?? [],
                'is_active' => $request->has('is_active'),
            ]);

            return redirect()->route('admin.reporter-assignments.index')
                ->with('success', '✅ Assignment updated successfully!');

        } catch (\Exception $e) {
            Log::error('Reporter Assignment Update Error: ' . $e->getMessage());
            return back()->with('error', 'Assignment अपडेट करने में समस्या आ रही है।')->withInput();
        }
    }

    public function toggle($id)
    {
        try {
            $assignment = ReporterAssignment::findOrFail($id);
            $assignment->update(['is_active' => !$assignment->is_active]);

            return redirect()->route('admin.reporter-assignments.index')
                ->with('success', '✅ Assignment toggled successfully!');

        } catch (\Exception $e) {
            Log::error('Reporter Assignment Toggle Error: ' . $e->getMessage());
            return back()->with('error', 'Assignment टॉगल करने में समस्या आ रही है।');
        }
    }

    public function destroy($id)
    {
        try {
            $assignment = ReporterAssignment::findOrFail($id);
            $assignment->delete();

            return redirect()->route('admin.reporter-assignments.index')
                ->with('success', '✅ Assignment deleted successfully!');

        } catch (\Exception $e) {
            Log::error('Reporter Assignment Delete Error: ' . $e->getMessage());
            return back()->with('error', 'Assignment डिलीट करने में समस्या आ रही है।');
        }
    }
}