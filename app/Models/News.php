<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\State;
use App\Models\District;
use App\Models\Tehsil;
use App\Models\Block;
use App\Models\Category;
use App\Models\User;
use Illuminate\Support\Str;

class News extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'category_id',
        'state_id',
        'district_id',
        'tehsil_id',
        'block_id',
        'title',
        'slug',
        'summary',
        'body',
        'featured_image',
        'alt_text',
        'seo_description',
        'type',
        'video_url',
        'is_breaking',
        'is_featured',
        'status',           // pending, published, rejected, draft
        'approval_status',  // pending, approved, rejected
        'approved_by',
        'approved_at',
        'rejected_at',
        'rejection_reason',
        'approval_level',   // block, tehsil, district, state, admin
        'published_at',
        'views',
        'meta_title',
        'meta_description',
        'meta_keywords',
        'canonical_url',
    ];

    protected $casts = [
        'is_breaking' => 'boolean',
        'is_featured' => 'boolean',
        'published_at' => 'datetime',
        'approved_at' => 'datetime',
        'rejected_at' => 'datetime',
        'views' => 'integer',
    ];

    // ============================================================
    // ✅ Slug Mutator (SEO-friendly slugs)
    // ============================================================
    public function setSlugAttribute($value)
    {
        // Strip all non-ASCII characters (keep only A-Z, a-z, 0-9, and hyphen)
        $clean = preg_replace('/[^A-Za-z0-9-]+/', '-', $value);
        $this->attributes['slug'] = strtolower(trim($clean, '-'));
    }

    // ============================================================
    // ✅ BOOT METHOD - Auto-generate SEO fields
    // ============================================================
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($news) {
            // Auto-generate alt_text if not provided
            if (empty($news->alt_text) && !empty($news->featured_image)) {
                $news->alt_text = self::generateAltText($news->title);
            }
            
            // Auto-generate SEO description if not provided
            if (empty($news->seo_description) && !empty($news->summary)) {
                $news->seo_description = self::generateSeoDescription($news->summary);
            } elseif (empty($news->seo_description) && !empty($news->body)) {
                $news->seo_description = self::generateSeoDescription($news->body);
            }
            
            // Auto-generate meta_title if not provided
            if (empty($news->meta_title)) {
                $news->meta_title = self::generateMetaTitle($news->title);
            }
            
            // Auto-generate meta_description if not provided
            if (empty($news->meta_description) && !empty($news->summary)) {
                $news->meta_description = self::generateMetaDescription($news->summary);
            } elseif (empty($news->meta_description) && !empty($news->body)) {
                $news->meta_description = self::generateMetaDescription($news->body);
            }
            
            // Auto-generate meta_keywords if not provided
            if (empty($news->meta_keywords)) {
                $news->meta_keywords = self::generateMetaKeywords($news->title, $news->category_id);
            }
        });

        static::updating(function ($news) {
            // Update alt_text if featured_image changed and alt_text is empty
            if ($news->isDirty('featured_image') && empty($news->alt_text) && !empty($news->featured_image)) {
                $news->alt_text = self::generateAltText($news->title);
            }
            
            // Update SEO description if summary changed
            if ($news->isDirty('summary') && !empty($news->summary)) {
                $news->seo_description = self::generateSeoDescription($news->summary);
                $news->meta_description = self::generateMetaDescription($news->summary);
            }
            
            // Update meta_title if title changed
            if ($news->isDirty('title') && !empty($news->title)) {
                $news->meta_title = self::generateMetaTitle($news->title);
                $news->meta_keywords = self::generateMetaKeywords($news->title, $news->category_id);
            }
        });
    }

    // ============================================================
    // ✅ SEO GENERATORS
    // ============================================================

    /**
     * Generate ALT text for featured image
     */
    public static function generateAltText($title, $imageNumber = 1)
    {
        $siteName = self::getSiteName();
        $alt = strip_tags($title);
        $alt = Str::limit($alt, 90);
        
        if ($imageNumber > 1) {
            $alt .= ' - चित्र ' . $imageNumber;
        }
        
        $alt .= ' | ' . $siteName;
        return $alt;
    }

    /**
     * Generate SEO Description from summary or body
     */
    public static function generateSeoDescription($text)
    {
        $clean = strip_tags($text);
        $clean = preg_replace('/\s+/', ' ', $clean);
        return Str::limit($clean, 160);
    }

    /**
     * Generate Meta Title
     */
    public static function generateMetaTitle($title)
    {
        $siteName = self::getSiteName();
        $title = strip_tags($title);
        
        if (strlen($title) > 60) {
            $title = Str::limit($title, 55);
        }
        
        return $title . ' - ' . $siteName;
    }

    /**
     * Generate Meta Description
     */
    public static function generateMetaDescription($text)
    {
        $clean = strip_tags($text);
        $clean = preg_replace('/\s+/', ' ', $clean);
        return Str::limit($clean, 160);
    }

    /**
     * Generate Meta Keywords
     */
    public static function generateMetaKeywords($title, $categoryId = null)
    {
        $keywords = [];
        
        // Extract from title
        $titleWords = preg_split('/\s+/', strip_tags($title), -1, PREG_SPLIT_NO_EMPTY);
        $keywords = array_merge($keywords, $titleWords);
        
        // Add category
        if ($categoryId) {
            $category = Category::find($categoryId);
            if ($category) {
                $keywords[] = $category->display_name ?? $category->name;
            }
        }
        
        // Add default keywords
        $keywords[] = 'हिंदी खबरें';
        $keywords[] = 'ताज़ा खबरें';
        $keywords[] = self::getSiteName();
        
        // Remove duplicates and limit
        $keywords = array_unique($keywords);
        $keywords = array_slice($keywords, 0, 10);
        
        return implode(', ', $keywords);
    }

    /**
     * Get site name
     */
    protected static function getSiteName()
    {
        try {
            return \App\Models\SiteSetting::get('site_name', 'द पब्लिक एक्सप्रेस');
        } catch (\Exception $e) {
            return 'द पब्लिक एक्सप्रेस';
        }
    }

    // ============================================================
    // ✅ ACCESSORS - SEO Meta
    // ============================================================

    public function getMetaTitleAttribute($value)
    {
        if (!empty($value)) {
            return $value;
        }
        return self::generateMetaTitle($this->title);
    }

    public function getMetaDescriptionAttribute($value)
    {
        if (!empty($value)) {
            return $value;
        }
        if (!empty($this->summary)) {
            return self::generateMetaDescription($this->summary);
        }
        if (!empty($this->body)) {
            return self::generateMetaDescription($this->body);
        }
        return self::generateMetaDescription($this->title);
    }

    public function getMetaKeywordsAttribute($value)
    {
        if (!empty($value)) {
            return $value;
        }
        return self::generateMetaKeywords($this->title, $this->category_id);
    }

    public function getAltTextAttribute($value)
    {
        if (!empty($value)) {
            return $value;
        }
        if (!empty($this->featured_image)) {
            return self::generateAltText($this->title);
        }
        return null;
    }

    public function getCanonicalUrlAttribute($value)
    {
        if (!empty($value)) {
            return $value;
        }
        try {
            return route('news.show', $this->slug);
        } catch (\Exception $e) {
            return url('/news/' . $this->slug);
        }
    }

    // ============================================================
    // ✅ RELATIONSHIPS
    // ============================================================
    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function state()
    {
        return $this->belongsTo(State::class);
    }

    public function district()
    {
        return $this->belongsTo(District::class);
    }

    public function tehsil()
    {
        return $this->belongsTo(Tehsil::class);
    }

    public function block()
    {
        return $this->belongsTo(Block::class);
    }

    // ============================================================
    // ✅ SCOPES
    // ============================================================
    public function scopePublished($query)
    {
        return $query->where('status', 'published');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeRejected($query)
    {
        return $query->where('status', 'rejected');
    }

    public function scopeDraft($query)
    {
        return $query->where('status', 'draft');
    }

    public function scopeWithLocation($query, $stateId = null, $districtId = null, $tehsilId = null, $blockId = null)
    {
        if ($blockId) {
            return $query->where('block_id', $blockId);
        }
        if ($tehsilId) {
            return $query->where('tehsil_id', $tehsilId);
        }
        if ($districtId) {
            return $query->where('district_id', $districtId);
        }
        if ($stateId) {
            return $query->where('state_id', $stateId);
        }
        return $query;
    }

    // ============================================================
    // ✅ ACCESSORS
    // ============================================================
    public function getStatusLabelAttribute()
    {
        $labels = [
            'pending' => '⏳ समीक्षा के लिए',
            'published' => '✅ प्रकाशित',
            'rejected' => '❌ अस्वीकृत',
            'draft' => '📝 ड्राफ्ट',
        ];
        return $labels[$this->status] ?? $this->status;
    }

    public function getApprovalStatusLabelAttribute()
    {
        $labels = [
            'pending' => '⏳ लंबित',
            'approved' => '✅ स्वीकृत',
            'rejected' => '❌ अस्वीकृत',
        ];
        return $labels[$this->approval_status] ?? $this->approval_status;
    }

    public function getApprovalLevelLabelAttribute()
    {
        $labels = [
            'block' => 'ब्लॉक',
            'tehsil' => 'तहसील',
            'district' => 'जिला',
            'state' => 'राज्य',
            'admin' => 'एडमिन',
        ];
        return $labels[$this->approval_level] ?? $this->approval_level;
    }

    // ============================================================
    // ✅ LOCATION LABEL
    // ============================================================
    public function getLocationLabel()
    {
        $parts = [];

        if ($this->state_id) {
            $state = State::find($this->state_id);
            if ($state) $parts[] = $state->name;
        }
        if ($this->district_id) {
            $district = District::find($this->district_id);
            if ($district) $parts[] = $district->name;
        }
        if ($this->tehsil_id) {
            $tehsil = Tehsil::find($this->tehsil_id);
            if ($tehsil) $parts[] = $tehsil->name;
        }
        if ($this->block_id) {
            $block = Block::find($this->block_id);
            if ($block) $parts[] = $block->name;
        }

        if (!empty($parts)) {
            return implode(', ', $parts);
        }

        if ($this->user) {
            return $this->user->getLocationString();
        }

        return 'N/A';
    }

    // ============================================================
    // ✅ SEO DATA FOR JSON-LD
    // ============================================================
    public function getSeoData()
    {
        return [
            'title' => $this->meta_title,
            'description' => $this->meta_description,
            'keywords' => $this->meta_keywords,
            'canonical' => $this->canonical_url,
            'image' => $this->featured_image ? asset($this->featured_image) : asset('images/logo.png'),
            'alt' => $this->alt_text,
            'published_time' => $this->published_at ?? $this->created_at,
            'modified_time' => $this->updated_at,
            'author' => $this->user->name ?? 'द पब्लिक एक्सप्रेस',
            'category' => $this->category->display_name ?? $this->category->name ?? null,
        ];
    }

    // ============================================================
    // ✅ INCREMENT VIEWS
    // ============================================================
    public function incrementViews()
    {
        $this->increment('views');
        return $this;
    }

    // ============================================================
    // ✅ GET RELATED NEWS
    // ============================================================
    public function getRelatedNews($limit = 5)
    {
        $query = self::published()
            ->where('id', '!=', $this->id)
            ->where('category_id', $this->category_id)
            ->latest('published_at');
        
        // Try to get from same location
        $locationQuery = clone $query;
        if ($this->block_id) {
            $locationQuery->where('block_id', $this->block_id);
        } elseif ($this->tehsil_id) {
            $locationQuery->where('tehsil_id', $this->tehsil_id);
        } elseif ($this->district_id) {
            $locationQuery->where('district_id', $this->district_id);
        } elseif ($this->state_id) {
            $locationQuery->where('state_id', $this->state_id);
        }
        
        $locationNews = $locationQuery->limit($limit)->get();
        
        if ($locationNews->count() >= $limit) {
            return $locationNews;
        }
        
        // If not enough, get from same category
        $categoryNews = $query->limit($limit)->get();
        return $categoryNews;
    }
}