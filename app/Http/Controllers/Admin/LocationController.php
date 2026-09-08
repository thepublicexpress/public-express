<?php
// app/Http/Controllers/Admin/LocationController.php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\State;
use App\Models\District;
use App\Models\Tehsil;
use App\Models\Block;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class LocationController extends Controller
{
    // ===================== STATES =====================
    
    public function states(Request $request)
    {
        $query = State::query();
        
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('name_hi', 'like', "%{$search}%");
            });
        }
        
        $states = $query->withCount('districts')
            ->orderBy('name_hi', 'asc')
            ->paginate(20);
        
        return view('admin.locations.states', compact('states'));
    }

    public function storeState(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:states,name',
            'name_hi' => 'nullable|string|max:255',
            'is_active' => 'nullable|boolean',
        ]);

        // ✅ Auto‑fill Hindi name if not provided
        $nameHi = $request->name_hi ?? $request->name;

        State::create([
            'name' => $request->name,
            'name_hi' => $nameHi,
            'slug' => Str::slug($request->name),
            'is_active' => $request->is_active ?? true,
        ]);

        return redirect()->route('admin.locations.states')
            ->with('success', 'State added successfully!');
    }

    public function updateState(Request $request, State $state)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:states,name,' . $state->id,
            'name_hi' => 'nullable|string|max:255',
            'is_active' => 'nullable|boolean',
        ]);

        // Keep existing name_hi if not provided, else use new, else fallback to name
        $nameHi = $request->name_hi ?? $state->name_hi ?? $request->name;

        $state->update([
            'name' => $request->name,
            'name_hi' => $nameHi,
            'slug' => Str::slug($request->name),
            'is_active' => $request->is_active ?? $state->is_active,
        ]);

        return redirect()->route('admin.locations.states')
            ->with('success', 'State updated successfully!');
    }

    public function toggleState(State $state)
    {
        $state->is_active = !$state->is_active;
        $state->save();
        
        return back()->with('success', 'State status updated!');
    }

    public function destroyState(State $state)
    {
        if ($state->districts()->count() > 0) {
            return back()->with('error', 'Cannot delete state with districts. Remove districts first.');
        }
        
        $state->delete();
        return back()->with('success', 'State deleted successfully!');
    }

    // ===================== DISTRICTS =====================
    
    public function districts(Request $request)
    {
        $query = District::with('state');
        
        if ($request->filled('state_id')) {
            $query->where('state_id', $request->state_id);
        }
        
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('name_hi', 'like', "%{$search}%");
            });
        }
        
        $districts = $query->withCount('tehsils')
            ->orderBy('name_hi', 'asc')
            ->paginate(20);
        
        $states = State::where('is_active', true)->orderBy('name')->get();
        
        return view('admin.locations.districts', compact('districts', 'states'));
    }

    public function storeDistrict(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:districts,name',
            'name_hi' => 'nullable|string|max:255',
            'state_id' => 'required|exists:states,id',
            'is_active' => 'nullable|boolean',
        ]);

        $nameHi = $request->name_hi ?? $request->name;

        District::create([
            'name' => $request->name,
            'name_hi' => $nameHi,
            'state_id' => $request->state_id,
            'slug' => Str::slug($request->name),
            'is_active' => $request->is_active ?? true,
        ]);

        return redirect()->route('admin.locations.districts')
            ->with('success', 'District added successfully!');
    }

    public function updateDistrict(Request $request, District $district)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:districts,name,' . $district->id,
            'name_hi' => 'nullable|string|max:255',
            'state_id' => 'required|exists:states,id',
            'is_active' => 'nullable|boolean',
        ]);

        $nameHi = $request->name_hi ?? $district->name_hi ?? $request->name;

        $district->update([
            'name' => $request->name,
            'name_hi' => $nameHi,
            'state_id' => $request->state_id,
            'slug' => Str::slug($request->name),
            'is_active' => $request->is_active ?? $district->is_active,
        ]);

        return redirect()->route('admin.locations.districts')
            ->with('success', 'District updated successfully!');
    }

    public function toggleDistrict(District $district)
    {
        $district->is_active = !$district->is_active;
        $district->save();
        
        return back()->with('success', 'District status updated!');
    }

    public function destroyDistrict(District $district)
    {
        if ($district->tehsils()->count() > 0) {
            return back()->with('error', 'Cannot delete district with tehsils. Remove tehsils first.');
        }
        
        $district->delete();
        return back()->with('success', 'District deleted successfully!');
    }

    // ===================== TEHSILS =====================
    
    public function tehsils(Request $request)
    {
        $query = Tehsil::with(['district', 'district.state']);
        
        if ($request->filled('district_id')) {
            $query->where('district_id', $request->district_id);
        }
        
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('name_hi', 'like', "%{$search}%");
            });
        }
        
        $tehsils = $query->withCount('blocks')
            ->orderBy('name_hi', 'asc')
            ->paginate(20);
        
        $districts = District::where('is_active', true)->orderBy('name')->get();
        
        return view('admin.locations.tehsils', compact('tehsils', 'districts'));
    }

    public function storeTehsil(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:tehsils,name',
            'name_hi' => 'nullable|string|max:255',
            'district_id' => 'required|exists:districts,id',
            'is_active' => 'nullable|boolean',
        ]);

        $nameHi = $request->name_hi ?? $request->name;

        Tehsil::create([
            'name' => $request->name,
            'name_hi' => $nameHi,
            'district_id' => $request->district_id,
            'slug' => Str::slug($request->name),
            'is_active' => $request->is_active ?? true,
        ]);

        return redirect()->route('admin.locations.tehsils')
            ->with('success', 'Tehsil added successfully!');
    }

    public function updateTehsil(Request $request, Tehsil $tehsil)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:tehsils,name,' . $tehsil->id,
            'name_hi' => 'nullable|string|max:255',
            'district_id' => 'required|exists:districts,id',
            'is_active' => 'nullable|boolean',
        ]);

        $nameHi = $request->name_hi ?? $tehsil->name_hi ?? $request->name;

        $tehsil->update([
            'name' => $request->name,
            'name_hi' => $nameHi,
            'district_id' => $request->district_id,
            'slug' => Str::slug($request->name),
            'is_active' => $request->is_active ?? $tehsil->is_active,
        ]);

        return redirect()->route('admin.locations.tehsils')
            ->with('success', 'Tehsil updated successfully!');
    }

    public function toggleTehsil(Tehsil $tehsil)
    {
        $tehsil->is_active = !$tehsil->is_active;
        $tehsil->save();
        
        return back()->with('success', 'Tehsil status updated!');
    }

    public function destroyTehsil(Tehsil $tehsil)
    {
        if ($tehsil->blocks()->count() > 0) {
            return back()->with('error', 'Cannot delete tehsil with blocks. Remove blocks first.');
        }
        
        $tehsil->delete();
        return back()->with('success', 'Tehsil deleted successfully!');
    }

    // ===================== BLOCKS =====================
    
    public function blocks(Request $request)
    {
        $query = Block::with(['tehsil', 'tehsil.district', 'tehsil.district.state']);
        
        if ($request->filled('tehsil_id')) {
            $query->where('tehsil_id', $request->tehsil_id);
        }
        
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('name_hi', 'like', "%{$search}%");
            });
        }
        
        $blocks = $query->orderBy('name_hi', 'asc')->paginate(20);
        
        $tehsils = Tehsil::where('is_active', true)->orderBy('name')->get();
        
        return view('admin.locations.blocks', compact('blocks', 'tehsils'));
    }

    public function storeBlock(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:blocks,name',
            'name_hi' => 'nullable|string|max:255',
            'tehsil_id' => 'required|exists:tehsils,id',
            'is_active' => 'nullable|boolean',
        ]);

        $nameHi = $request->name_hi ?? $request->name;

        Block::create([
            'name' => $request->name,
            'name_hi' => $nameHi,
            'tehsil_id' => $request->tehsil_id,
            'slug' => Str::slug($request->name),
            'is_active' => $request->is_active ?? true,
        ]);

        return redirect()->route('admin.locations.blocks')
            ->with('success', 'Block added successfully!');
    }

    public function updateBlock(Request $request, Block $block)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:blocks,name,' . $block->id,
            'name_hi' => 'nullable|string|max:255',
            'tehsil_id' => 'required|exists:tehsils,id',
            'is_active' => 'nullable|boolean',
        ]);

        $nameHi = $request->name_hi ?? $block->name_hi ?? $request->name;

        $block->update([
            'name' => $request->name,
            'name_hi' => $nameHi,
            'tehsil_id' => $request->tehsil_id,
            'slug' => Str::slug($request->name),
            'is_active' => $request->is_active ?? $block->is_active,
        ]);

        return redirect()->route('admin.locations.blocks')
            ->with('success', 'Block updated successfully!');
    }

    public function toggleBlock(Block $block)
    {
        $block->is_active = !$block->is_active;
        $block->save();
        
        return back()->with('success', 'Block status updated!');
    }

    public function destroyBlock(Block $block)
    {
        $block->delete();
        return back()->with('success', 'Block deleted successfully!');
    }
}