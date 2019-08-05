<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Image extends Model
{
    const RATIO = '4/4';
    const DETAIL_WIDTH = 1024;
    const DETAIL_HEIGHT = 1024;
    const LIST_WIDTH = 256;
    const LIST_HEIGHT = 256;
    const THUMB_WIDTH = 128;
    const THUMB_HEIGHT = 128;

    protected $fillable = [
        'head',
        'active'
    ];

    protected $casts = [
        'head' => 'boolean',
        'active' => 'boolean'
    ];
}
