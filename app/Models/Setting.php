<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $fillable = ['key', 'value', 'description'];

    // دالة مساعدة لجلب قيمة إعداد معين
    public static function get(string $key, $default = null)
    {
        $setting = self::where('key', $key)->first();
        return $setting ? $setting->value : $default;
    }

    // دالة مساعدة لتعيين قيمة إعداد معين
    public static function set(string $key, $value, string $description = null)
    {
        return self::updateOrCreate(
            ['key' => $key],
            ['value' => $value, 'description' => $description]
        );
    }
}
