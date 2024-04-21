<?php

namespace NormanHuth\Library;

use Illuminate\Support\Str;

class Tailwind
{
    /**
     * The Tailwind CSS classes.
     */
    protected static ?array $classes = null;

    /**
     * Get all Tailwind CSS classes.
     */
    public static function classes(): array
    {
        if (! static::$classes) {
            static::$classes = json_decode(file_get_contents(
                dirname(__FILE__, 2).'/data/tailwind-classes.json'), true
            );
        }

        return static::$classes;
    }

    /**
     * Get the property of a Tailwind CSS class.
     */
    public static function property(string $class): ?string
    {
        return data_get(static::classes(), $class);
    }

    /**
     * Find a Tailwind CSS for a property.
     */
    public static function class(string $property): ?string
    {
        if (str_ends_with($property, ';')) {
            $property = substr($property, 0, -1);
        }

        return data_get(array_flip(static::classes()), $property);
    }

    /**
     * Get the hexadecimal string of a Tailwind CSS color.
     */
    public static function color(string $palette, int $shade): ?string
    {
        $class = data_get(static::classes(), 'text-'.Str::lower($palette).'-'.$shade);

        if (! $class || ! str_contains($class, 'color: rgb')) {
            return null;
        }

        preg_match('/\((.*?)\)/', $class, $matches);

        if (! isset($matches[1])) {
            return null;
        }

        [$r, $g, $b] = sscanf($matches[1], '%d %d %d');

        return sprintf('#%02x%02x%02x', $r, $g, $b);
    }
}
