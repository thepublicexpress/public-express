<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\State;
use App\Models\District;
use App\Models\Tehsil;
use App\Models\Block;
use App\Models\Category; // ✅ NewsCategory की जगह Category
use App\Models\News;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;

class UserController extends Controller
{
    /**
     * Display a listing of users
     */
    public function index(Request $request)
    {
        $query = User::query();

        if ($request->has('role') && $request->role != '') {
            $query->where('role', $request->role);
        }

        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        $users = $query->orderBy('created_at', 'desc')->paginate(20);
        $roles = $this->getAllRoles();

        return view('admin.users.index', compact('users', 'roles'));
    }

    public function create()
    {
        $roles = $this->getAllRoles();
        $states = State::where('is_active', 1)->orderBy('name')->get();
        $categories = Category::where('is_active', 1)->orderBy('name')->get(); // ✅ Category
        return view('admin.users.create', compact('roles', 'states', 'categories'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'phone' => 'nullable|string|max:50',
            'password' => 'required|string|min:8',
            'role' => 'required|string|max:50',
            'assigned_state_id' => 'nullable|exists:states,id',
            'assigned_district_id' => 'nullable|exists:districts,id',
            'assigned_tehsil_id' => 'nullable|exists:tehsils,id',
            'assigned_block_id' => 'nullable|exists:blocks,id',
            'is_active' => 'nullable|boolean',
            'is_approved' => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

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
            'is_active' => $request->is_active ?? 1,
            'is_approved' => $request->is_approved ?? 1,
            'is_verified' => 1,
            'points' => 0,
            'wallet_balance' => 0,
        ]);

        return redirect()->route('admin.users.index')
            ->with('success', '✅ User created successfully!');
    }

    /**
     * Display the specified user.
     */
    public function show($id)
    {
        $user = User::with(['assignedState', 'assignedDistrict', 'assignedTehsil', 'assignedBlock'])
            ->findOrFail($id);

        $news = News::where('user_id', $user->id)
            ->whereNull('deleted_at')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        $assignments = News::where('user_id', $user->id)
            ->whereNull('deleted_at')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        $totalNews = News::where('user_id', $user->id)->whereNull('deleted_at')->count();
        $publishedNews = News::where('user_id', $user->id)->whereNull('deleted_at')->where('status', 'published')->count();
        $pendingNews = News::where('user_id', $user->id)->whereNull('deleted_at')->where('status', 'pending')->count();
        $rejectedNews = News::where('user_id', $user->id)->whereNull('deleted_at')->where('status', 'rejected')->count();
        $totalViews = News::where('user_id', $user->id)->whereNull('deleted_at')->sum('views');

        $categoryNames = Category::pluck('name', 'id')->toArray(); // ✅ Category

        return view('admin.users.show', compact(
            'user',
            'news',
            'assignments',
            'totalNews',
            'publishedNews',
            'pendingNews',
            'rejectedNews',
            'totalViews',
            'categoryNames'
        ));
    }

    public function edit($id)
    {
        $user = User::findOrFail($id);
        $roles = $this->getAllRoles();
        $states = State::where('is_active', 1)->orderBy('name')->get();
        $categories = Category::where('is_active', 1)->orderBy('name')->get(); // ✅ Category

        $districts = [];
        if ($user->assigned_state_id) {
            $districts = District::where('state_id', $user->assigned_state_id)
                ->where('is_active', 1)
                ->orderBy('name')
                ->get();
        }

        $tehsils = [];
        if ($user->assigned_district_id) {
            $tehsils = Tehsil::where('district_id', $user->assigned_district_id)
                ->where('is_active', 1)
                ->orderBy('name')
                ->get();
        }

        $blocks = [];
        if ($user->assigned_tehsil_id) {
            $blocks = Block::where('tehsil_id', $user->assigned_tehsil_id)
                ->where('is_active', 1)
                ->orderBy('name')
                ->get();
        }

        return view('admin.users.edit', compact(
            'user', 'roles', 'states', 'categories',
            'districts', 'tehsils', 'blocks'
        ));
    }

