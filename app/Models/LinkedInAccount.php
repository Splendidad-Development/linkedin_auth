<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LinkedInAccount extends Model
{
    protected $table = 'linkedin_accounts';

    protected $fillable = [
        'user_id',
        'linkedin_id',
        'access_token',
        'expires_at',
        'first_name',
        'last_name',
        'email',
        'profile_picture',
        'last_post_hash',
        'last_posted_at',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'last_posted_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function isExpired(): bool
    {
        return $this->expires_at->isPast();
    }
}
