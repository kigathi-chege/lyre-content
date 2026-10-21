<?php

namespace Lyre\Content\Console\Commands;

use Illuminate\Console\Command;
use Lyre\Content\Models\Article;
use Lyre\Content\Support\ArticleHtmlSanitizer;

class SanitizeArticles extends Command
{
    protected $signature = 'lyre-content:sanitize-articles {--dry-run : Report what would change without writing}';

    protected $description = 'Clean the HTML of articles saved before sanitizing on save was added';

    public function handle(): int
    {
        $changed = 0;
        $checked = 0;

        Article::query()->withoutGlobalScopes()->select(['id', 'slug', 'content'])->chunkById(200, function ($articles) use (&$changed, &$checked) {
            foreach ($articles as $article) {
                $checked++;
                $clean = ArticleHtmlSanitizer::clean($article->content);
                if ($clean === $article->content) {
                    continue;
                }

                $changed++;
                $this->line("  {$article->slug}");
                if (!$this->option('dry-run')) {
                    // Raw update: no model events, no updated_at bump.
                    Article::query()->whereKey($article->id)->toBase()->update(['content' => $clean]);
                }
            }
        });

        $verb = $this->option('dry-run') ? 'would change' : 'changed';
        $this->info("Checked {$checked} articles; {$verb} {$changed}.");

        return self::SUCCESS;
    }
}
