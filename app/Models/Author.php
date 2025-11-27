<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Traits\HasFormattedDate;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
class Author extends Model
{
    /** @use HasFactory<\Database\Factories\AuthorFactory> */
    use HasFactory;
    use HasFormattedDate;

    protected $guarded = ['id'];

    protected $appends = [
        'created_at_formatted',
        'updated_at_formatted',
    ];

    protected function dateOfBirthFormatted(): Attribute
    {
        return Attribute::make(
            get: fn() => $this->date_of_birth
                ? \Carbon\Carbon::parse($this->date_of_birth)->toFormattedDateString()
                : null
        );
    }

    public function posts(): HasMany
    {
        return $this->hasMany(Post::class);
    }
}

