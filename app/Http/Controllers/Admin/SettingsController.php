<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SiteSetting;
use App\Models\RevenueSetting;
use App\Models\NavigationMenu;
use App\Models\User;
use App\Models\ReporterMonetisation;
use App\Models\NewsView;

class SettingsController extends Controller
{
    // ============ REVENUE SETTINGS ============
    public function revenueSettings()
    {
        $settings = RevenueSetting::first();
        if (!$settings) {
            $settings = RevenueSetting::create([
                'views_per_point' => 100,
                'point_value' => 0.10,
                'min_withdrawal' => 100,
                'max_withdrawal' => 10000,
                'points_per_news' => 10,
                'is_monetisation_active' => false,
                'is_wallet_visible' => false,
                'min_points_for_monetisation' => 100,
                'min_views_for_monetisation' => 1000,
                'min_followers_for_monetisation' => 100,
                'max_views_per_ip_per_day' => 5,
                'point_to_rupee_rate' => 0.10,
            ]);
        }

        $totalReporters = User::whereIn('role', ['reporter', 'state_reporter', 'district_reporter', 'tehsil_reporter', 'block_reporter', 'national_reporter'])->count();
        $activeMonetisation = ReporterMonetisation::where('is_monetisation_active', true)->count();
        $totalPoints = User::sum('points');
        $totalEarnings = ReporterMonetisation::sum('total_earnings');
        $fakeViews = NewsView::where('is_fake', true)->count();

        return view('admin.settings.revenue', compact(
            'settings',
            'totalReporters',
            'activeMonetisation',
            'totalPoints',
            'totalEarnings',
            'fakeViews'
        ));
    }

    public function updateRevenueSettings(Request $request)
    {
        $request->validate([
            'views_per_point' => 'required|integer|min:1',
            'point_value' => 'required|numeric|min:0.01',
            'min_withdrawal' => 'required|numeric|min:10',
            'max_withdrawal' => 'required|numeric|min:100',
            'points_per_news' => 'required|integer|min:1',
            'min_points_for_monetisation' => 'required|integer|min:0',
            'min_views_for_monetisation' => 'required|integer|min:0',
            'min_followers_for_monetisation' => 'required|integer|min:0',
            'max_views_per_ip_per_day' => 'required|integer|min:1',
            'point_to_rupee_rate' => 'required|numeric|min:0.01',
            'is_monetisation_active' => 'nullable|boolean',
            'is_wallet_visible' => 'nullable|boolean',
            'monetisation_terms' => 'nullable|string',
        ]);

        $settings = RevenueSetting::first();
        if (!$settings) {
            $settings = new RevenueSetting();
        }

        $settings->update([
            'views_per_point' => $request->views_per_point,
            'point_value' => $request->point_value,
            'min_withdrawal' => $request->min_withdrawal,
            'max_withdrawal' => $request->max_withdrawal,
            'points_per_news' => $request->points_per_news,
            'min_points_for_monetisation' => $request->min_points_for_monetisation,
            'min_views_for_monetisation' => $request->min_views_for_monetisation,
            'min_followers_for_monetisation' => $request->min_followers_for_monetisation,
            'max_views_per_ip_per_day' => $request->max_views_per_ip_per_day,
            'point_to_rupee_rate' => $request->point_to_rupee_rate,
            'is_monetisation_active' => $request->has('is_monetisation_active'),
            'is_wallet_visible' => $request->has('is_wallet_visible'),
            'monetisation_terms' => $request->monetisation_terms,
        ]);

        return back()->with('success', 'Revenue settings updated successfully!');
    }

    // ============ SITE SETTINGS ============
    public function siteSettings()
    {
        $settings = SiteSetting::pluck('value', 'key')->all();
        return view('admin.settings.site', compact('settings'));
    }

