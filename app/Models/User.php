<?php

namespace App\Models;

use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use Notifiable, HasFactory;

    // Don't add create and update timestamps in database.
    public $timestamps  = false;

    protected $table = 'authenticated_user';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password', 'birth_date', 'country_id',
        'avatar', 'city', 'description', 'google_id',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'remember_token', 'areas_expertise'
    ];

    public function country()
    {
        return $this->belongsTo(Country::class);
    }

    public function followers()
    {
        return $this->belongsToMany(User::class, 'follow', 'followed_id', 'follower_id');
    }

    public function following()
    {
        return $this->belongsToMany(User::class, 'follow', 'follower_id', 'followed_id');
    }

    // verify if foreign key is right (default should be '<Model>_id')
    public function suspensions()
    {
        return $this->hasMany(Suspension::class, 'user_id');
    }

    public function reports()
    {
        return $this->hasMany(Report::class, 'reported_id');
    }

    public function givenReports()
    {
        return $this->hasMany(Report::class, 'reporter_id');
    }

    public function proposedTags()
    {
        return $this->hasMany(Tag::class, 'user_id');
    }

    // Access area of expertise with area->pivot
    public function areasExpertise()
    {
        return $this->belongsToMany(Tag::class, 'area_of_expertise', 'user_id')->withPivot('reputation');
    }

    public function favoriteTags()
    {
        return $this->belongsToMany(Tag::class, 'favorite_tag', 'user_id');
    }

    public function content()
    {
        return $this->hasMany(Content::class, 'author_id');
    }

    public function feedback()
    {
        return $this->hasMany(Feedback::class);
    }

    public function notifications()
    {
        return $this->hasMany(Notification::class, 'receiver_id');
    }

    // Notifications caused by the user's feedback
    public function feedbackNotifications()
    {
        return $this->hasMany(FeedbackNotification::class, 'fb_giver');
    }

    public function articles()
    {
        return Article::where('author_id', $this->id)->get();
    }

    public function comments()
    {
        return Comment::where('author_id', $this->id)->get();
    }

    public function isFollowing($userId)
    {
        $followList = $this->following->where('id', $userId);
        return count($followList) > 0;
    }

    public function topAreasExpertise()
    {
        return $this->areasExpertise->map(function ($area) {
            return [
                'tag_id' => $area->id,
                'tag_name' => $area->name,
                'reputation' => $area->pivot->reputation,
            ];
        })->sortByDesc('reputation')->take(3)->where('reputation', '>', '0');
    }

    // Gets the info on the suspension with the farthest end_time
    public function suspensionEndInfo()
    {
        $suspension = $this->suspensions->sortByDesc('end_time')->first();
        if (!isset($suspension)) return null;

        return [
            'reason' => $suspension->reason,
            'end_date' => gmdate('d-m-Y', strtotime($suspension->end_time)),
        ];
    }
}
