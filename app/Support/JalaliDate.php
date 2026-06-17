<?php

namespace App\Support;

use Ariaieboy\Jalali\Jalali;
use Carbon\Carbon;

class JalaliDate
{
    public static function toStorage(?string $state): ?string
    {
        if (blank($state)) {
            return null;
        }

        // Normalize incoming state to a Gregorian datetime string to
        // prevent timezone/day shifts when the JS picker parses values.
        if (self::isJalaliFormat($state)) {
            return Jalali::fromFormat('Y/m/d', $state)->toCarbon()->format('Y-m-d H:i:s');
        }

        return Carbon::parse($state)->format('Y-m-d H:i:s');
    }

    public static function forPicker(?string $state): ?string
    {
        if (blank($state)) {
            return null;
        }

        // Return a full Gregorian datetime string for the picker so the
        // frontend JS can parse it without inferring incorrect timezone
        // offsets.
        if (self::isJalaliFormat($state)) {
            return Jalali::fromFormat('Y/m/d', $state)->toCarbon()->format('Y-m-d H:i:s');
        }

        return Carbon::parse($state)->format('Y-m-d H:i:s');
    }

    public static function forDisplay(?string $state): ?string
    {
        if (blank($state)) {
            return null;
        }

        if (self::isJalaliFormat($state)) {
            return $state;
        }

        return Jalali::fromCarbon(Carbon::parse($state))->format('Y/m/d');
    }

    public static function todayForPicker(): string
    {
        return Jalali::now()->toCarbon()->format('Y-m-d H:i:s');
    }

    private static function isJalaliFormat(string $state): bool
    {
        return (bool) preg_match('/^\d{4}\/\d{1,2}\/\d{1,2}/', $state);
    }
}
