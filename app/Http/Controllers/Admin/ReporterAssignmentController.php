<?php
// app/Http/Controllers/Admin/ReporterAssignmentController.php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\State;
use App\Models\District;
use App\Models\Tehsil;
use App\Models\Block;
use App\Models\Category;
use App\Models\ReporterAssignment;
use Illuminate\Http\Request;

class ReporterAssignmentController extends Controller
{
    /**
     * Display all reporter assignments
     */
    public function index(Request $request)
    {
        $query = ReporterAssignment::with(['reporter', 'assignedState', 'assignedDistrict', 'assignedTehsil', 'assignedBlock', 'assignedCategory']);

        if ($request->filled('reporter_id')) {
            $query->where('reporter_id', $request->reporter_id);
        }

        if ($request->filled('state_id')) {
            $query->where('assigned_state_id', $request->state_id);
        }

        $assignments = $query->latest('assigned_at')->paginate(20);

        $reporters = User::where('role', 'reporter')->where('is_active', true)->get();
        $states = State::where('is_active', true)->get();
        $districts = District::where('is_active', true)->get();
        $tehsils = Tehsil::where('is_active', true)->get();
        $blocks = Block::where('is_active', true)->get();
        $categories = Category::where('is_active', true)->get();

        return view('admin.reporter-assignments.index', compact(
            'assignments', 'reporters', 'states', 'districts', 'tehsils', 'blocks', 'categories'
        ));
    }

    /**
     * Show form to create new assignment
     */
    public function create()
    {
        $reporters = User::where('role', 'reporter')
            ->where('is_active', true)
            ->where('is_approved', true)
            ->get();
        $states = State::where('is_active', true)->get();
        $categories = Category::where('is_active', true)->get();

        return view('admin.reporter-assignments.create', compact('reporters', 'states', 'categories'));
    }

    /**
     * Store new assignment
     */
    public function store(Request $request)
    {
        $request->validate([
            'reporter_id' => 'required|exists:users,id',
            'assigned_state_id' => 'nullable|exists:states,id',
            'assigned_district_id' => 'nullable|exists:districts,id',
            'assigned_tehsil_id' => 'nullable|exists:tehsils,id',
            'assigned_block_id' => 'nullable|exists:blocks,id',
            'assigned_category_id' => 'nullable|exists:categories,id',
            'assigned_categories' => 'nullable|array',
        ]);

        // Check if assignment already exists
        $existing = ReporterAssignment::where('reporter_id', $request->reporter_id)
            ->where('is_active', true)
            ->first();

        if ($existing) {
            return back()->with('error', 'This reporter already has an active assignment. Please deactivate it first.');
        }

        // Update user's assigned fields
        $user = User::find($request->reporter_id);
        $user->assigned_state_id = $request->assigned_state_id;
        $user->assigned_district_id = $request->assigned_district_id;
        $user->assigned_tehsil_id = $request->assigned_tehsil_id;
        $user->assigned_block_id = $request->assigned_block_id;
        $user->assigned_category_id = $request->assigned_category_id;
        $user->assigned_categories = $request->assigned_categories ? json_encode($request->assigned_categories) : null;
        $user->save();

        // Create assignment record
        ReporterAssignment::create([
            'reporter_id' => $request->reporter_id,
            'assigned_by' => auth()->id(),
            'assigned_state_id' => $request->assigned_state_id,
            'assigned_district_id' => $request->assigned_district_id,
            'assigned_tehsil_id' => $request->assigned_tehsil_id,
            'assigned_block_id' => $request->assigned_block_id,
            'assigned_category_id' => $request->assigned_category_id,
            'assigned_categories' => $request->assigned_categories ? json_encode($request->assigned_categories) : null,
            'is_active' => true,
            'assigned_at' => now(),
        ]);

        return redirect()->route('admin.reporter-assignments.index')
            ->with('success', 'Reporter assigned successfully!');
    }

