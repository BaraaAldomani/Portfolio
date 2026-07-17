<?php

namespace App\Models;

use App\Models\Concerns\HasLocalizedContent;
use Illuminate\Database\Eloquent\Model;

final class Technology extends Model
{
    use HasLocalizedContent;

    protected $guarded = [];
}
