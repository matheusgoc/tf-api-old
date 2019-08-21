<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Category Entity
 *
 * @package App\Models
 */
class Category extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'category_id',
        'name',
        'name_pt',
        'position',
        'active'
    ];

    public $casts = [
        'active' => 'boolean'
    ];

    public function products()
    {
        return $this->belongsToMany(Product::class);
    }
}
