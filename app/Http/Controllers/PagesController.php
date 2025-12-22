<?php

namespace App\Http\Controllers;

use App\Models\PageSetting;
use Illuminate\Http\Request;

class PagesController extends Controller
{
    /**
     * Get all page settings as key-value array
     */
    private function getSettings()
    {
        return PageSetting::getAllAsArray();
    }

    public function privacy()
    {
        $settings = $this->getSettings();
        return view('pages.privacy', compact('settings'));
    }

    public function terms()
    {
        $settings = $this->getSettings();
        return view('pages.terms', compact('settings'));
    }

    public function support()
    {
        $settings = $this->getSettings();
        return view('pages.support', compact('settings'));
    }

    public function documentation()
    {
        $settings = $this->getSettings();
        return view('pages.documentation', compact('settings'));
    }
}
