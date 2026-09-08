<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'phone',
        'bio',
        'photo',
        'facebook',
        'x',
        'instagram',
        'youtube',
        'linkedin',
        'website',
        'password',
        'role',
        'assigned_state_id',
        'assigned_district_id',
        'assigned_tehsil_id',
        'assigned_block_id',
        'is_active',
        'is_approved',
        'is_verified',
        'points',
        'wallet_balance',
        'fcm_token',               // ✅ Firebase Cloud Messaging Token
        'fcm_token_updated_at',    // ✅ Last update timestamp
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'is_active' => 'boolean',
        'is_approved' => 'boolean',
        'is_verified' => 'boolean',
        'points' => 'integer',
        'wallet_balance' => 'integer',
        'fcm_token_updated_at' => 'datetime',
    ];

    // ============================================================
    // ✅ PHOTO ACCESSOR
    // ============================================================
    public function getPhotoUrlAttribute()
    {
        if ($this->photo && file_exists(public_path($this->photo))) {
            return asset($this->photo);
        }
        return asset('images/default-avatar.png');
    }

    // ============================================================
    // ✅ SOCIAL MEDIA URL HELPERS
    // ============================================================
    public function getFacebookUrlAttribute() { return $this->facebook ?: null; }
    public function getXUrlAttribute() { return $this->x ?: null; }
    public function getInstagramUrlAttribute() { return $this->instagram ?: null; }
    public function getYoutubeUrlAttribute() { return $this->youtube ?: null; }
    public function getLinkedinUrlAttribute() { return $this->linkedin ?: null; }
    public function getWebsiteUrlAttribute() { return $this->website ?: null; }

    // ============================================================
    // ✅ FULL NAME ACCESSOR
    // ============================================================
    public function getFullNameAttribute() { return $this->name; }

    // ============================================================
    // ✅ RELATIONSHIPS
    // ============================================================
    public function news() { return $this->hasMany(News::class); }
    public function assignedState() { return $this->belongsTo(State::class, 'assigned_state_id'); }
    public function assignedDistrict() { return $this->belongsTo(District::class, 'assigned_district_id'); }
    public function assignedTehsil() { return $this->belongsTo(Tehsil::class, 'assigned_tehsil_id'); }
    public function assignedBlock() { return $this->belongsTo(Block::class, 'assigned_block_id'); }
    public function monetisation() { return $this->hasOne(ReporterMonetisation::class); }

    // ============================================================
    // ✅ ROLE CHECK METHODS
    // ============================================================
    public function isAdmin() { return in_array($this->role, ['super_admin', 'admin']); }
    public function isSuperAdmin() { return $this->role == 'super_admin'; }
    public function isReporter() { return str_contains($this->role, 'reporter'); }
    public function isSubscriber() { return $this->role == 'subscriber'; }

    public function getIsAdminAttribute() { return in_array($this->role, ['admin', 'super_admin']); }
    public function getIsReporterAttribute() { return str_contains($this->role, 'reporter'); }
    public function getIsSubscriberAttribute() { return $this->role == 'subscriber'; }

    // ============================================================
    // ✅ DISPLAY ROLE (हिंदी)
    // ============================================================
    public function getDisplayRoleAttribute()
    {
        $roles = [
            'super_admin' => 'सुपर एडमिन',
            'admin' => 'एडमिन',
            'state_admin' => 'राज्य एडमिन',
            'district_admin' => 'जिला एडमिन',
            'tehsil_admin' => 'तहसील एडमिन',
            'block_admin' => 'ब्लॉक एडमिन',
            'national_reporter' => 'राष्ट्रीय रिपोर्टर',
            'state_reporter' => 'राज्य रिपोर्टर',
            'district_reporter' => 'जिला रिपोर्टर',
            'tehsil_reporter' => 'तहसील रिपोर्टर',
            'block_reporter' => 'ब्लॉक रिपोर्टर',
            'subscriber' => 'सब्सक्राइबर',
        ];
        return $roles[$this->role] ?? $this->role;
    }

    // ============================================================
    // ✅ LOCATION METHODS
    // ============================================================
    public function getLocation()
    {
        $location = [];
        if ($this->assigned_state_id) {
            $state = State::find($this->assigned_state_id);
            if ($state) {
                $location['state'] = $state->display_name ?? $state->name;
                $location['state_id'] = $state->id;
                $location['state_slug'] = $state->slug ?? null;
            }
        }
        if ($this->assigned_district_id) {
            $district = District::find($this->assigned_district_id);
            if ($district) {
                $location['district'] = $district->display_name ?? $district->name;
                $location['district_id'] = $district->id;
                $location['district_slug'] = $district->slug ?? null;
            }
        }
        if ($this->assigned_tehsil_id) {
            $tehsil = Tehsil::find($this->assigned_tehsil_id);
            if ($tehsil) {
                $location['tehsil'] = $tehsil->display_name ?? $tehsil->name;
                $location['tehsil_id'] = $tehsil->id;
                $location['tehsil_slug'] = $tehsil->slug ?? null;
            }
        }
        if ($this->assigned_block_id) {
            $block = Block::find($this->assigned_block_id);
            if ($block) {
                $location['block'] = $block->display_name ?? $block->name;
                $location['block_id'] = $block->id;
                $location['block_slug'] = $block->slug ?? null;
            }
        }
        return $location;
    }

    public function getLocationString()
    {
        $location = $this->getLocation();
        $parts = [];
        if (isset($location['block'])) $parts[] = $location['block'];
        if (isset($location['tehsil'])) $parts[] = $location['tehsil'];
        if (isset($location['district'])) $parts[] = $location['district'];
        if (isset($location['state'])) $parts[] = $location['state'];
        return !empty($parts) ? implode(', ', $parts) : 'N/A';
    }

    public function hasLocation() { return $this->assigned_state_id || $this->assigned_district_id || $this->assigned_tehsil_id || $this->assigned_block_id; }
    public function getLocationLevel() { if ($this->assigned_block_id) return 'block'; if ($this->assigned_tehsil_id) return 'tehsil'; if ($this->assigned_district_id) return 'district'; if ($this->assigned_state_id) return 'state'; return 'national'; }
    public function getLocationId() { if ($this->assigned_block_id) return $this->assigned_block_id; if ($this->assigned_tehsil_id) return $this->assigned_tehsil_id; if ($this->assigned_district_id) return $this->assigned_district_id; if ($this->assigned_state_id) return $this->assigned_state_id; return null; }
    public function getLocationName() { $location = $this->getLocation(); if (isset($location['block'])) return $location['block']; if (isset($location['tehsil'])) return $location['tehsil']; if (isset($location['district'])) return $location['district']; if (isset($location['state'])) return $location['state']; return 'National'; }
    public function getLocationType() { $types = ['block'=>'ब्लॉक','tehsil'=>'तहसील','district'=>'जिला','state'=>'राज्य','national'=>'राष्ट्रीय']; return $types[$this->getLocationLevel()] ?? 'N/A'; }

    // ============================================================
    // ✅ HELPER: GET USER LABEL
    // ============================================================
    public function getLabelAttribute() { return $this->name . ' (' . $this->email . ')'; }

    // ============================================================
    // ✅ SCOPES
    // ============================================================
    public function scopeActive($query) { return $query->where('is_active', 1); }
    public function scopeApproved($query) { return $query->where('is_approved', 1); }
    public function scopeVerified($query) { return $query->where('is_verified', 1); }
    public function scopeReporter($query) { return $query->where('role', 'like', '%reporter%'); }
    public function scopeAdmin($query) { return $query->whereIn('role', ['admin', 'super_admin']); }
    public function scopeSubscriber($query) { return $query->where('role', 'subscriber'); }
}