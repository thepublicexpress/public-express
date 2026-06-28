<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Problem extends Model
{
    protected $fillable = [
        'name', 'phone', 'district_id', 'title', 'description', 'image', 'status',
    ];

    public function district()
    {
        return $this->belongsTo(District::class);
    }
}
