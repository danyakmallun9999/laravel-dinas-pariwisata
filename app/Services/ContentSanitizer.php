<?php

namespace App\Services;

/**
 * Content Sanitizer Service
 * 
 * Sanitizes HTML content to prevent XSS attacks.
 * 
 * Installation required:
 * composer require mews/purifier
 * php artisan vendor:publish --provider="Mews\Purifier\PurifierServiceProvider"
 */
class ContentSanitizer
{
    /**
     * Sanitize HTML content using HTMLPurifier.
     * 
     * @param string|null $content
     * @return string
     */
    public static function sanitize(?string $content): string
    {
        if (empty($content)) {
            return '';
        }

        // If HTMLPurifier is installed, use it
        if (class_exists(\Mews\Purifier\Facades\Purifier::class)) {
            return \Mews\Purifier\Facades\Purifier::clean($content);
        }

        // Fallback: Basic HTML escaping (less secure, but better than nothing)
        // NOTE: This is a temporary fallback. Install HTMLPurifier for proper sanitization.
        return htmlspecialchars($content, ENT_QUOTES, 'UTF-8');
    }

    /**
     * Sanitize content but allow safe HTML tags.
     * 
     * @param string|null $content
     * @return string
     */
    public static function sanitizeAllowHtml(?string $content): string
    {
        if (empty($content)) {
            return '';
        }

        // If HTMLPurifier is installed, use it with default config
        if (class_exists(\Mews\Purifier\Facades\Purifier::class)) {
            return \Mews\Purifier\Facades\Purifier::clean($content);
        }

        // Fallback: Strip all HTML tags except safe ones
        $allowedTags = '<p><br><strong><em><u><ol><ul><li><a><h1><h2><h3><h4><h5><h6><img>';
        return strip_tags($content, $allowedTags);
    }
}

