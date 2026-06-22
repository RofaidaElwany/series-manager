<?php

namespace Helpers;

if (! defined('ABSPATH')) {
    exit;
}

class SeriesStyleHelper
{
    /**
     * @return array<string, string>
     */
    public static function getForTerm(int $termId): array
    {
        $settingsService = new \Service\SeriesSettingsService();
        $settings = $settingsService->getSettings($termId);
        $style = $settings['style'] ?? [];

        return is_array($style) ? $style : [];
    }

    public static function sanitizeColor(?string $color): string
    {
        if ($color === null || $color === '') {
            return '';
        }

        $hex = sanitize_hex_color($color);
        if ($hex) {
            return $hex;
        }

        if (preg_match('/^rgba?\(\s*[\d.%\s,]+\s*\)$/i', $color)) {
            return $color;
        }

        $css_value = self::resolveStyleCSSValue($color);
        if (str_starts_with($css_value, 'var(--')) {
            return $css_value;
        }

        return '';
    }

    public static function colorStyle(string $property, ?string $color): string
    {
        $sanitized = self::sanitizeColor($color);

        if ($sanitized === '') {
            return '';
        }

        return esc_attr($property) . ':' . esc_attr($sanitized);
    }

    public static function withOpacity(?string $color, float $opacity = 0.2): ?string
    {
        $color = self::sanitizeColor($color);

        if ($color === '') {
            return null;
        }

        if (preg_match('/^rgba?\(/i', $color)) {
            return $color;
        }

        $hex = ltrim($color, '#');

        if (strlen($hex) === 3) {
            $hex = $hex[0] . $hex[0] . $hex[1] . $hex[1] . $hex[2] . $hex[2];
        }

        if (strlen($hex) !== 6 || ! ctype_xdigit($hex)) {
            return null;
        }

        $r = hexdec(substr($hex, 0, 2));
        $g = hexdec(substr($hex, 2, 2));
        $b = hexdec(substr($hex, 4, 2));

        return sprintf('rgba(%d,%d,%d,%s)', $r, $g, $b, $opacity);
    }

    public static function inlineStyle(array $rules): string
    {
        $parts = [];

        foreach ($rules as $property => $value) {
            if ($value === null || $value === '') {
                continue;
            }

            if (str_contains((string) $property, 'color') || $property === 'border-color') {
                $rule = self::colorStyle((string) $property, (string) $value);
            } else {
                $sanitized = self::sanitizeCssValue((string) $value);

                if ($sanitized === '') {
                    continue;
                }

                $rule = esc_attr((string) $property) . ':' . esc_attr($sanitized);
            }

            if ($rule !== '') {
                $parts[] = $rule;
            }
        }

        if ($parts === []) {
            return '';
        }

        return ' style="' . esc_attr(implode(';', $parts)) . '"';
    }

    public static function sanitizeCssValue(?string $value): string
    {
        return self::resolveStyleCSSValue($value);
    }

    /**
     * @param string|null $value
     */
    public static function resolveStyleCSSValue($value): string
    {
        if ($value === null || $value === '') {
            return '';
        }

        $value = trim((string) $value);

        if ($value === '0') {
            return '0';
        }

        if (preg_match('/^var:preset\|spacing\|([a-z0-9-]+)$/i', $value, $matches)) {
            return 'var(--wp--preset--spacing--' . sanitize_key($matches[1]) . ')';
        }

        if (preg_match('/^var:preset\|color\|([a-z0-9-]+)$/i', $value, $matches)) {
            return 'var(--wp--preset--color--' . sanitize_key($matches[1]) . ')';
        }

        if (str_starts_with($value, 'var(--')) {
            return preg_match('/^var\(--[\w-]+\)$/', $value) ? $value : '';
        }

        if (preg_match('/^(\d+(\.\d+)?)(px|rem|em|%|vh|vw)$/i', $value)) {
            return $value;
        }

        if (preg_match('/^\d+$/', $value)) {
            return $value . 'px';
        }

        return '';
    }

    /**
     * @param mixed $value
     */
    private static function isSpacingValueSet($value): bool
    {
        return $value !== null && $value !== '';
    }

