<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Review extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'product_id',
        'customer_name',
        'rating',
        'comment',
    ];   

   protected $appends = [
    'created_at_formatted',
    'updated_at_formatted',
];
    

    protected function createdAtFormatted(): Attribute
    {
       return Attribute::make(
            get: fn() => $this->created_at?->diffForHumans()
        );
    }

    protected function updatedAtFormatted(): Attribute
    {
       return Attribute::make(
            get: fn() => $this->updated_at?->diffForHumans()
        );
    }   

        public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
    
}
