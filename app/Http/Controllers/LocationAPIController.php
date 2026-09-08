<?php
// app/Http/Controllers/LocationAPIController.php

namespace App\Http\Controllers;

use App\Models\State;
use App\Models\District;
use App\Models\Tehsil;
use App\Models\Block;
use Illuminate\Http\Request;

class LocationAPIController extends Controller
{
    /**
     * Get Districts by State ID (only active)
     */
    public function getDistricts($state_id)
    {
        $districts = District::where('state_id', $state_id)
            ->where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'name', 'name_hi', 'slug', 'display_name']);

        $districts->transform(function ($district) {
            $district->display_name = $district->name_hi ?? $district->display_name ?? $district->name;
            return $district;
        });

        return response()->json($districts);
    }

    /**
     * Get Tehsils by District ID (only active)
     */
    public function getTehsils($district_id)
    {
        $tehsils = Tehsil::where('district_id', $district_id)
            ->where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'name', 'name_hi', 'slug', 'display_name']);

        $tehsils->transform(function ($tehsil) {
            $tehsil->display_name = $tehsil->name_hi ?? $tehsil->display_name ?? $tehsil->name;
            return $tehsil;
        });

        return response()->json($tehsils);
    }

    /**
     * Get Blocks by Tehsil ID (only active)
     */
    public function getBlocks($tehsil_id)
    {
        $blocks = Block::where('tehsil_id', $tehsil_id)
            ->where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'name', 'name_hi', 'slug', 'display_name']);

        $blocks->transform(function ($block) {
            $block->display_name = $block->name_hi ?? $block->display_name ?? $block->name;
            return $block;
        });

        return response()->json($blocks);
    }

    /**
     * Get States for dropdown (only active)
     */
    public function getStates()
    {
        $states = State::where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'name', 'name_hi', 'slug', 'display_name']);

        $states->transform(function ($state) {
            $state->display_name = $state->name_hi ?? $state->display_name ?? $state->name;
            return $state;
        });

        return response()->json($states);
    }

    /**
     * Get Reporters by Location
     */
    public function getReportersByLocation(Request $request)
    {
        $query = \App\Models\User::where('role', 'reporter')
            ->where('is_active', true)
            ->where('is_approved', true);

        if ($request->filled('state_id')) {
            $query->where('assigned_state_id', $request->state_id);
        }

        if ($request->filled('district_id')) {
            $query->where('assigned_district_id', $request->district_id);
        }

        if ($request->filled('tehsil_id')) {
            $query->where('assigned_tehsil_id', $request->tehsil_id);
        }

        if ($request->filled('block_id')) {
            $query->where('assigned_block_id', $request->block_id);
        }

        $reporters = $query->get(['id', 'name', 'email']);

        return response()->json($reporters);
    }
}