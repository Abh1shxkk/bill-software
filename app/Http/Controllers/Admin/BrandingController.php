<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Organization;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class BrandingController extends Controller
{
    /**
     * Show branding settings
     */
    public function index()
    {
        $organization = auth()->user()->organization;
        
        if (!$organization) {
            return redirect()->route('admin.dashboard')
                ->with('error', 'No organization found.');
        }

        return view('admin.organization.branding', compact('organization'));
    }

    /**
     * Update branding settings
     */
    public function update(Request $request)
    {
        $organization = auth()->user()->organization;

        if (!$organization) {
            return back()->with('error', 'No organization found.');
        }

        // Only organization owner can update branding
        if (!auth()->user()->is_organization_owner && !auth()->user()->isAdmin()) {
            return back()->with('error', 'Only organization owner can update branding.');
        }

        $validated = $request->validate([
            // Colors
            'primary_color' => 'nullable|string|max:7|regex:/^#[A-Fa-f0-9]{6}$/',
            'secondary_color' => 'nullable|string|max:7|regex:/^#[A-Fa-f0-9]{6}$/',
            'accent_color' => 'nullable|string|max:7|regex:/^#[A-Fa-f0-9]{6}$/',
            'sidebar_color' => 'nullable|string|max:7|regex:/^#[A-Fa-f0-9]{6}$/',
            'header_color' => 'nullable|string|max:7|regex:/^#[A-Fa-f0-9]{6}$/',
            
            // Text
            'app_name' => 'nullable|string|max:100',
            'tagline' => 'nullable|string|max:255',
            'footer_text' => 'nullable|string|max:500',
            
            // Invoice
            'invoice_header_html' => 'nullable|string|max:2000',
            'invoice_footer_html' => 'nullable|string|max:2000',
            'invoice_terms' => 'nullable|string|max:2000',
            
            // Custom CSS
            'custom_css' => 'nullable|string|max:5000',
            
            // Options
            'show_powered_by' => 'boolean',
            
            // Files
            'favicon' => 'nullable|file|mimes:ico,png|max:256',
            'login_background' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        // Handle favicon upload
        if ($request->hasFile('favicon')) {
            if ($organization->favicon_path) {
                Storage::disk('public')->delete($organization->favicon_path);
            }
            $validated['favicon_path'] = $request->file('favicon')
                ->store('organizations/branding', 'public');
        }
        unset($validated['favicon']);

        // Handle login background upload
        if ($request->hasFile('login_background')) {
            if ($organization->login_background_path) {
                Storage::disk('public')->delete($organization->login_background_path);
            }
            $validated['login_background_path'] = $request->file('login_background')
                ->store('organizations/branding', 'public');
        }
        unset($validated['login_background']);

        // Set defaults for empty colors
        $validated['primary_color'] = $validated['primary_color'] ?? '#6366f1';
        $validated['secondary_color'] = $validated['secondary_color'] ?? '#8b5cf6';
        $validated['accent_color'] = $validated['accent_color'] ?? '#10b981';
        $validated['sidebar_color'] = $validated['sidebar_color'] ?? '#1e293b';
        $validated['header_color'] = $validated['header_color'] ?? '#0f172a';
        $validated['show_powered_by'] = $validated['show_powered_by'] ?? true;

        $organization->update($validated);

        return back()->with('success', 'Branding settings updated successfully.');
    }

    /**
     * Reset branding to defaults
     */
    public function reset()
    {
        $organization = auth()->user()->organization;

        if (!$organization) {
            return back()->with('error', 'No organization found.');
        }

        // Delete uploaded files
        if ($organization->favicon_path) {
            Storage::disk('public')->delete($organization->favicon_path);
        }
        if ($organization->login_background_path) {
            Storage::disk('public')->delete($organization->login_background_path);
        }

        $organization->update([
            'primary_color' => '#6366f1',
            'secondary_color' => '#8b5cf6',
            'accent_color' => '#10b981',
            'sidebar_color' => '#1e293b',
            'header_color' => '#0f172a',
            'favicon_path' => null,
            'login_background_path' => null,
            'app_name' => null,
            'tagline' => null,
            'footer_text' => null,
            'invoice_header_html' => null,
            'invoice_footer_html' => null,
            'invoice_terms' => null,
            'custom_css' => null,
            'show_powered_by' => true,
        ]);

        return back()->with('success', 'Branding reset to defaults.');
    }

    /**
     * Preview branding
     */
    public function preview(Request $request)
    {
        // Return a preview of the branding
        $colors = [
            'primary' => $request->primary_color ?? '#6366f1',
            'secondary' => $request->secondary_color ?? '#8b5cf6',
            'accent' => $request->accent_color ?? '#10b981',
            'sidebar' => $request->sidebar_color ?? '#1e293b',
            'header' => $request->header_color ?? '#0f172a',
        ];

        return response()->json(['colors' => $colors]);
    }
}
