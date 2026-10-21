<?php

namespace Lyre\Content\Support;

use Symfony\Component\HtmlSanitizer\HtmlSanitizer;
use Symfony\Component\HtmlSanitizer\HtmlSanitizerConfig;

/**
 * Cleans article HTML before it is stored, so script, event handlers and
 * javascript: URLs never reach a page that renders it raw.
 *
 * Builds on Filament's rich-editor config (classes, inline styles, data-*
 * attributes) and adds what Tiptap task lists, tables and AI-formatted
 * articles produce.
 */
class ArticleHtmlSanitizer
{
    private static ?HtmlSanitizer $sanitizer = null;

    public static function clean(?string $html): ?string
    {
        if ($html === null || trim($html) === '') {
            return $html;
        }

        return self::sanitizer()->sanitize($html);
    }

    private static function sanitizer(): HtmlSanitizer
    {
        return self::$sanitizer ??= new HtmlSanitizer(self::config());
    }

    private static function config(): HtmlSanitizerConfig
    {
        $base = app()->bound(HtmlSanitizerConfig::class)
            ? app(HtmlSanitizerConfig::class)
            : (new HtmlSanitizerConfig)
                ->allowSafeElements()
                ->allowRelativeLinks()
                ->allowRelativeMedias()
                ->allowAttribute('class', '*')
                ->allowAttribute('style', '*')
                ->allowAttribute('data-type', '*')
                ->allowAttribute('data-color', '*');

        return $base
            ->allowLinkSchemes(['http', 'https', 'mailto', 'tel'])
            ->allowMediaSchemes(['http', 'https'])
            ->allowAttribute('data-checked', ['li', 'ul'])
            ->allowAttribute('colspan', ['td', 'th'])
            ->allowAttribute('rowspan', ['td', 'th'])
            ->allowAttribute('colwidth', ['td', 'th'])
            ->allowAttribute('data-colwidth', ['td', 'th'])
            ->allowAttribute('start', 'ol')
            ->allowAttribute('alt', 'img')
            ->allowAttribute('title', '*')
            ->allowAttribute('target', 'a')
            ->forceAttribute('a', 'rel', 'noopener noreferrer nofollow')
            ->withMaxInputLength(2_000_000);
    }
}
