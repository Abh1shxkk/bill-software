<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\BelongsToOrganization;

class PageSetting extends Model
{
    use BelongsToOrganization;

    protected $fillable = ['key', 'value', 'group', 'label', 'type'];

    /**
     * Get a setting value by key
     */
    public static function getValue($key, $default = null)
    {
        $setting = self::where('key', $key)->first();
        return $setting ? $setting->value : $default;
    }

    /**
     * Set a setting value by key
     */
    public static function setValue($key, $value)
    {
        return self::where('key', $key)->update(['value' => $value]);
    }

    /**
     * Get all settings by group
     */
    public static function getByGroup($group)
    {
        return self::where('group', $group)->get();
    }

    /**
     * Get all settings as key-value array
     */
    public static function getAllAsArray()
    {
        return self::pluck('value', 'key')->toArray();
    }
}