    /**
     * Show form to edit assignment
     */
    public function edit(ReporterAssignment $assignment)
    {
        $reporters = User::where('role', 'reporter')->where('is_active', true)->get();
        $states = State::where('is_active', true)->get();
        $categories = Category::where('is_active', true)->get();

        return view('admin.reporter-assignments.edit', compact('assignment', 'reporters', 'states', 'categories'));
    }

    /**
     * Update assignment
     */
    public function update(Request $request, ReporterAssignment $assignment)
    {
        $request->validate([
            'reporter_id' => 'required|exists:users,id',
            'assigned_state_id' => 'nullable|exists:states,id',
            'assigned_district_id' => 'nullable|exists:districts,id',
            'assigned_tehsil_id' => 'nullable|exists:tehsils,id',
            'assigned_block_id' => 'nullable|exists:blocks,id',
            'assigned_category_id' => 'nullable|exists:categories,id',
            'assigned_categories' => 'nullable|array',
        ]);

        // Update user's assigned fields
        $user = User::find($request->reporter_id);
        $user->assigned_state_id = $request->assigned_state_id;
        $user->assigned_district_id = $request->assigned_district_id;
        $user->assigned_tehsil_id = $request->assigned_tehsil_id;
        $user->assigned_block_id = $request->assigned_block_id;
        $user->assigned_category_id = $request->assigned_category_id;
        $user->assigned_categories = $request->assigned_categories ? json_encode($request->assigned_categories) : null;
        $user->save();

        $assignment->update([
            'reporter_id' => $request->reporter_id,
            'assigned_state_id' => $request->assigned_state_id,
            'assigned_district_id' => $request->assigned_district_id,
            'assigned_tehsil_id' => $request->assigned_tehsil_id,
            'assigned_block_id' => $request->assigned_block_id,
            'assigned_category_id' => $request->assigned_category_id,
            'assigned_categories' => $request->assigned_categories ? json_encode($request->assigned_categories) : null,
            'updated_at' => now(),
        ]);

        return redirect()->route('admin.reporter-assignments.index')
            ->with('success', 'Assignment updated successfully!');
    }

    /**
     * Toggle assignment status
     */
    public function toggle(ReporterAssignment $assignment)
    {
        $assignment->is_active = !$assignment->is_active;
        $assignment->save();

        // Also update user's assigned fields
        $user = $assignment->reporter;
        if (!$assignment->is_active) {
            $user->assigned_state_id = null;
            $user->assigned_district_id = null;
            $user->assigned_tehsil_id = null;
            $user->assigned_block_id = null;
            $user->assigned_category_id = null;
            $user->assigned_categories = null;
            $user->save();
        }

        $status = $assignment->is_active ? 'activated' : 'deactivated';
        return back()->with('success', "Assignment {$status} successfully!");
    }

    /**
     * Delete assignment
     */
    public function destroy(ReporterAssignment $assignment)
    {
        // Remove user's assigned fields
        $user = $assignment->reporter;
        $user->assigned_state_id = null;
        $user->assigned_district_id = null;
        $user->assigned_tehsil_id = null;
        $user->assigned_block_id = null;
        $user->assigned_category_id = null;
        $user->assigned_categories = null;
        $user->save();

        $assignment->delete();

        return back()->with('success', 'Assignment deleted successfully!');
    }

    /**
     * Get districts by state (AJAX)
     */
    public function getDistricts($stateId)
    {
        $districts = District::where('state_id', $stateId)
            ->where('is_active', true)
            ->get(['id', 'name']);
        return response()->json($districts);
    }

    /**
     * Get tehsils by district (AJAX)
     */
    public function getTehsils($districtId)
    {
        $tehsils = Tehsil::where('district_id', $districtId)
            ->where('is_active', true)
            ->get(['id', 'name']);
        return response()->json($tehsils);
    }

    /**
     * Get blocks by tehsil (AJAX)
     */
    public function getBlocks($tehsilId)
    {
        $blocks = Block::where('tehsil_id', $tehsilId)
            ->where('is_active', true)
            ->get(['id', 'name']);
        return response()->json($blocks);
    }
}