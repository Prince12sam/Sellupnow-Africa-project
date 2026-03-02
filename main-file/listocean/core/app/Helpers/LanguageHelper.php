<?php

namespace App\Helpers;


use App\Models\Backend\Language;

class LanguageHelper
{
    private static $language = null;
    private static $default = null;
    private static $user_lang_slug = null;
    private static $default_slug = null;
    private static $user_lang = null;
    private static $all_language = null;

    public function __construct()
    {
        self::lang_instance();
    }

    private static function lang_instance()
    {
        if (self::$language === null) {
            self::$language = new Language();
        }
        return self::$language;
    }

    public static function user_lang()
    {
        if (self::$user_lang === null) {
            try {
                $session_lang = session()->get('lang');
                if (!empty($session_lang) && $session_lang !== self::default_slug()) {
                    self::$user_lang = self::lang_instance()->where('slug', session()->get('lang'))->first();
                } else {
                    self::$user_lang = self::default();
                }
            } catch (\Exception $e) {
                self::$user_lang = null;
            }
        }
        return self::$user_lang;
    }

    public static function default()
    {
        if (self::$default === null) {
            try {
                $default = self::lang_instance()->where('default', '1')->first();
                self::$default = $default;
            } catch (\Exception $e) {
                self::$default = null;
            }
        }
        return self::$default;
    }

    public static function default_slug()
    {
        if (self::$default_slug === null) {
            try {
                $default = self::lang_instance()->where('default', '1')->first();
                self::$default_slug = optional($default)->slug ?? 'en';
            } catch (\Exception $e) {
                self::$default_slug = 'en';
            }
        }
        return self::$default_slug;
    }
    public static function default_dir()
    {
        if (self::$default === null) {
            try {
                $default = self::lang_instance()->where('default', '1')->first();
                self::$default = $default;
            } catch (\Exception $e) {
                self::$default = null;
            }
        }
        return optional(self::$default)->direction ?? 'ltr';
    }
    public static function user_lang_slug(){
        if (self::$user_lang_slug === null) {
            try {
                $default = self::lang_instance()->where('default', '1')->first();
                self::$user_lang_slug = session()->get('lang') ?? optional($default)->slug ?? 'en';
            } catch (\Exception $e) {
                self::$user_lang_slug = session()->get('lang') ?? 'en';
            }
        }
        return self::$user_lang_slug;
    }
    public static function user_lang_dir()
    {
        return optional(self::user_lang())->direction ?? 'ltr';
    }

    public static function all_languages(string $type = 'publish')
    {
        if (self::$all_language === null) {
            try {
                self::$all_language = self::lang_instance()->where(['status' => 'publish'])->get();
            } catch (\Exception $e) {
                self::$all_language = collect();
            }
        }
        return self::$all_language;
    }
}
