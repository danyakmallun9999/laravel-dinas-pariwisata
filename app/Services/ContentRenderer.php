<?php

namespace App\Services;

/**
 * Content Renderer Service
 * 
 * Handles dual-format rendering: HTML (legacy TinyMCE) and JSON (Editor.js).
 * All text output is escaped except whitelisted inline HTML in paragraphs.
 */
class ContentRenderer
{
    /**
     * Render content based on format.
     */
    public static function render(?string $content, string $format = 'html'): string
    {
        if (empty($content)) return '';

        return match ($format) {
            'editorjs' => self::renderEditorJs($content),
            default    => ContentSanitizer::sanitizeAllowHtml($content),
        };
    }

    /**
     * Parse Editor.js JSON and render to HTML.
     */
    private static function renderEditorJs(string $json): string
    {
        $data = json_decode($json, true);
        if (!$data || !isset($data['blocks'])) return '';

        $html = '';
        foreach ($data['blocks'] as $block) {
            $html .= self::renderBlock($block);
        }

        return $html;
    }

    /**
     * Render a single Editor.js block to HTML.
     */
    private static function renderBlock(array $block): string
    {
        $type = $block['type'] ?? '';
        $data = $block['data'] ?? [];
        $tunes = $block['tunes'] ?? [];
        $alignment = $tunes['textAlignment']['alignment'] ?? null;

        $rendered = match ($type) {
            'header'    => self::renderHeader($data, $alignment),
            'paragraph' => self::renderParagraph($data, $alignment),
            'list'      => self::renderList($data),
            'image'     => self::renderImage($data),
            'quote'     => self::renderQuote($data),
            'table'     => self::renderTable($data),
            'embed'     => self::renderEmbed($data),
            'delimiter' => '<hr class="my-8 border-gray-200">',
            'code'      => '<pre class="bg-gray-900 text-gray-100 p-4 rounded-xl my-4 overflow-x-auto text-sm"><code>' . e($data['code'] ?? '') . '</code></pre>',
            'warning'   => self::renderWarning($data),
            'checklist' => self::renderChecklist($data),
            'raw'       => self::renderRaw($data),
            default     => '',
        };

        return $rendered;
    }

    private static function renderHeader(array $data, ?string $alignment = null): string
    {
        $level = min(max((int)($data['level'] ?? 2), 1), 6);
        $text = self::sanitizeInlineHtml($data['text'] ?? '');
        $style = $alignment ? ' style="text-align:' . e($alignment) . '"' : '';
        return "<h{$level}{$style}>{$text}</h{$level}>";
    }

    private static function renderParagraph(array $data, ?string $alignment = null): string
    {
        $text = self::sanitizeInlineHtml($data['text'] ?? '');
        $style = $alignment ? ' style="text-align:' . e($alignment) . '"' : '';
        return "<p{$style}>{$text}</p>";
    }

    private static function renderList(array $data): string
    {
        $style = ($data['style'] ?? 'unordered') === 'ordered' ? 'ol' : 'ul';
        $items = $data['items'] ?? [];
        
        $html = "<{$style}>";
        foreach ($items as $item) {
            // Handle nested list format (Editor.js List v2)
            $content = is_array($item) ? ($item['content'] ?? '') : $item;
            $html .= '<li>' . self::sanitizeInlineHtml($content) . '</li>';
        }
        $html .= "</{$style}>";
        
        return $html;
    }

    private static function renderImage(array $data): string
    {
        $url = e($data['file']['url'] ?? ($data['url'] ?? ''));
        if (empty($url)) return '';

        $caption = e($data['caption'] ?? '');
        $stretched = !empty($data['stretched']) ? ' style="width:100%"' : '';
        $withBorder = !empty($data['withBorder']) ? ' border border-gray-200' : '';
        $withBackground = !empty($data['withBackground']) ? ' bg-gray-50 p-4' : '';

        $html = '<figure class="my-6' . $withBackground . '">';
        $html .= '<img src="' . $url . '" alt="' . $caption . '" class="max-w-full h-auto rounded-xl shadow-sm mx-auto' . $withBorder . '"' . $stretched . ' loading="lazy">';
        if ($caption) {
            $html .= '<figcaption class="text-center text-sm text-gray-500 mt-2">' . $caption . '</figcaption>';
        }
        $html .= '</figure>';
        
        return $html;
    }

