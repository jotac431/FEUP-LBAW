<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tag extends Model
{
    public $timestamps  = false;

    protected $table = 'tag';

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [
        'state',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function areasExpertise()
    {
        return $this->belongsToMany(User::class, 'area_of_expertise')->withPivot('reputation');
    }

    public function favoriteUsers()
    {
        return $this->belongsToMany(User::class, 'favorite_tag');
    }

    public function articleTags()
    {
        return $this->belongsToMany(Article::class, 'article_tag', 'tag_id', 'article_id');
    }

    /**
     * Returns the list of tags in a certain state
     *
     * @return List of tags
     */
    public static function listTagsByState($tag_state)
    {
        return Tag::where('state', $tag_state)
            ->orderBy('name', 'asc')->get();
    }

    /**
     * Checks if a user already has a tag as favorite
     * @return Bool
     */
    public function isFavorite($user_id)
    {
        $favoriteList = $this->favoriteUsers->where('id', $user_id);
        return count($favoriteList) > 0;
    }
}
