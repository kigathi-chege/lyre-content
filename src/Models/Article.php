<?php

namespace Lyre\Content\Models;

use App\Models\User;
use App\Models\Interaction;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Lyre\Facet\Concerns\HasFacet;
use Lyre\File\Concerns\HasFile;
use Lyre\File\Http\Resources\File;
use Lyre\Model;

class Article extends Model
{
    use HasFactory, HasFile, HasFacet;

    const ID_COLUMN = 'slug';
    const NAME_COLUMN = 'title';
    const SINGLE_FILE = 'true';
    const ORDER_COLUMN = 'published_at';
    const ORDER_DIRECTION = 'desc';

    protected $with = ['author', 'facetValues', 'interactions'];

    protected array $included = ['read_time', 'featured_image', 'interaction_summary'];

    // TODO: Kigathi - May 18 2025 - Relations should also be exclusible
    protected array $excluded = ['interactions'];

    public function getStatusAttribute()
    {
        return !$this->unpublished && $this->published_at && $this->published_at <= now() ? 'published' : 'unpublished';
    }

    public function scopePublished($query)
    {
        return $query->where('unpublished', '!=', true)->where('published_at', '<=', now());
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    public function interactions(): MorphMany
    {
        return $this->morphMany(Interaction::class, 'entity');
    }

    public function getInteractionSummaryAttribute(): array
    {
        $userId = auth()->id();

        $interactions = $this->interactions;

        // Group by interaction type
        $grouped = $interactions->groupBy(fn($i) => $i->interactionType?->name);

        // Map into structured array
        return \App\Models\InteractionType::where(['status' => 'active'])->get()->map(function ($type) use ($grouped, $userId) {
            $interactions = $grouped->get($type->name, collect());

            return [
                'name' => $type->name,
                'icon' => $type->icon?->content,
                'count' => $interactions->count(),
                'has_interacted' => $userId
                    ? $interactions->contains(fn($i) => $i->user_id == $userId && $i->status == 'published')
                    : false,
            ];
        })->toArray();
    }


    public function getFeaturedImageAttribute()
    {
        $featuredImage = $this->files()->where('mimetype', 'like', 'image/%')->first();

        if ($featuredImage) {
            return File::make($featuredImage);
        }
    }

    public function getReadTimeAttribute()
    {
        $text = strip_tags($this->content);
        $wordCount = str_word_count($text);

        // TODO: Kigathi - April 26 2025 - This should be configurable
        $wordsPerMinute = 200;
        $minutes = ceil($wordCount / $wordsPerMinute);
        return $minutes;
    }
}