    private static function renderQuote(array $data): string
    {
        $text = e($data['text'] ?? '');
        $caption = e($data['caption'] ?? '');
        $alignment = ($data['alignment'] ?? 'left') === 'center' ? 'text-center' : '';

        $html = '<blockquote class="border-l-4 border-blue-300 pl-5 italic my-6 text-gray-600 ' . $alignment . '">';
        $html .= '<p>' . $text . '</p>';
        if ($caption) {
            $html .= '<cite class="block mt-2 not-italic text-sm text-gray-500">— ' . $caption . '</cite>';
        }
        $html .= '</blockquote>';
        
        return $html;
    }

    private static function renderTable(array $data): string
    {
        $content = $data['content'] ?? [];
        $withHeadings = !empty($data['withHeadings']);

        if (empty($content)) return '';

        $html = '<div class="overflow-x-auto my-6"><table class="w-full border-collapse">';
        
        foreach ($content as $i => $row) {
            $tag = ($i === 0 && $withHeadings) ? 'th' : 'td';
            $rowClass = ($i === 0 && $withHeadings) ? 'bg-gray-50 font-semibold' : '';
            
            $html .= '<tr class="' . $rowClass . '">';
            foreach ($row as $cell) {
                $html .= '<' . $tag . ' class="border border-gray-200 px-3 py-2 text-sm">' . self::sanitizeInlineHtml($cell) . '</' . $tag . '>';
            }
            $html .= '</tr>';
        }
        
        $html .= '</table></div>';
        return $html;
    }

    private static function renderEmbed(array $data): string
    {
        $service = e($data['service'] ?? '');
        $embed = e($data['embed'] ?? '');
        $caption = e($data['caption'] ?? '');

        if (empty($embed)) return '';

        $html = '<figure class="my-6">';
        $html .= '<div class="relative aspect-video rounded-xl overflow-hidden shadow-sm">';
        $html .= '<iframe src="' . $embed . '" class="absolute inset-0 w-full h-full" frameborder="0" allowfullscreen loading="lazy"></iframe>';
        $html .= '</div>';
        if ($caption) {
            $html .= '<figcaption class="text-center text-sm text-gray-500 mt-2">' . $caption . '</figcaption>';
        }
        $html .= '</figure>';
        
        return $html;
    }

    private static function renderWarning(array $data): string
    {
        $title = e($data['title'] ?? '');
        $message = e($data['message'] ?? '');

        return '<div class="bg-amber-50 border-l-4 border-amber-400 p-4 my-4 rounded-r-xl">'
            . '<p class="font-bold text-amber-800">' . $title . '</p>'
            . '<p class="text-amber-700 text-sm mt-1">' . $message . '</p>'
            . '</div>';
    }

    private static function renderChecklist(array $data): string
    {
        $items = $data['items'] ?? [];
        if (empty($items)) return '';

        $html = '<div class="my-4 space-y-2">';
        foreach ($items as $item) {
            $checked = !empty($item['checked']);
            $text = self::sanitizeInlineHtml($item['text'] ?? '');
            $icon = $checked 
                ? '<svg class="w-4 h-4 text-green-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path></svg>'
                : '<svg class="w-4 h-4 text-gray-300 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><rect x="3" y="3" width="18" height="18" rx="4" stroke-width="2"></rect></svg>';
            $textClass = $checked ? 'line-through text-gray-400' : 'text-gray-700';
            $html .= '<div class="flex items-center gap-2">' . $icon . '<span class="text-sm ' . $textClass . '">' . $text . '</span></div>';
        }
        $html .= '</div>';
        
        return $html;
    }

    /**
     * Render raw HTML block (sanitized).
     */
    private static function renderRaw(array $data): string
    {
        $html = $data['html'] ?? '';
        // Allow safe HTML tags only
        return strip_tags($html, '<p><h1><h2><h3><h4><h5><h6><div><span><a><img><br><hr><ul><ol><li><table><tr><td><th><thead><tbody><blockquote><pre><code><b><i><u><s><del><strong><em><mark><figure><figcaption><iframe>');
    }

    /**
     * Sanitize inline HTML — only allow safe formatting tags.
     * Includes strikethrough (<s>, <del>) and color/style (<span>) tags.
     */
    private static function sanitizeInlineHtml(string $text): string
    {
        return strip_tags($text, '<b><i><a><code><mark><u><br><strong><em><s><del><span><font><sup><sub>');
    }
}
