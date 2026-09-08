<?php
// app/Models/ReporterAssignment.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReporterAssignment extends Model
{
    protected $table = 'reporter_assignments';

    protected $fillable = [
        'reporter_id',
        'assigned_by',
        'assigned_state_id',
        'assigned_district_id',
        'assigned_tehsil_id',
        'assigned_block_id',
        'assigned_category_id',
        'assigned_categories',
        'is_active',
        'assigned_at'
    ];

    protected $casts = [
        'assigned_categories' => 'array',
        'is_active' => 'boolean',
        'assigned_at' => 'datetime'
    ];

    public $timestamps = false;

    // ===== RELATIONSHIPS =====

    public function reporter()
    {
        return $this->belongsTo(User::class, 'reporter_id');
    }

    public function assignedBy()
    {
        return $this->belongsTo(User::class, 'assigned_by');
    }

    public function assignedState()
    {
        return $this->belongsTo(State::class, 'assigned_state_id');
    }

    public function assignedDistrict()
    {
        return $this->belongsTo(District::class, 'assigned_district_id');
    }

    public function assignedTehsil()
    {
        return $this->belongsTo(Tehsil::class, 'assigned_tehsil_id');
    }

    public function assignedBlock()
    {
        return $this->belongsTo(Block::class, 'assigned_block_id');
    }

    // ✅ FIXED: NewsCategory → Category
    public function assignedCategory()
    {
        return $this->belongsTo(Category::class, 'assigned_category_id');
    }

    // ===== HELPERS =====

    public function getAssignedCategoriesArray()
    {
        return $this->assigned_categories ?? [];
    }

    public function hasCategory($categoryId)
    {
        $categories = $this->getAssignedCategoriesArray();
        return in_array($categoryId, $categories);
    }

    public function getLocationLabel()
    {
        if ($this->assigned_block_id) {
            return $this->assignedBlock->name ?? 'N/A';
        }
        if ($this->assigned_tehsil_id) {
            return $this->assignedTehsil->name ?? 'N/A';
        }
        if ($this->assigned_district_id) {
            return $this->assignedDistrict->name ?? 'N/A';
        }
        if ($this->assigned_state_id) {
            return $this->assignedState->name ?? 'N/A';
        }
        return 'No Location';
    }

    public function getLocationLevel()
    {
        if ($this->assigned_block_id) return 'block';
        if ($this->assigned_tehsil_id) return 'tehsil';
        if ($this->assigned_district_id) return 'district';
        if ($this->assigned_state_id) return 'state';
        return 'none';
    }
}