    /**
     * @param array<string, mixed>|null $spacing
     * @return array<string, string>
     */
    public static function spacingRules(?array $spacing, string $propertyPrefix): array
    {
        if (! is_array($spacing)) {
            return [];
        }

        $rules = [];

        if (self::isSpacingValueSet($spacing['horizontal'] ?? null)) {
            $value = self::resolveStyleCSSValue((string) $spacing['horizontal']);

            if ($value !== '') {
                $rules["{$propertyPrefix}-left"] = $value;
                $rules["{$propertyPrefix}-right"] = $value;
            }
        }

        if (self::isSpacingValueSet($spacing['vertical'] ?? null)) {
            $value = self::resolveStyleCSSValue((string) $spacing['vertical']);

            if ($value !== '') {
                $rules["{$propertyPrefix}-top"] = $value;
                $rules["{$propertyPrefix}-bottom"] = $value;
            }
        }

        foreach (['top', 'right', 'bottom', 'left'] as $side) {
            if (! self::isSpacingValueSet($spacing[$side] ?? null)) {
                continue;
            }

            $value = self::resolveStyleCSSValue((string) $spacing[$side]);

            if ($value !== '') {
                $rules["{$propertyPrefix}-{$side}"] = $value;
            }
        }

        return $rules;
    }

    /**
     * @param array<string, mixed>|null $border
     * @return array<string, string>
     */
    public static function borderRules(?array $border): array
    {
        if (! is_array($border)) {
            return [];
        }

        $rules = [];
        $allowed_styles = ['solid', 'dashed', 'dotted', 'double', 'none'];

        if (self::isSpacingValueSet($border['width'] ?? null)) {
            $width = self::resolveStyleCSSValue((string) $border['width']);

            if ($width !== '') {
                $rules['border-width'] = $width;
            }
        }

        if (self::isSpacingValueSet($border['color'] ?? null)) {
            $color = self::sanitizeColor((string) $border['color']);

            if ($color !== '') {
                $rules['border-color'] = $color;
            }
        }

        if (! empty($border['style']) && in_array($border['style'], $allowed_styles, true)) {
            $rules['border-style'] = $border['style'];
        }

        if ($rules !== [] && ! isset($rules['border-style'])) {
            $rules['border-style'] = 'solid';
        }

        return $rules;
    }

    /**
     * Default shell spacing applied when the user has not overridden a side.
     *
     * @return array<string, string>
     */
    public static function shellDefaults(): array
    {
        return [
            'padding-top' => '5rem',
            'padding-bottom' => '5rem',
            'padding-left' => '1.5rem',
            'padding-right' => '1.5rem',
            'margin-bottom' => '5rem',
        ];
    }

    /**
     * @param array<string, mixed> $style
     * @param array<string, string> $defaults
     */
    public static function layoutContainerStyle(array $style, array $defaults = []): string
    {
        if ($defaults === []) {
            $defaults = self::shellDefaults();
        }

        $rules = array_merge(
            $defaults,
            self::spacingRules($style['padding'] ?? null, 'padding'),
            self::spacingRules($style['margin'] ?? null, 'margin'),
            self::borderRules($style['border'] ?? null)
        );

        return self::inlineStyle($rules);
    }

    public static function layoutContainerStyleCustomOnly(array $style): string
    {
        $rules = array_merge(
            self::spacingRules($style['padding'] ?? null, 'padding'),
            self::spacingRules($style['margin'] ?? null, 'margin'),
            self::borderRules($style['border'] ?? null)
        );

        return self::inlineStyle($rules);
    }

    public static function ksesWithStyles(string $html): string
    {
        $allowed = wp_kses_allowed_html('post');

        foreach (['div', 'h2', 'h3', 'h4', 'a', 'span', 'p', 'details', 'section'] as $tag) {
            if (! isset($allowed[$tag])) {
                $allowed[$tag] = [];
            }

            $allowed[$tag]['style'] = true;
            $allowed[$tag]['class'] = true;
        }

        return wp_kses($html, $allowed);
    }
}