    /**
     * ✅ UPDATE USER (with Bio & Photo)
     */
    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        // ✅ Validation – bio & photo added
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $id,
            'phone' => 'nullable|string|max:50',
            'bio'   => 'nullable|string',                        // ✅ Bio
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048', // ✅ Photo
            'password' => 'nullable|string|min:8',
            'role' => 'required|string|max:50',
            'assigned_state_id' => 'nullable|exists:states,id',
            'assigned_district_id' => 'nullable|exists:districts,id',
            'assigned_tehsil_id' => 'nullable|exists:tehsils,id',
            'assigned_block_id' => 'nullable|exists:blocks,id',
            'is_active' => 'nullable|boolean',
            'is_approved' => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // ✅ Basic data (including bio)
        $data = [
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'bio'   => $request->bio,                // ✅ Bio save
            'role' => $request->role,
            'assigned_state_id' => $request->assigned_state_id,
            'assigned_district_id' => $request->assigned_district_id,
            'assigned_tehsil_id' => $request->assigned_tehsil_id,
            'assigned_block_id' => $request->assigned_block_id,
            'is_active' => $request->is_active ?? 0,
            'is_approved' => $request->is_approved ?? 0,
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        // ✅ Photo upload (if provided)
        if ($request->hasFile('photo')) {
            // Delete old photo (if exists)
            if ($user->photo && file_exists(public_path($user->photo))) {
                unlink(public_path($user->photo));
            }

            // Generate new filename & move to public/uploads/reporters/
            $fileName = time() . '_' . $request->file('photo')->getClientOriginalName();
            $request->file('photo')->move(public_path('uploads/reporters'), $fileName);
            $data['photo'] = 'uploads/reporters/' . $fileName;
        }

        // ✅ Update user with all data
        $user->update($data);

        return redirect()->route('admin.users.index')
            ->with('success', '✅ User updated successfully with Bio and Photo!');
    }

    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $user->delete();

        return redirect()->route('admin.users.index')
            ->with('success', '✅ User deleted successfully!');
    }

    public function toggleActive($id)
    {
        $user = User::findOrFail($id);
        $user->is_active = !$user->is_active;
        $user->save();

        return redirect()->back()
            ->with('success', '✅ User status updated!');
    }

    public function approve($id)
    {
        $user = User::findOrFail($id);
        $user->is_approved = 1;
        $user->save();

        return redirect()->back()
            ->with('success', '✅ User approved successfully!');
    }

    public function reject($id)
    {
        $user = User::findOrFail($id);
        $user->is_approved = 0;
        $user->save();

        return redirect()->back()
            ->with('success', '✅ User rejected!');
    }

    public function verifyReporter($id)
    {
        $user = User::findOrFail($id);
        $user->is_verified = 1;
        $user->save();

        return redirect()->back()
            ->with('success', '✅ Reporter verified successfully!');
    }

    public function addPoints(Request $request, $id)
    {
        $request->validate(['points' => 'required|integer|min:1']);
        $user = User::findOrFail($id);
        $user->points += $request->points;
        $user->save();

        return redirect()->back()
            ->with('success', "✅ {$request->points} points added successfully!");
    }

    public function deductPoints(Request $request, $id)
    {
        $request->validate(['points' => 'required|integer|min:1']);
        $user = User::findOrFail($id);
        $user->points = max(0, $user->points - $request->points);
        $user->save();

        return redirect()->back()
            ->with('success', "✅ {$request->points} points deducted!");
    }

    private function getAllRoles()
    {
        return [
            'super_admin' => 'Super Admin',
            'admin' => 'Admin',
            'state_admin' => 'State Admin',
            'district_admin' => 'District Admin',
            'tehsil_admin' => 'Tehsil Admin',
            'block_admin' => 'Block Admin',
            'national_reporter' => 'National Reporter',
            'state_reporter' => 'State Reporter',
            'district_reporter' => 'District Reporter',
            'tehsil_reporter' => 'Tehsil Reporter',
            'block_reporter' => 'Block Reporter',
            'subscriber' => 'Subscriber',
        ];
    }

    public function getDistricts($stateId)
    {
        $districts = District::where('state_id', $stateId)
            ->where('is_active', 1)
            ->orderBy('name')
            ->get(['id', 'name', 'name_hi']);
        return response()->json($districts);
    }

    public function getTehsils($districtId)
    {
        $tehsils = Tehsil::where('district_id', $districtId)
            ->where('is_active', 1)
            ->orderBy('name')
            ->get(['id', 'name', 'name_hi']);
        return response()->json($tehsils);
    }

    public function getBlocks($tehsilId)
    {
        $blocks = Block::where('tehsil_id', $tehsilId)
            ->where('is_active', 1)
            ->orderBy('name')
            ->get(['id', 'name', 'name_hi']);
        return response()->json($blocks);
    }
}