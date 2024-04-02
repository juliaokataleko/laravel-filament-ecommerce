<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Item extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function category (): BelongsTo {
        return $this->belongsTo(Category::class);
    }

    public function brand (): BelongsTo {
        return $this->belongsTo(Brand::class);
    }

    public function tax (): BelongsTo {
        return $this->belongsTo(Tax::class);
    }

    public function categories (): BelongsToMany {
        return $this->belongsToMany(Category::class)->withTimestamps();
    }

}
