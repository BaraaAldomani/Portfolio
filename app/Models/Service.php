<?php

namespace App\Models;

use App\Models\Concerns\HasLocalizedContent;
use Illuminate\Database\Eloquent\Model;

final class Service extends Model
{
    use HasLocalizedContent;

    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'points_ar' => 'array',
            'points_en' => 'array',
        ];
    }

    /**
     * @return array<int, string>
     */
    public function points(): array
    {
        return $this->localizedArray('points');
    }

    public function imageUrl(): string
    {
        return image_url($this->image_path, "portfolio.images.services.{$this->key}");
    }
}
