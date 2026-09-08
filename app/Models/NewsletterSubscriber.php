<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NewsletterSubscriber extends Model
{
    protected $table = 'newsletter_subscribers';
    
    protected $fillable = [
        'email', 'name', 'ip_address', 'is_active'
    ];
    
    public $timestamps = false; // हम created_at manually डाल रहे हैं
}