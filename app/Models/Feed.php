<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Feed extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'content'
        ];
        public function user(): BelongsTo
        {
        return $this->belongsTo(User::class);

}
public function likes(): HasMany
{
return $this->hasMany(Like::class);
}
public function comments(): HasMany
{
return $this->hasMany(Comment::class);
}
public function getLikedAttribute(): bool{
return (bool) $this->likes()->where('feed_id' , $this->id)->where('user_id', auth()->id())->exists();
}
}