    public function updateSiteSettings(Request $request)
    {
        $request->validate([
            'site_name' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
            'site_logo' => 'nullable|file|mimes:png,jpg,jpeg,webp|max:4096',
            'favicon' => 'nullable|file|mimes:png,ico,jpg,jpeg|max:512',
            'primary_color' => 'nullable|string|max:7',
            'footer_text' => 'nullable|string',
            'facebook_url' => 'nullable|url',
            'twitter_url' => 'nullable|url',
            'instagram_url' => 'nullable|url',
            'youtube_url' => 'nullable|url',
            'contact_email' => 'nullable|email',
            'contact_phone' => 'nullable|string',
        ]);

        // Text Settings
        $textSettings = [
            'site_name' => $request->site_name,
            'meta_description' => $request->meta_description,
            'primary_color' => $request->primary_color,
            'footer_text' => $request->footer_text,
            'facebook_url' => $request->facebook_url,
            'twitter_url' => $request->twitter_url,
            'instagram_url' => $request->instagram_url,
            'youtube_url' => $request->youtube_url,
            'contact_email' => $request->contact_email,
            'contact_phone' => $request->contact_phone,
        ];

        foreach ($textSettings as $key => $value) {
            SiteSetting::updateOrCreate(
                ['key' => $key],
                ['value' => $value, 'type' => 'text', 'group' => 'general']
            );
        }

        // ✅ Logo Upload to public/images/
        if ($request->hasFile('site_logo')) {
            try {
                $file = $request->file('site_logo');
                $filename = 'logo_' . time() . '.' . $file->getClientOriginalExtension();
                $file->move(public_path('images'), $filename);
                
                // Delete old logo if exists
                $oldLogo = SiteSetting::where('key', 'site_logo')->first();
                if ($oldLogo && $oldLogo->value && file_exists(public_path($oldLogo->value))) {
                    unlink(public_path($oldLogo->value));
                }
                
                SiteSetting::updateOrCreate(
                    ['key' => 'site_logo'],
                    ['value' => 'images/' . $filename, 'type' => 'image', 'group' => 'general']
                );
            } catch (\Exception $e) {
                return back()->with('error', 'Logo upload failed: ' . $e->getMessage());
            }
        }

        // ✅ Favicon Upload to public/images/
        if ($request->hasFile('favicon')) {
            try {
                $file = $request->file('favicon');
                $filename = 'favicon_' . time() . '.' . $file->getClientOriginalExtension();
                $file->move(public_path('images'), $filename);
                
                // Delete old favicon if exists
                $oldFavicon = SiteSetting::where('key', 'favicon')->first();
                if ($oldFavicon && $oldFavicon->value && file_exists(public_path($oldFavicon->value))) {
                    unlink(public_path($oldFavicon->value));
                }
                
                SiteSetting::updateOrCreate(
                    ['key' => 'favicon'],
                    ['value' => 'images/' . $filename, 'type' => 'image', 'group' => 'general']
                );
            } catch (\Exception $e) {
                return back()->with('error', 'Favicon upload failed: ' . $e->getMessage());
            }
        }

        return back()->with('success', 'Site settings updated successfully!');
    }

    // ============ NAVIGATION MENU ============
    public function navigationMenu()
    {
        $menus = NavigationMenu::orderBy('order')->get();
        return view('admin.settings.navigation', compact('menus'));
    }

    public function storeNavigationMenu(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'url' => 'required|string|max:255',
            'location' => 'required|in:header,footer,sidebar',
        ]);

        $maxOrder = NavigationMenu::where('location', $request->location)->max('order');
        
        NavigationMenu::create([
            'title' => $request->title,
            'url' => $request->url,
            'icon' => $request->icon,
            'order' => $maxOrder + 1,
            'location' => $request->location,
            'is_active' => true,
        ]);

        return back()->with('success', 'Menu item added successfully!');
    }

    public function updateNavigationMenu(Request $request, NavigationMenu $menu)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'url' => 'required|string|max:255',
            'order' => 'integer',
        ]);

        $isActive = $request->has('is_active') ? true : false;

        $menu->update([
            'title' => $request->title,
            'url' => $request->url,
            'icon' => $request->icon,
            'order' => $request->order ?? $menu->order,
            'is_active' => $isActive,
        ]);

        return back()->with('success', 'Menu item updated successfully!');
    }

    public function deleteNavigationMenu(NavigationMenu $menu)
    {
        $menu->delete();
        return back()->with('success', 'Menu item deleted successfully!');
    }
}