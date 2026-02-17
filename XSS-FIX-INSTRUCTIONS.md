# XSS Fix Instructions - HTMLPurifier Installation

## Status
✅ ContentSanitizer service sudah dibuat  
✅ Views sudah diupdate untuk menggunakan sanitization  
⚠️ **HTMLPurifier package belum diinstall**

## Installation Steps

### 1. Install HTMLPurifier Package
```bash
composer require mews/purifier
```

### 2. Publish Configuration
```bash
php artisan vendor:publish --provider="Mews\Purifier\PurifierServiceProvider"
```

### 3. Configure HTMLPurifier (config/purifier.php)

Update configuration untuk allow safe HTML tags yang digunakan oleh TinyMCE:

```php
'HTML.Allowed' => 'p,br,strong,em,u,ol,ul,li,a[href|target|rel],img[src|alt|width|height],h1,h2,h3,h4,h5,h6,blockquote,pre,code,table,thead,tbody,tr,th,td',
'HTML.SafeIframe' => true,
'URI.SafeIframeRegexp' => '%^(https?:)?//(www\.youtube(?:-nocookie)?\.com/embed/|player\.vimeo\.com/video/)%',
```

### 4. Verify Installation

Setelah install, `ContentSanitizer` akan otomatis menggunakan HTMLPurifier. Test dengan:

```php
// In tinker or test
$content = '<script>alert("XSS")</script><p>Safe content</p>';
$sanitized = \App\Services\ContentSanitizer::sanitizeAllowHtml($content);
// Should output: <p>Safe content</p> (script tag removed)
```

## Files Updated

1. ✅ `app/Services/ContentSanitizer.php` - Service untuk sanitization
2. ✅ `resources/views/public/posts/show.blade.php` - Updated untuk sanitize content
3. ✅ `resources/views/public/events/show.blade.php` - Updated untuk sanitize content

## Current Behavior

**Before HTMLPurifier installation:**
- ContentSanitizer akan menggunakan fallback: `strip_tags()` dengan allowed tags
- Ini lebih aman dari `{!! !!}` tanpa sanitization, tapi kurang optimal

**After HTMLPurifier installation:**
- ContentSanitizer akan menggunakan HTMLPurifier dengan full sanitization
- XSS attacks akan di-block secara comprehensive
- Safe HTML tags dari TinyMCE tetap dipertahankan

## Testing

Setelah install, test dengan input berbahaya:
1. Login sebagai admin
2. Create/edit post dengan content: `<script>alert('XSS')</script><p>Test</p>`
3. View post di public area
4. Verify: Script tag harus dihapus, hanya `<p>Test</p>` yang muncul

