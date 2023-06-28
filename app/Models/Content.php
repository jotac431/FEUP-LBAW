<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Content extends Model
{
    public $timestamps  = false;

    protected $table = 'content';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'body', 'published_at', 'is_edited'
    ];

    public function author() {
        return $this->belongsTo(User::class);
    }

    public function feedback() {
        return $this->hasMany(Feedback::class);
    }

    // Notifications caused by feedback on this content
    public function feedbackNotifications() {
        return $this->hasMany(FeedbackNotification::class, 'rated_content');
    }
}
