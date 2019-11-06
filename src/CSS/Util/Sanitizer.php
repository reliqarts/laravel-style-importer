<?php

declare(strict_types=1);

namespace ReliqArts\StyleImporter\CSS\Util;

final class Sanitizer
{
    /**
     * @param string $styles
     *
     * @return string
     *
     * @see https://www.regextester.com/94246 Adapted from
     */
    public static function sanitizeStyles(string $styles): string
    {
        $replacements = [
            '/{/' => ' {',
            '/\/\*[\s\S]*?\*\/|([^:]|^)\/\/.*$/' => '',
        ];

        return preg_replace(
            array_keys($replacements),
            array_values($replacements),
            sprintf("\n%s", $styles)
        );
    }

    /**
     * @param string $elementName
     *
     * @return string
     */
    public static function sanitizeElementName(string $elementName): string
    {
        return str_replace('.', '\.', str_replace('#', '\#', $elementName));
    }
}
