<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PageSetting;
use Illuminate\Http\Request;

class PageSettingController extends Controller
{
    /**
     * Display the page settings form
     */
    public function index()
    {
        $generalSettings = PageSetting::getByGroup('general');
        $privacySettings = PageSetting::getByGroup('privacy');
        $termsSettings = PageSetting::getByGroup('terms');
        $supportSettings = PageSetting::getByGroup('support');
        $faqSettings = PageSetting::getByGroup('faq');

        return view('admin.settings.page-settings', compact(
            'generalSettings',
            'privacySettings',
            'termsSettings',
            'supportSettings',
            'faqSettings'
        ));
    }

    /**
     * Update page settings
     */
    public function update(Request $request)
    {
        $settings = $request->input('settings', []);

        foreach ($settings as $key => $value) {
            PageSetting::where('key', $key)->update(['value' => $value]);
        }

        return redirect()->route('admin.page-settings.index')
            ->with('success', 'Page settings updated successfully!');
    }
}
