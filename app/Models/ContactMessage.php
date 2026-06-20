<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

final class ContactMessage extends Model
{
    protected $fillable = ['name', 'email', 'message', 'locale', 'ip'];
}
