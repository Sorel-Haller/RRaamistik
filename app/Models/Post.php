<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Post extends Model
{
    use HasFactory;

    // Mass assignable fields
    protected $fillable = [
        'title',
        'content',
        'author_id',
        'published',
    ];   

    // Automatically append formatted dates
    protected $appends = [
        'created_at_formatted',
        'updated_at_formatted',
    ];

    // Format created_at
    protected function createdAtFormatted(): Attribute
    {
       return Attribute::make(
            get: fn() => $this->created_at?->diffForHumans()
        );
    }

    // Format updated_at
    protected function updatedAtFormatted(): Attribute
    {
       return Attribute::make(
            get: fn() => $this->updated_at?->diffForHumans()
        );
    }

    // Relationship: Post belongs to an author
    public function author(): BelongsTo
    {
        return $this->belongsTo(Author::class, 'author_id');
    }

    // Relationship: Post has many comments
    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class);
    }
}
