<?php
// app/Models/User.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'password',
        'role',
        'assigned_state_id',
        'assigned_district_id',
        'assigned_tehsil_id',
        'assigned_block_id',
        'assigned_category_id',
        'assigned_categories',
        'can_approve',
        'approval_level',
        'is_active',
        'is_approved',
        'is_verified',
        'points',
        'wallet_balance',
        'location_id',
        'upi_id',
        // ===== HYPERLOCAL FIELDS =====
        'latitude',
        'longitude',
        'location_updated_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'is_active' => 'boolean',
        'is_approved' => 'boolean',
        'is_verified' => 'boolean',
        'can_approve' => 'boolean',
        'assigned_categories' => 'array',
        'points' => 'integer',
        'wallet_balance' => 'decimal:2',
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
        'location_updated_at' => 'datetime',
    ];

    // ===== RELATIONSHIPS =====
    
    public function state()
    {
        return $this->belongsTo(State::class, 'assigned_state_id');
    }

    public function district()
    {
        return $this->belongsTo(District::class, 'assigned_district_id');
    }

    public function tehsil()
    {
        return $this->belongsTo(Tehsil::class, 'assigned_tehsil_id');
    }

    public function block()
    {
        return $this->belongsTo(Block::class, 'assigned_block_id');
    }

    public function category()
    {
        return $this->belongsTo(Category::class, 'assigned_category_id');
    }

    public function location()
    {
        return $this->belongsTo(Tehsil::class, 'location_id');
    }

    public function news()
    {
        return $this->hasMany(News::class);
    }

    public function reporterAssignments()
    {
        return $this->hasMany(ReporterAssignment::class, 'reporter_id');
    }

    public function assignedReporters()
    {
        return $this->hasMany(ReporterAssignment::class, 'assigned_by');
    }

    public function notifications()
    {
        return $this->hasMany(Notification::class);
    }

    public function points()
    {
        return $this->hasMany(ReporterPoint::class);
    }

    public function monetisation()
    {
        return $this->hasOne(ReporterMonetisation::class);
    }

    public function withdrawals()
    {
        return $this->hasMany(Withdrawal::class);
    }

    // ===== HYPERLOCAL RELATIONSHIPS =====
    public function locations()
    {
        return $this->hasMany(UserLocation::class);
    }

    public function hyperlocalNotifications()
    {
        return $this->hasMany(HyperlocalNotification::class);
    }

    // ===== ROLE CHECK METHODS =====
    
    public function isSuperAdmin()
    {
        return $this->role === 'super_admin';
    }

    public function isAdmin()
    {
        return in_array($this->role, ['admin', 'super_admin', 'state_admin', 'district_admin', 'tehsil_admin', 'block_admin']);
    }

    public function isStateReporter()
    {
        return $this->role === 'state_reporter';
    }

    public function isDistrictReporter()
    {
        return $this->role === 'district_reporter';
    }

    public function isTehsilReporter()
    {
        return $this->role === 'tehsil_reporter';
    }

    public function isBlockReporter()
    {
        return $this->role === 'block_reporter';
    }

    public function isNationalReporter()
    {
        return $this->role === 'national_reporter';
    }

    public function isReporter()
    {
        return in_array($this->role, ['reporter', 'state_reporter', 'district_reporter', 'tehsil_reporter', 'block_reporter', 'national_reporter']);
    }

    // ===== CATEGORY PERMISSION METHODS =====
    
    /**
     * Check if reporter can write in a specific category
     */
    public function canWriteCategory($categoryId)
    {
        // Super Admin and Admin can write any category
        if ($this->isSuperAdmin() || $this->isAdmin()) {
            return true;
        }

        // If no category restrictions, allow all
        if (!$this->assigned_category_id && !$this->assigned_categories) {
            return true;
        }

        // Check single category
        if ($this->assigned_category_id && $this->assigned_category_id == $categoryId) {
            return true;
        }

        // Check multiple categories
        if ($this->assigned_categories) {
            $categories = is_array($this->assigned_categories) 
                ? $this->assigned_categories 
                : json_decode($this->assigned_categories, true);
            
            if (is_array($categories) && in_array($categoryId, $categories)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Get all allowed categories for reporter
     */
    public function getAllowedCategories()
    {
        // Super Admin and Admin - all categories
        if ($this->isSuperAdmin() || $this->isAdmin()) {
            return Category::where('is_active', true)->get();
        }

        // If no restrictions, return all active categories
        if (!$this->assigned_category_id && !$this->assigned_categories) {
            return Category::where('is_active', true)->get();
        }

        // If single category assigned
        if ($this->assigned_category_id) {
            return Category::where('id', $this->assigned_category_id)
                ->where('is_active', true)
                ->get();
        }

        // If multiple categories assigned
        if ($this->assigned_categories) {
            $categoryIds = is_array($this->assigned_categories) 
                ? $this->assigned_categories 
                : json_decode($this->assigned_categories, true);
            
            return Category::whereIn('id', $categoryIds)
                ->where('is_active', true)
                ->get();
        }

        return collect();
    }

    // ===== LOCATION PERMISSION METHODS =====
    
    /**
     * Check if reporter can write for a specific location
     */
    public function canWriteLocation($stateId = null, $districtId = null, $tehsilId = null, $blockId = null)
    {
        // Super Admin and Admin can write any location
        if ($this->isSuperAdmin() || $this->isAdmin()) {
            return true;
        }

        // National Reporter can write any location
        if ($this->isNationalReporter()) {
            return true;
        }

        // Check State level
        if ($this->assigned_state_id && $this->assigned_state_id != $stateId) {
            return false;
        }

        // Check District level
        if ($this->assigned_district_id && $this->assigned_district_id != $districtId) {
            return false;
        }

        // Check Tehsil level
        if ($this->assigned_tehsil_id && $this->assigned_tehsil_id != $tehsilId) {
            return false;
        }

        // Check Block level
        if ($this->assigned_block_id && $this->assigned_block_id != $blockId) {
            return false;
        }

        return true;
    }

    /**
     * Get allowed locations for reporter
     */
    public function getAllowedLocations()
    {
        // Super Admin and Admin - all locations
        if ($this->isSuperAdmin() || $this->isAdmin()) {
            return [
                'type' => 'all',
                'states' => State::where('is_active', true)->get(),
                'message' => 'You can write from any location'
            ];
        }

        // National Reporter - All locations
        if ($this->isNationalReporter()) {
            return [
                'type' => 'national',
                'states' => State::where('is_active', true)->get(),
                'message' => 'You are a National Reporter. Location is optional.'
            ];
        }

        // State Reporter
        if ($this->assigned_state_id) {
            return [
                'type' => 'state',
                'state_id' => $this->assigned_state_id,
                'state' => $this->state,
                'districts' => District::where('state_id', $this->assigned_state_id)
                    ->where('is_active', true)
                    ->get(),
                'message' => 'You can write only in ' . ($this->state->name ?? 'your assigned state')
            ];
        }

        // District Reporter
        if ($this->assigned_district_id) {
            return [
                'type' => 'district',
                'district_id' => $this->assigned_district_id,
                'district' => $this->district,
                'tehsils' => Tehsil::where('district_id', $this->assigned_district_id)
                    ->where('is_active', true)
                    ->get(),
                'message' => 'You can write only in ' . ($this->district->name ?? 'your assigned district')
            ];
        }

        // Tehsil Reporter
        if ($this->assigned_tehsil_id) {
            return [
                'type' => 'tehsil',
                'tehsil_id' => $this->assigned_tehsil_id,
                'tehsil' => $this->tehsil,
                'blocks' => Block::where('tehsil_id', $this->assigned_tehsil_id)
                    ->where('is_active', true)
                    ->get(),
                'message' => 'You can write only in ' . ($this->tehsil->name ?? 'your assigned tehsil')
            ];
        }

        // Block Reporter
        if ($this->assigned_block_id) {
            return [
                'type' => 'block',
                'block_id' => $this->assigned_block_id,
                'block' => $this->block,
                'message' => 'You can write only in ' . ($this->block->name ?? 'your assigned block')
            ];
        }

        return [
            'type' => 'none',
            'message' => 'No location assigned. Please contact admin.'
        ];
    }

    // ===== LOCATION ACCESS METHODS =====
    
    public function getAssignedLocation()
    {
        if ($this->assigned_block_id) {
            return $this->block;
        }
        if ($this->assigned_tehsil_id) {
            return $this->tehsil;
        }
        if ($this->assigned_district_id) {
            return $this->district;
        }
        if ($this->assigned_state_id) {
            return $this->state;
        }
        return null;
    }

    public function getLocationLevel()
    {
        if ($this->assigned_block_id) return 'block';
        if ($this->assigned_tehsil_id) return 'tehsil';
        if ($this->assigned_district_id) return 'district';
        if ($this->assigned_state_id) return 'state';
        return 'none';
    }

    public function canAccessLocation($stateId = null, $districtId = null, $tehsilId = null, $blockId = null)
    {
        if ($this->isSuperAdmin() || $this->isAdmin()) {
            return true;
        }

        if ($this->assigned_state_id && $this->assigned_state_id != $stateId) {
            return false;
        }

        if ($this->assigned_district_id && $this->assigned_district_id != $districtId) {
            return false;
        }

        if ($this->assigned_tehsil_id && $this->assigned_tehsil_id != $tehsilId) {
            return false;
        }

        if ($this->assigned_block_id && $this->assigned_block_id != $blockId) {
            return false;
        }

        return true;
    }

    public function canApproveNews($newsStateId = null, $newsDistrictId = null, $newsTehsilId = null, $newsBlockId = null)
    {
        if ($this->isSuperAdmin() || $this->isAdmin()) {
            return true;
        }

        if (!$this->can_approve) {
            return false;
        }

        if ($this->approval_level === 'national') {
            return true;
        }

        if ($this->approval_level === 'state' && $this->assigned_state_id == $newsStateId) {
            return true;
        }

        if ($this->approval_level === 'district' && $this->assigned_district_id == $newsDistrictId) {
            return true;
        }

        if ($this->approval_level === 'tehsil' && $this->assigned_tehsil_id == $newsTehsilId) {
            return true;
        }

        if ($this->approval_level === 'block' && $this->assigned_block_id == $newsBlockId) {
            return true;
        }

        return false;
    }

    // ===== HYPERLOCAL METHODS =====

    /**
     * Get user's current location
     */
    public function getLocation()
    {
        if ($this->latitude && $this->longitude) {
            return [
                'latitude' => $this->latitude,
                'longitude' => $this->longitude,
            ];
        }
        
        // Get from user_locations
        $location = $this->locations()->where('is_current', true)->first();
        if ($location) {
            return [
                'latitude' => $location->latitude,
                'longitude' => $location->longitude,
            ];
        }
        
        return null;
    }

    /**
     * Update user's location
     */
    public function updateLocation($latitude, $longitude, $address = null)
    {
        $this->latitude = $latitude;
        $this->longitude = $longitude;
        $this->location_updated_at = now();
        $this->save();
        
        // Save to history
        UserLocation::create([
            'user_id' => $this->id,
            'latitude' => $latitude,
            'longitude' => $longitude,
            'address' => $address,
            'is_current' => true,
            'location_at' => now(),
        ]);
        
        // Mark old locations as not current
        UserLocation::where('user_id', $this->id)
            ->where('id', '!=', $this->id)
            ->update(['is_current' => false]);
    }

    /**
     * Get nearby news based on user location
     */
    public function getNearbyNews($radius = 5, $limit = 20)
    {
        $location = $this->getLocation();
        if (!$location) {
            return News::where('status', 'published')
                ->latest('published_at')
                ->limit($limit)
                ->get();
        }
        
        $lat = $location['latitude'];
        $lng = $location['longitude'];
        
        return News::where('status', 'published')
            ->whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->selectRaw(
                "*, ( 6371 * acos( cos( radians(?) ) * cos( radians( latitude ) ) * cos( radians( longitude ) - radians(?) ) + sin( radians(?) ) * sin( radians( latitude ) ) ) ) AS distance",
                [$lat, $lng, $lat]
            )
            ->having('distance', '<=', $radius)
            ->orderBy('distance')
            ->limit($limit)
            ->get();
    }

    /**
     * Get unread hyperlocal notifications count
     */
    public function getUnreadHyperlocalCount()
    {
        return $this->hyperlocalNotifications()
            ->where('is_read', false)
            ->count();
    }

    /**
     * Mark all hyperlocal notifications as read
     */
    public function markHyperlocalRead()
    {
        return $this->hyperlocalNotifications()
            ->where('is_read', false)
            ->update(['is_read' => true]);
    }

    // ===== SCOPES =====
    
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeApproved($query)
    {
        return $query->where('is_approved', true);
    }

    public function scopeReporters($query)
    {
        return $query->whereIn('role', ['reporter', 'state_reporter', 'district_reporter', 'tehsil_reporter', 'block_reporter', 'national_reporter']);
    }

    public function scopeWithLocation($query)
    {
        return $query->whereNotNull('latitude')->whereNotNull('longitude');
    }

    // ===== ACCESSORS =====
    
    public function getRoleLabelAttribute()
    {
        $labels = [
            'super_admin' => 'Super Admin',
            'admin' => 'Admin',
            'state_admin' => 'State Admin',
            'district_admin' => 'District Admin',
            'tehsil_admin' => 'Tehsil Admin',
            'block_admin' => 'Block Admin',
            'state_reporter' => 'State Reporter',
            'district_reporter' => 'District Reporter',
            'tehsil_reporter' => 'Tehsil Reporter',
            'block_reporter' => 'Block Reporter',
            'national_reporter' => 'National Reporter',
            'reporter' => 'Reporter',
            'subscriber' => 'Subscriber',
        ];
        return $labels[$this->role] ?? $this->role;
    }

    public function getApprovalLevelLabelAttribute()
    {
        $labels = [
            'none' => 'None',
            'block' => 'Block Level',
            'tehsil' => 'Tehsil Level',
            'district' => 'District Level',
            'state' => 'State Level',
            'national' => 'National Level',
        ];
        return $labels[$this->approval_level] ?? $this->approval_level;
    }

    public function getLocationLabelAttribute()
    {
        if ($this->assigned_block_id && $this->block) {
            return 'Block: ' . $this->block->name;
        }
        if ($this->assigned_tehsil_id && $this->tehsil) {
            return 'Tehsil: ' . $this->tehsil->name;
        }
        if ($this->assigned_district_id && $this->district) {
            return 'District: ' . $this->district->name;
        }
        if ($this->assigned_state_id && $this->state) {
            return 'State: ' . $this->state->name;
        }
        return 'No Location';
    }

    public function getTotalPointsAttribute()
    {
        return $this->points()->sum('points');
    }

    public function getLocationTextAttribute()
    {
        $location = $this->getLocation();
        if (!$location) {
            return 'Location not set';
        }
        return $location['latitude'] . ', ' . $location['longitude'];
    }
}