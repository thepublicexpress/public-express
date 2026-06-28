<?php
// app/Http/Controllers/Admin/UserController.php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\State;
use App\Models\District;
use App\Models\Tehsil;
use App\Models\Block;
use App\Models\Category;
use App\Models\ReporterAssignment;
use App\Models\ReporterPoint;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    /**
     * Display a listing of the users.
     */
    public function index(Request $request)
    {
        $query = User::query();

        // Search filter
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        // Role filter
        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        // Status filter
        if ($request->filled('status')) {
            $query->where('is_active', $request->status == 'active');
        }

        // Approval filter
        if ($request->filled('approved')) {
            $query->where('is_approved', $request->approved == 'approved');
        }

        $users = $query->latest()->paginate(20);

        // Stats
        $stats = [
            'total' => User::count(),
            'admins' => User::whereIn('role', ['admin', 'super_admin', 'state_admin', 'district_admin', 'tehsil_admin', 'block_admin'])->count(),
            'reporters' => User::whereIn('role', ['reporter', 'state_reporter', 'district_reporter', 'tehsil_reporter', 'block_reporter', 'national_reporter'])->count(),
            'subscribers' => User::where('role', 'subscriber')->count(),
            'pending' => User::where('is_approved', false)->count(),
            'active' => User::where('is_active', true)->count(),
        ];

        $roles = ['super_admin', 'admin', 'state_admin', 'district_admin', 'tehsil_admin', 'block_admin', 
                  'state_reporter', 'district_reporter', 'tehsil_reporter', 'block_reporter', 'national_reporter', 
                  'reporter', 'subscriber'];

        return view('admin.users.index', compact('users', 'stats', 'roles'));
    }

    /**
     * Show the form for creating a new user.
     */
    public function create()
    {
        $roles = ['super_admin', 'admin', 'state_admin', 'district_admin', 'tehsil_admin', 'block_admin', 
                  'state_reporter', 'district_reporter', 'tehsil_reporter', 'block_reporter', 'national_reporter', 
                  'reporter', 'subscriber'];
        $states = State::where('is_active', true)->get();
        $districts = District::where('is_active', true)->get();
        $tehsils = Tehsil::where('is_active', true)->get();
        $blocks = Block::where('is_active', true)->get();
        $categories = Category::where('is_active', true)->get();

        return view('admin.users.create', compact('roles', 'states', 'districts', 'tehsils', 'blocks', 'categories'));
    }

    /**
     * Store a newly created user.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'phone' => 'nullable|string|max:20',
            'password' => 'required|min:8|confirmed',
            'role' => 'required|in:super_admin,admin,state_admin,district_admin,tehsil_admin,block_admin,state_reporter,district_reporter,tehsil_reporter,block_reporter,national_reporter,reporter,subscriber',
            'assigned_state_id' => 'nullable|exists:states,id',
            'assigned_district_id' => 'nullable|exists:districts,id',
            'assigned_tehsil_id' => 'nullable|exists:tehsils,id',
            'assigned_block_id' => 'nullable|exists:blocks,id',
            'assigned_category_id' => 'nullable|exists:categories,id',
            'assigned_categories' => 'nullable|array',
            'can_approve' => 'nullable|boolean',
            'approval_level' => 'nullable|in:none,block,tehsil,district,state,national',
            'is_active' => 'nullable|boolean',
            'is_approved' => 'nullable|boolean',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'password' => Hash::make($request->password),
            'role' => $request->role,
            'assigned_state_id' => $request->assigned_state_id,
            'assigned_district_id' => $request->assigned_district_id,
            'assigned_tehsil_id' => $request->assigned_tehsil_id,
            'assigned_block_id' => $request->assigned_block_id,
            'assigned_category_id' => $request->assigned_category_id,
            'assigned_categories' => $request->assigned_categories ? json_encode($request->assigned_categories) : null,
            'can_approve' => $request->can_approve ?? false,
            'approval_level' => $request->approval_level ?? 'none',
            'is_active' => $request->is_active ?? true,
            'is_approved' => $request->is_approved ?? false,
        ]);

        // Create reporter assignment if reporter
        if (in_array($request->role, ['reporter', 'state_reporter', 'district_reporter', 'tehsil_reporter', 'block_reporter', 'national_reporter'])) {
            ReporterAssignment::create([
                'reporter_id' => $user->id,
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
        }

        return redirect()->route('admin.users.index')
            ->with('success', 'User created successfully!');
    }

    /**
     * Display the specified user.
     */
    public function show(User $user)
    {
        $user->load(['state', 'district', 'tehsil', 'block', 'category']);
        $news = $user->news()->latest()->paginate(10);
        $assignments = ReporterAssignment::where('reporter_id', $user->id)->get();

        return view('admin.users.show', compact('user', 'news', 'assignments'));
    }

    /**
     * Show the form for editing the specified user.
     */
    public function edit(User $user)
    {
        $roles = ['super_admin', 'admin', 'state_admin', 'district_admin', 'tehsil_admin', 'block_admin', 
                  'state_reporter', 'district_reporter', 'tehsil_reporter', 'block_reporter', 'national_reporter', 
                  'reporter', 'subscriber'];
        $states = State::where('is_active', true)->get();
        $districts = District::where('is_active', true)->get();
        $tehsils = Tehsil::where('is_active', true)->get();
        $blocks = Block::where('is_active', true)->get();
        $categories = Category::where('is_active', true)->get();

        // Get assigned categories
        $assignedCategories = [];
        if ($user->assigned_categories) {
            $assignedCategories = is_array($user->assigned_categories) 
                ? $user->assigned_categories 
                : json_decode($user->assigned_categories, true);
        }

        return view('admin.users.edit', compact(
            'user', 'roles', 'states', 'districts', 'tehsils', 'blocks', 'categories', 'assignedCategories'
        ));
    }

    /**
     * Update the specified user.
     */
    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'phone' => 'nullable|string|max:20',
            'role' => 'required|in:super_admin,admin,state_admin,district_admin,tehsil_admin,block_admin,state_reporter,district_reporter,tehsil_reporter,block_reporter,national_reporter,reporter,subscriber',
            'assigned_state_id' => 'nullable|exists:states,id',
            'assigned_district_id' => 'nullable|exists:districts,id',
            'assigned_tehsil_id' => 'nullable|exists:tehsils,id',
            'assigned_block_id' => 'nullable|exists:blocks,id',
            'assigned_category_id' => 'nullable|exists:categories,id',
            'assigned_categories' => 'nullable|array',
            'can_approve' => 'nullable|boolean',
            'approval_level' => 'nullable|in:none,block,tehsil,district,state,national',
            'is_active' => 'nullable|boolean',
            'is_approved' => 'nullable|boolean',
            'is_verified' => 'nullable|boolean',
            'password' => 'nullable|min:8|confirmed',
        ]);

        $user->name = $request->name;
        $user->email = $request->email;
        $user->phone = $request->phone;
        $user->role = $request->role;
        $user->assigned_state_id = $request->assigned_state_id;
        $user->assigned_district_id = $request->assigned_district_id;
        $user->assigned_tehsil_id = $request->assigned_tehsil_id;
        $user->assigned_block_id = $request->assigned_block_id;
        $user->assigned_category_id = $request->assigned_category_id;
        $user->assigned_categories = $request->assigned_categories ? json_encode($request->assigned_categories) : null;
        $user->can_approve = $request->has('can_approve');
        $user->approval_level = $request->approval_level ?? 'none';
        $user->is_active = $request->has('is_active');
        $user->is_approved = $request->has('is_approved');
        $user->is_verified = $request->has('is_verified');

        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }

        $user->save();

        // Update reporter assignment
        if (in_array($request->role, ['reporter', 'state_reporter', 'district_reporter', 'tehsil_reporter', 'block_reporter', 'national_reporter'])) {
            ReporterAssignment::updateOrCreate(
                ['reporter_id' => $user->id],
                [
                    'assigned_by' => auth()->id(),
                    'assigned_state_id' => $request->assigned_state_id,
                    'assigned_district_id' => $request->assigned_district_id,
                    'assigned_tehsil_id' => $request->assigned_tehsil_id,
                    'assigned_block_id' => $request->assigned_block_id,
                    'assigned_category_id' => $request->assigned_category_id,
                    'assigned_categories' => $request->assigned_categories ? json_encode($request->assigned_categories) : null,
                    'is_active' => true,
                    'assigned_at' => now(),
                ]
            );
        } else {
            ReporterAssignment::where('reporter_id', $user->id)->delete();
        }

        return redirect()->route('admin.users.index')
            ->with('success', 'User updated successfully!');
    }

    /**
     * Toggle user active status.
     */
    public function toggleActive(User $user)
    {
        $user->is_active = !$user->is_active;
        $user->save();

        $status = $user->is_active ? 'activated' : 'deactivated';
        return back()->with('success', "User {$status} successfully!");
    }

    /**
     * Approve user.
     */
    public function approve(User $user)
    {
        $user->is_approved = true;
        $user->is_active = true;
        $user->save();

        return back()->with('success', 'User approved successfully!');
    }

    /**
     * Reject user.
     */
    public function reject(User $user)
    {
        $user->is_approved = false;
        $user->is_active = false;
        $user->save();

        return back()->with('success', 'User rejected successfully!');
    }

    /**
     * Verify reporter.
     */
    public function verifyReporter(User $user)
    {
        $user->is_verified = !$user->is_verified;
        $user->save();

        $status = $user->is_verified ? 'verified' : 'unverified';
        return back()->with('success', "Reporter {$status} successfully!");
    }

    /**
     * Add points to reporter.
     */
    public function addPoints(Request $request, User $user)
    {
        $request->validate([
            'points' => 'required|integer|min:1|max:1000',
            'reason' => 'required|string|max:255',
        ]);

        $user->increment('points', $request->points);

        ReporterPoint::create([
            'user_id' => $user->id,
            'points' => $request->points,
            'reason' => $request->reason,
            'action' => 'admin_added',
        ]);

        return back()->with('success', "{$request->points} points added successfully!");
    }

    /**
     * Deduct points from reporter.
     */
    public function deductPoints(Request $request, User $user)
    {
        $request->validate([
            'points' => 'required|integer|min:1|max:' . ($user->points ?? 0),
            'reason' => 'required|string|max:255',
        ]);

        $user->decrement('points', $request->points);

        ReporterPoint::create([
            'user_id' => $user->id,
            'points' => -$request->points,
            'reason' => $request->reason,
            'action' => 'admin_deducted',
        ]);

        return back()->with('success', "{$request->points} points deducted successfully!");
    }

    /**
     * Remove the specified user.
     */
    public function destroy(User $user)
    {
        if ($user->role === 'super_admin' || $user->role === 'admin') {
            return back()->with('error', 'Cannot delete admin user!');
        }

        if ($user->id === auth()->id()) {
            return back()->with('error', 'You cannot delete your own account!');
        }

        ReporterAssignment::where('reporter_id', $user->id)->delete();

        $user->delete();

        return back()->with('success', 'User deleted successfully!');
    }
}