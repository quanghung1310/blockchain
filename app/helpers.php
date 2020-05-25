<?php

use Carbon\Carbon;

if (! function_exists('storage_url')) {
    /**
     * Get url of path.
     *
     * @param string|null $path
     * @param string|null $disk
     * @return string|null
     */
    function storage_url($path, $disk = null)
    {
        if($path == null || empty($path)) {
            return null;
        }
        return Storage::disk($disk)->url($path);
    }
}

if (! function_exists('normalize_version')) {

    function normalize_version($version)
    {
        $segments = explode('.', $version);
        return sprintf('%2d.%2d.%2d', $segments[0] ?? 0, $segments[1] ?? 0, $segments[2] ?? 0);
    }
}

if (! function_exists('compare_app_version')) {

    function compare_app_version($version1, $version2)
    {
        return strcmp(normalize_version($version1), normalize_version($version2));
    }
}

if (! function_exists('parse_datetime_with_app_timezone')) {

    /**
     * @param string|null $datetime
     * @return Carbon
     */
    function parse_datetime_with_app_timezone($datetime)
    {
        return Carbon::parse($datetime)->setTimezone(env('APP_TIMEZONE'));
    }
}

if ( ! function_exists('config_path'))
{
    /**
     * Get the configuration path.
     *
     * @param  string $path
     * @return string
     */
    function config_path($path = '')
    {
        return app()->basePath() . '/config' . ($path ? '/' . $path : $path);
    }
}
