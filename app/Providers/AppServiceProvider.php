<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use App\Models\Category;
use App\Models\State;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // यह कोड हेडर और मोबाइल मेन्यू के लिए कैटेगरीज और राज्यों का डेटा हर पेज पर अपने आप भेज देगा
        View::composer('layouts.app', function ($view) {
            $categories = Category::all();
            $states = State::with('districts.tehsils')->get();
            
            $view->with(compact('categories', 'states'));
        });
    }
}