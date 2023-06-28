<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Article extends Content
{
  protected $table = 'article';

  protected $primaryKey = 'content_id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'title', 'thumbnail',
    ];

    public static function boot()
    {
        parent::boot();

        // All the queries are joined with the content table
        static::addGlobalScope(function ($query) {
            $query->join('content', 'content_id', '=', 'id');
        });
    }

    public function comments() {
      return $this->hasMany(Comment::class, 'article_id');
    }

    public function content() {
      return $this->belongsTo(Content::class);
    }

    public function articleTags() {
      return $this->belongsToMany(Tag::class, 'article_tag', 'article_id', 'tag_id');
    }

    public function getParsedComments() {
      return $this->comments->filter(function ($comment) {
        return $comment->parent_comment_id === null;
      })->sortBy([['likes', 'desc'], ['published_at', 'desc']])
        ->map(function ($comment) {

          $commentInfo = $comment->getInfo();
          $children = $this->comments->filter(function ($comment) use($commentInfo) {
            return $comment->parent_comment_id === $commentInfo['id'];
          })->sortBy([['likes', 'desc'], ['published_at', 'desc']])
            ->map(fn ($comment) => $comment->getInfo());

          $commentInfo['children'] = $children;
          $commentInfo['hasFeedback'] = $commentInfo['hasFeedback'] || !$commentInfo['children']->isEmpty();
          return $commentInfo;
        });
    }
}
