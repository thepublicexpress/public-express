<?php

namespace App\Http\Controllers;

use App\Models\NewsletterSubscriber;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class NewsletterController extends Controller
{
    /**
     * Subscribe to Newsletter – Supports both AJAX and Normal POST
     */
    public function subscribe(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|max:255|unique:newsletter_subscribers,email',
            'name' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors()
                ], 422);
            }

            return redirect()->back()
                ->with('newsletter_error', $validator->errors()->first())
                ->withInput();
        }

        try {
            $subscriber = NewsletterSubscriber::create([
                'email' => $request->email,
                'name' => $request->name,
                'ip_address' => $request->ip(),
                'is_active' => 1,
                'created_at' => now(),
            ]);

            \Log::info('📧 New Newsletter Subscriber', ['email' => $request->email]);

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => '✅ आप सफलतापूर्वक सब्सक्राइब हो गए!',
                ]);
            }

            return redirect()->back()
                ->with('newsletter_success', '✅ आप सफलतापूर्वक सब्सक्राइब हो गए!');

        } catch (\Exception $e) {
            \Log::error('❌ Newsletter Subscribe Error: ' . $e->getMessage());

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'कुछ गड़बड़ हो गई। कृपया पुनः प्रयास करें।'
                ], 500);
            }

            return redirect()->back()
                ->with('newsletter_error', 'कुछ गड़बड़ हो गई। कृपया पुनः प्रयास करें।');
        }
    }

    /**
     * Admin: List all subscribers
     */
    public function index()
    {
        $subscribers = NewsletterSubscriber::orderBy('created_at', 'desc')->paginate(20);
        return view('admin.newsletter.index', compact('subscribers'));
    }

    /**
     * Admin: Delete a subscriber
     */
    public function destroy($id)
    {
        $subscriber = NewsletterSubscriber::findOrFail($id);
        $subscriber->delete();

        return redirect()->back()
            ->with('success', '✅ सब्सक्राइबर हटा दिया गया!');
    }

    /**
     * Admin: Toggle Active/Inactive
     */
    public function toggle($id)
    {
        $subscriber = NewsletterSubscriber::findOrFail($id);
        $subscriber->update(['is_active' => !$subscriber->is_active]);

        return redirect()->back()
            ->with('success', '✅ सब्सक्राइबर स्थिति बदल दी गई!');
    }
}