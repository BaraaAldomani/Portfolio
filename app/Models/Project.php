<?php

namespace App\Models;

use App\Models\Concerns\HasLocalizedContent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

final class Project extends Model
{
    use HasLocalizedContent;

    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'highlights_ar' => 'array',
            'highlights_en' => 'array',
            'featured' => 'boolean',
        ];
    }

    public function scopeFeatured(Builder $query): Builder
    {
        return $query->where('featured', true)->orderBy('sort_order');
    }

    /**
     * @return array<int, string>
     */
    public function highlights(): array
    {
        return $this->localizedArray('highlights');
    }

    public function slug(): string
    {
        return $this->{'slug_' . app()->getLocale()};
    }

    /**
     * Short brand name for display type ("سجّه"); falls back to the full title.
     */
    public function name(): string
    {
        return $this->localized('name') ?: $this->localized('title');
    }

    /**
     * Whether a real screenshot exists. Confidential work (government) has
     * none and is shown as typographic dot art instead.
     */
    public function hasCover(): bool
    {
        return filled($this->image_path);
    }

    public function coverImage(): string
    {
        return image_url($this->image_path);
    }

    public function mobileImage(): ?string
    {
        return $this->mobile_image_path ? image_url($this->mobile_image_path) : null;
    }

    /**
     * Route params per locale, for hreflang alternates of the show page.
     *
     * @return array<string, array<string, string>>
     */
    public function slugsByLocale(): array
    {
        return collect(config('app.supported_locales'))
            ->mapWithKeys(fn (string $locale) => [$locale => ['project' => $this->{"slug_{$locale}"}]])
            ->all();
    }
}
